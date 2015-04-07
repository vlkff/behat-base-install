<?php

namespace BehatBaseInstaller\Tests;

use Symfony\Component\Finder\Finder;

class BehatBaseInstallerTest extends \PHPUnit_Framework_TestCase{


    protected $path;

    public function setUp()
    {
        $this->path = __DIR__ . '/../../../../tests/';
    }
    
    /**
     * @test
     */
    public function feature_files_exist()
    {

        $features = [
            'example_api_testing.feature',
            'example_custom_step.feature',
            'example_env_file.feature',
            'example_faker.feature',
            'example_mockery.feature',
            'example_oauth.feature',
            'example_token.feature',
            'example_ui.feature',
        ];

        foreach($features as $feature)
        {
            $this->assertFileExists($this->path . 'acceptance/features/' . $feature);
        }
    }

    /**
     * @test
     */
    public function bootstrap_exists()
    {

        $files = [
            'BaseContext.php',
            'BaseDrupalContext.php',
            'FeatureContext.php',
        ];

        foreach($files as $file)
        {
            $this->assertFileExists($this->path . 'acceptance/bootstrap/' . $file);
        }
    }
}