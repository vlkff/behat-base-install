<?php

require_once __DIR__ . '/bootstrap.php';

use Symfony\Component\Filesystem\Filesystem;

try
{
    $filesystem = new Filesystem();
    if($filesystem->exists('.env'))
    {
        echo sprintf("Env file exists already %s", __DIR__ . '/../.env');
    } else {
        echo sprintf("Copied .env file too root");
        exec("cp env.example .env");
    }
}
catch(\Exception $e)
{
    throw new \Exception(sprintf("Error setting up Env file %s", $e->getMessage()));
}