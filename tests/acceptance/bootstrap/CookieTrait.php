<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 4/5/15
 * Time: 8:49 PM
 */

trait CookieTrait {


    /**
     * Look for a cookie and value
     *
     * @Then /^I should see cookie "([^"]*)" with value "([^"]*)"$/
     */
    public function iShouldSeeCookieWithValue($cookie_name, $value) {
        $cookie_name = $this->fixStepArgument($cookie_name);
        $value       = $this->fixStepArgument($value);
        if($cookie = $this->getSession()->getCookie($cookie_name)) {
            if($cookie != $value)
                throw new Exception(sprintf("Cookie %s found but value was %s", $cookie_name, $cookie));
        } else {
            throw new Exception(sprintf("Cookie %s not found", $cookie_name));
        }
    }

    /**
     * Look for a cookie
     *
     * @Then /^I should see cookie "([^"]*)"$/
     */
    public function iShouldSeeCookie($cookie_name) {
        $cookie_name = $this->fixStepArgument($cookie_name);
        if($this->getSession()->getCookie($cookie_name)) {
            return TRUE;
        } else {
            throw new Exception('Cookie not found');
        }
    }

    /**
     * @Then /^I set cookie "([^"]*)" with value "([^"]*)"$/
     */
    public function iSetCookieWithValue($cookie_name, $value) {
        $cookie_name    = $this->fixStepArgument($cookie_name);
        $value          = $this->fixStepArgument($value);
        $this->getSession()->setCookie($cookie_name, $value);
    }

    /**
     * @Then /^I should not see cookie "([^"]*)"$/
     */
    public function iShouldNotSeeCookie($cookie_name) {
        $cookie_name    = $this->fixStepArgument($cookie_name);
        if($this->getSession()->getCookie('welcome_info_name') == $cookie_name) {
            throw new Exception('Cookie not found');
        }
    }

    /**
     * Destroy cookies
     *
     * @Then /^I destroy my cookies$/
     */
    public function iDestroyMyCookies() {
        $this->getSession()->reset();
    }


}