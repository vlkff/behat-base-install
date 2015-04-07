<?php

require_once __DIR__ . '/bootstrap.php';

use Symfony\Component\Filesystem\Filesystem;

if(!$base = getenv('TEST_ROOT'))
    $base = __DIR__ . '/../';

$file = new Filesystem();

$prepare = ['tests'];

foreach($prepare as $folder)
{
    try
    {
        if(!$file->exists($base . $folder))
        {
            $file->mkdir($base . $folder);
            echo sprintf("Making folder %s \n", $base . $folder);
        }
    }
    catch (\Exception $e)
    {
        throw new \Exception(sprintf("Error tyring to make the tests folder %s with message %s", $folder, $e->getMessage()));
    }
}


try
{
    $options['override'] = false;
    $folders = ['acceptance', 'factories'];
    foreach($folders as $folder)
    {
        $file->mirror(__DIR__ . '/stubs/' . $folder, $base . '/tests/' . $folder, null, $options);
        echo sprintf("Mirrored folder %s to %s\n", __DIR__ . '/stubs/' . $folder, $base . '/tests/' . $folder);
    }
}
catch (\Exception $e)
{
    throw new \Exception(sprintf("Error copying over acceptance files %s", $e->getMessage()));
}



