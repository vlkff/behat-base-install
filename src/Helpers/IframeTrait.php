<?php namespace AlfredNutileInc\BehatBaseInstaller\Helpers;
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 4/5/15
 * Time: 8:52 PM
 */

trait IframeTrait {


    /**
     * Sets an iFrame ID to no_name_iframe if there is no ID so you can then add a Switch to iFrame step after it using the na_name_iframe ID. You must use an ID but do not pass the #
     *
     * @Given /^I set the iframe located in element with an id of "([^"]*)"$/
     */
    public function iSetTheIframeLocatedInElementWithAnIdOf($element_id) {
        $element_id = $this->fixStepArgument($element_id);
        $check = 1; //@todo need to check using js if exists
        if($check <= 0) {
            throw new \Exception('Element not found');
        } else {
            $javascript = <<<JS
            (function(){
              var elem = document.getElementById('$element_id');
              var iframes = elem.getElementsByTagName('iframe');
              var f = iframes[0];
              f.id = "no_name_iframe";
            })()
JS;
            $this->getSession()->executeScript($javascript);
        }
    }

    /**
     * And I switch to iframe with name "name here"
     *
     * @Given /^I switch to iframe with name "([^"]*)"$/
     * @Given /^I wait switch to iframe named "([^"]*)"$/
     */
    public function iSwitchToiFrameWithName($name) {
        $name = $this->fixStepArgument($name);
        $this->getSession()->switchToIFrame($name);
    }


    /**
     * @hidden
     *
     * @When /^I press the xpath "([^"]*)"$/
     */
    public function iPressTheXpath($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $node = $this->getSession()->getPage()->find('xpath', $arg);
        if($node) {
            $this->getSession()->getPage()->find('xpath', $arg)->press();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * Check that there are more than or = to a number of elements on a page
     *
     * @Then /^I should see more "([^"]*)" or more "([^"]*)" elements$/
     */
    public function iShouldSeeMoreOrMoreElements($num, $element)
    {

        $container = $this->getSession()->getPage();
        $nodes = $container->findAll('css', $element);

        if (intval($num) > count($nodes)) {
            $message = sprintf('Number of elements not found on page');
            throw new ExpectationException($message, $this->session);
        }
    }

    /**
     * @hidden
     *
     * @When /^I press the xpath "([^"]*)" and switch to popup$/
     */
    public function iPressTheXpathAndSwitchToPopup($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $this->setMainWindow();
        $this->iPressTheXpath($arg);
        $popupName = $this->getNewPopup($this->originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }


    /**
     * @hidden
     *
     * @When /^I click the xpath "([^"]*)"$/
     */
    public function iClickTheXpath($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $node = $this->getSession()->getPage()->find('xpath', $arg);
        if($node) {
            $this->getSession()->getPage()->find('xpath', $arg)->click();
        } else {
            throw new Exception('Element not found');
        }
    }



    /**
     * @hidden
     *
     * @When /^I click the xpath "([^"]*)" click on the alert and switch to popup$/
     */
    public function iClickTheXpathClickOnTheAlertAndSwitchToPopup($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $this->setMainWindow();
        $this->iClickTheXpath($arg);
        $this->iClickOnTheAlertWindow();
        $popupName = $this->getNewPopup($this->originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * @hidden
     *
     * @When /^I click the xpath "([^"]*)" and switch to popup$/
     */
    public function iClickTheXpathAndSwitchToPopup($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $this->setMainWindow();
        $this->iClickTheXpath($arg);
        $popupName = $this->getNewPopup($this->originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * @hidden
     *
     * @When /^I follow the xpath "([^"]*)"$/
     */
    public function iFollowTheXpath($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $node = $this->getSession()->getPage()->find('xpath', $arg);
        if($node) {
            $this->getSession()->getPage()->find('xpath', $arg)->click();
        } else {
            throw new Exception('Element not found');
        }
    }

}