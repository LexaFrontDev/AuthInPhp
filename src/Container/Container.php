<?php

namespace App\Container;

use ReflectionClass;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Container
{
    private $services = [];

    public function registerClassesInDirectory(string $directory)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
    
        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }
    
            $class = $this->getClassFromFile($file);
            if ($class === \App\Router\Route::class) {
                continue;
            }
    
            if ($class) {
                $this->register($class);
            }
        }
    }
    

    private function getClassFromFile($file)
    {
        $filePath = $file->getRealPath();
        $className = $this->getClassNameFromFile($filePath);

        return $className;
    }

    private function getClassNameFromFile($filePath)
    {
        $contents = file_get_contents($filePath);
        if (preg_match('/namespace\s+([^;]+);/', $contents, $matches)) {
            $namespace = $matches[1];
            $class = basename($filePath, '.php');
            return $namespace . '\\' . $class;
        }
        return null;
    }

    public function register(string $class)
    {
        if (!isset($this->services[$class])) {
            $this->services[$class] = $this->create($class);
        }
    }

    public function create(string $class)
    {
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor) {
            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $type = $parameter->getType();
                if ($type && !$type->isBuiltin()) {
                    $dependencies[] = $this->get($type->getName());
                }
            }

            return $reflectionClass->newInstanceArgs($dependencies);
        }

        return new $class();
    }

    public function get(string $class)
    {
        if (!isset($this->services[$class])) {
            $this->register($class);
        }

        return $this->services[$class];
    }

    public function clear()
    {
        $this->services = [];
    }
}
