<?php

namespace App\Router;

use ReflectionClass;
use ReflectionMethod;
use RecursiveIteratorIterator; 
use RecursiveDirectoryIterator; 

class RouteDispatcher
{
    public function dispatch(string $url, string $httpMethod): void
    {
        $classes = $this->getClassesFromNamespace('App\Controller');
        
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
            
            foreach ($methods as $method) {
                $attributes = $method->getAttributes(Route::class);
                foreach ($attributes as $attribute) {
                    $route = $attribute->newInstance();
                    if ($route->url === $url && $route->method === strtoupper($httpMethod)) {
                        $controller = $this->getController($class);
                        $controller->{$method->getName()}();
                        return;
                    }
                }
            }
        }
    
        echo "404 Not Found";
    }
    

    
    private function getController($class)
    {
        global $container;
        return $container->get($class);
    }
    

    private function getClassesFromNamespace($namespace)
    {
        $classes = [];
        $dir = __DIR__ . '/../Controller'; 
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = $this->getClassFromFile($file);
            if ($className && strpos($className, $namespace) === 0) {
                $classes[] = $className;
            }
        }

        return $classes;
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
}
