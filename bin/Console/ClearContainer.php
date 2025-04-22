<?php

require 'vendor/autoload.php';
use App\Container\Container;

$container = new Container();
$container->clear();
echo "Container cache cleared.\n";