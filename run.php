<?php
use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$dotEnv = new Dotenv(__DIR__);
$dotEnv->load();

$application = new Application();
$application->add(new \Volantus\MspBroker\Src\CLI\BrokerCommand());
$application->run();