<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;



// Using the CLI factory default services container
$di = new CliDI();



/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();

$loader->registerDirs(
    [
        __DIR__ . "/tasks",
        require_once __DIR__ . '/vendor/autoload.php',
    ]
);

$loader->register();



// Load the configuration file (if any)

$configFile = __DIR__ . "/config/config.php";

if (is_readable($configFile)) {
    $config = include $configFile;

    $di->set("config", $config);
}



// Create a console application
$console = new ConsoleApp();

$console->setDI($di);



/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments["task"] = $arg;
    } elseif ($k === 2) {
        $arguments["action"] = $arg;
    } elseif ($k >= 3) {
        $arguments["params"][] = $arg;
    }
}



try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();

    exit(255);
}