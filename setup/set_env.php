<?php

require_once __DIR__ . '/bootstrap.php';

use Symfony\Component\Filesystem\Filesystem;

try
{
<<<<<<< HEAD
    $filesystem = new Filesystem();
    if($filesystem->exists('.env'))
    {
        echo sprintf("Env file exists already %s", __DIR__ . '/../.env');
    } else {
        echo sprintf("Copied .env file too root");
=======
    if(!file_exists(__DIR__.'/../.env'))
    {
>>>>>>> 1505bfb119affd1e7d4769d3bac2885d047d54d5
        exec("cp env.example .env");
    }
}
catch(\Exception $e)
{
    throw new \Exception(sprintf("Error setting up Env file %s", $e->getMessage()));
}