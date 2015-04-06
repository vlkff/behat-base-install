<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 4/5/15
 * Time: 8:54 PM
 */

trait AuthHelpers {

    /**
     *
     * @Given /^I do basic auth on behat as demo$/
     */
    public function iDoBasicAuthOnBehatAsDemo()
    {


        $username = $this->userNameDemo;
        $password = $this->passWordDemo;

        $this->getSession()->setBasicAuth($username, $password);
        $this->client->setDefaultOption('auth', [$username, $password]);
    }

    /**
     *
     * @Given /^I do basic auth "([^"]*)" "([^"]*)"$/
     * @Given /^I do basic auth$/
     * @Given /^I do basic auth on behat$/
     */
    public function iDoBasicAuth($username = false, $password = false)
    {
        if($username == false)
        {
            $username = $this->userName;
            $password = $this->passWord;
        }
        $this->getSession()->setBasicAuth($username, $password);
        $this->client->setDefaultOption('auth', [$username, $password]);
    }

    /**
     * @hidden This will set authentication in a token
     *
     * @Then /^I set basic auth to username "([^"]*)" and password "([^"]*)"$/
     */
    public function iSetBasicAuthToUsernameAndPassword($username, $password)
    {
        $username = $this->fixStepArgument($username);
        $password = $this->fixStepArgument($password);

        $this->getSession()->setBasicAuth($username, $password);
    }


    /**
     *
     * @Given /^I scroll to top of page$/
     */
    public function iScrollToTopOfPage() {
        $function = <<<JS
            (function(){
              window.scrollTo(0,0);
            })()
JS;
        try {
            $this->getSession()->executeScript($function);
        }
        catch(Exception $e) {
            throw new \Exception("ScrollIntoView failed");
        }
    }

    /**
     * Y would be the way to go up and down the page. A good default for X is 0
     *
     * @Given /^I scroll to x "([^"]*)" y "([^"]*)" coordinates of page$/
     */
    public function iScrollToXYCoordinatesOfPage($arg1, $arg2) {
        $function = <<<JS
            (function(){
              window.scrollTo($arg1, $arg2);
            })()
JS;
        try {
            $this->getSession()->executeScript($function);
        }
        catch(Exception $e) {
            throw new \Exception("ScrollIntoView failed");
        }
    }


    /** end from orig file */

    /**
     * @Given /^there are (\d+) rows of "([^"]*)"$/
     */
    public function thereAreRowsOf($arg1, $arg2)
    {

        $count = $arg2::all()->count();
        if($count != $arg1) {
            throw new Exception(
                "Actual count is:\n" . $count
            );
        }
    }


    /**
     * @Then /^I log in as demo$/
     */
    public function iLogInAsDemo()
    {
        $this->fillField('email', $this->userNameDemo);
        $this->fillField('password', $this->passWordDemo);
        $this->getSession()->getPage()->find('css', '#login-submit')->press();
    }

    /**
     * @Then /^I log in$/
     */
    public function iLogIn()
    {
        $this->fillField('email', $this->userName);
        $this->fillField('password', $this->passWord);
        $this->getSession()->getPage()->find('css', '#login-submit')->press();
    }



}