<?php

try
{
    exec("cp setup/example_foo_bar.behat.inc tests/acceptance/custom/");
}
catch(\Exception $e)
{
    throw new \Exception(sprintf("Error setting up Env file %s", $e->getMessage()));
}