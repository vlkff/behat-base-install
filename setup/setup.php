<?php

$root = __DIR__;

$files = ['set_env', 'tests_folder'];

foreach($files as $file)
{
    try
    {
        exec("php {$root}/{$file}.php", $output, $results);
        if($results > 0)
        {
            throw new \Exception(sprintf("Error executing setup file %s with message %s", $file, implode("\n", $output)));
        }
        echo implode("\n", $output) . "\n";
    }
    catch(\Exception $e)
    {
        throw new \Exception(sprintf("Error executing setup file %s with message %s", $file, $e->getMessage()));
    }
}