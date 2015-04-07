<?php namespace AlfredNutileInc\BehatBaseInstaller\Helpers;
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 4/5/15
 * Time: 8:48 PM
 */

trait WindowTraits {


    /**
     * @hidden
     *
     * @Then /^I switch to popup by clicking "([^"]*)" and clicking alert$/
     */
    public function iSwitchToPopupByClickingAndClickingAlert($arg1) {
        $arg1 = $this->fixStepArgument($arg1);
        $originalWindowName = $this->getSession()->getWindowName(); //Get the original name
        $this->setMainWindow();

        $this->getSession()->getPage()->clickLink("$arg1");
        $this->iClickOnTheAlertWindow();

        $popupName = $this->getNewPopup($originalWindowName);

        //Switch to the popup Window
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * @hidden
     *
     * @Then /^I switch to popup by clicking "([^"]*)"$/
     */
    public function iSwitchToPopupByClicking($arg1) {
        $arg1 = $this->fixStepArgument($arg1);
        $this->setMainWindow();
        $this->getSession()->getPage()->clickLink("$arg1"); //Pressing the withdraw button

        $popupName = $this->getNewPopup($this->originalWindowName);

        //Switch to the popup Window
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * @hidden
     */
    protected function setMainWindow() {
        $originalWindowName = $this->getSession()->getWindowName();
        if (empty($this->originalWindowName)) {
            $this->originalWindowName = $originalWindowName;
        }
    }

    /**
     * @hidden
     *
     * @Then /^I switch to popup by pressing "([^"]*)"$/
     */
    public function iSwitchToPopupByPressing($arg1) {
        $arg1 = $this->fixStepArgument($arg1);
        $originalWindowName = $this->getSession()->getWindowName(); //Get the original name
        if (empty($this->originalWindowName)) {
            $this->originalWindowName = $originalWindowName;
        }

        $this->getSession()->getPage()->pressButton("$arg1"); //Pressing the withdraw button

        $popupName = $this->getNewPopup($originalWindowName);

        //Switch to the popup Window
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * @hidden
     *
     * @Given /^I switch to popup by pressing the xpath "([^"]*)"$/
     */
    public function iSwitchToPopupByPressingTheXpath($arg1)
    {
        $arg1 = $this->fixStepArgument($arg1);
        $this->GetOriginalWindowName();
        $originalWindowName = $this->originalWindowName;

        if($this->getSession()->getPage()->find('xpath', $arg1)) {
            $button = $this->getSession()->getPage()->find('xpath', $arg1);
            $button->press();
        } else {
            throw new Exception('Element not found');
        }

        $popupName = $this->getNewPopup($originalWindowName);

        //Switch to the popup Window
        $this->getSession()->switchToWindow($popupName);

    }

    /**
     * @hidden
     */
    protected function GetOriginalWindowName()
    {
        $originalWindowName = $this->getSession()->getWindowName(); //Get the original name
        if (empty($this->originalWindowName)) {
            $this->originalWindowName = $originalWindowName;
        }
    }

    /**
     * @hidden
     *
     * @Then /^I switch to popup by clicking link "([^"]*)"$/
     */
    public function iSwitchToPopupByClickingLink($arg1) {
        $arg1 = $this->fixStepArgument($arg1);
        $originalWindowName = $this->getSession()->getWindowName(); //Get the original name
        if (empty($this->originalWindowName)) {
            $this->originalWindowName = $originalWindowName;
        }

        $this->getSession()->getPage()->clickLink($arg1); //Pressing the withdraw button

        $popupName = $this->getNewPopup($originalWindowName);

        //Switch to the popup Window
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * Allows you to switch back to a window after switching to a popup
     *
     * @Then /^I switch back to original window$/
     * @Then /^I switch back original window$/
     */
    public function iSwitchBackToOriginalWindow() {
        //Switch to the original window
        $this->getSession()->switchToWindow($this->originalWindowName);
    }

    /**
     * @hidden
     *
     * @Given /^I switch to previous window$/
     */
    public function iSwitchPreviousToWindow()
    {
        $this->getSession()->switchToWindow();
    }

    /**
     * @hidden
     *
     * This gets the window name of the new popup.
     */
    private function getNewPopup($originalWindowName = NULL) {
        $originalWindowName = $this->fixStepArgument($originalWindowName);
        //Get all of the window names first
        $names = $this->getSession()->getWindowNames();

        //Now it should be the last window name
        $last = array_pop($names);

        if (!empty($originalWindowName)) {
            while ($last == $originalWindowName && !empty($names)) {
                $last = array_pop($names);
            }
        }

        return $last;
    }






}