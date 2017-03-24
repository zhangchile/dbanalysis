<?php

spl_autoload_register(function ($class) {
    
    list($dir, $class) = explode('\\', $class);
        $class_file =  $dir . '/'. $class . '.php';
        if (file_exists($class_file)) {
            require($class_file);
        } else {
            throw new \Exception('class '. $class_file . ' not exists');
        }
});