<?php

try
{
    if(!file_exists(__DIR__.'/../.env'))
    {
        exec("cp env.example .env");
    }
}
catch(\Exception $e)
{
    throw new \Exception(sprintf("Error setting up Env file %s", $e->getMessage()));
}