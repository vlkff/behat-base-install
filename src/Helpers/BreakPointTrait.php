<?php namespace AlfredNutileInc\BehatBaseInstaller\Helpers;

trait BreakPointTrait {


    /**
     * @Given /^the custom size is "([^"]*)" by "([^"]*)"$/
     */
    public function theCustomSizeIs($width, $height)
    {
        $this->getSession()->resizeWindow($width, $height, 'current');
    }

    /**
     * @Given /^the size is desktop/
     */
    public function theSizeIsDesktop()
    {
        $this->getSession()->resizeWindow(1400, 900, 'current');
    }

    /**
     * @Given /^the size is tablet landscape/
     */
    public function theSizeIsTabletLandscape()
    {
        $this->getSession()->resizeWindow(1024, 900, 'current');
    }

    /**
     * @Given /^the size is tablet portrait/
     */
    public function theSizeIsTabletPortrait()
    {
        $this->getSession()->resizeWindow(768, 900, 'current');
    }

    /**
     * @Given /^the size is mobile landscape/
     */
    public function theSizeIsMobileLandscape()
    {
        $this->getSession()->resizeWindow(640, 900, 'current');
    }

    /**
     * @Given /^the size is mobile portrait/
     */
    public function theSizeIsMobilePortrait()
    {
        $this->getSession()->resizeWindow(320, 900, 'current');
    }

}