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
    public function assets_folder_exists()
    {
        $this->assertFileExists($this->path . 'acceptance/assets/.gitkeep');
        $this->assertFileExists($this->path . 'factories/.gitkeep');
    }

    /**
     * @test
     */
    public function custom_files_exists()
    {
        $files = ['example_foo_bar.behat.inc'];

        foreach($files as $file)
        {
            $this->assertFileExists($this->path . 'acceptance/custom/' . $file);
        }
    }

    /**
     * @test
     */
    public function bootstrap_exists()
    {

        $files = [
            'FeatureContext.php',
            'BaseDrupalContext.php',
        ];

        foreach($files as $file)
        {
            $this->assertFileExists($this->path . 'acceptance/bootstrap/' . $file);
        }
    }
}