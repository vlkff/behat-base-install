<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 3/23/15
 * Time: 5:54 AM
 */

trait SeedHelper {


    /**
     * You will need to make this work for your framework
     * @Given /^I reseed the database$/
     */
    public function iReseedTheDatabase()
    {
        $env = getenv('APP_ENV');

        if(getenv('APP_ENV') != 'production')
        {
            try
            {
                if(getenv('APP_ENV') == 'acceptance' || getenv('APP_ENV') == 'testing')
                {
                    exec("php artisan migrate:refresh --seed -n --env=$env", $output, $return_value);
                } else {
                    exec("php artisan migrate:refresh --seed -n --env=local", $output, $return_value);
                    if($return_value == true)
                    {
                        throw new \Exception(sprintf("Error with migration %s", implode("\n", $output)));
                    }
                }
            }
            catch(\Exception $e)
            {
                throw new \Exception(sprintf("Error seeding the database %s", $e->getMessage()));
            }
        } else {
            throw new \Exception(sprintf("You can not seed production"));
        }
    }
    
}