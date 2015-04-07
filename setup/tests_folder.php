<?php

require_once __DIR__ . '/bootstrap.php';

use Symfony\Component\Filesystem\Filesystem;

$base = __DIR__ . '/../';

$file = new Filesystem();

try
{
    if(!$file->exists($base . 'tests'))
    {
        $file->mkdir($base . 'tests');
    }
}
catch (\Exception $e)
{
    throw new \Exception(sprintf("Error tyring to make the tests folder %s", $e->getMessage()));
}

try
{
    $file->copy(__DIR__ . '/stubs/acceptance', $base . '/tests/acceptance');
}
catch (\Exception $e)
{
    throw new \Exception(sprintf("Error copying over acceptance files %s", $e->getMessage()));
}



