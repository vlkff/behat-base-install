<?php

use Behat\Mink\Exception\ExpectationException;
use PHPUnit_Framework_Assert as PHPUnit;

class FeatureContext extends BaseContext
{
    protected $scope_array;
    protected $firstTestUrl;

    protected $asset_path;
    protected $asset_prefix;

    private $output;

    public function __construct(array $parameters) {

        parent::__construct($parameters);
    }

    /**
     * @Given /^I follow the first link containing the text "([^"]*)"$/
     */
    public function iFollowTheFirstLinkContainingTheText($link) {
        try {
            $link = $this->fixStepArgument($link);
            $this->getMainContext()->getSession()->getPage()->clickLink($link);
        }
        catch(\Exception $e)
        {
            throw new ExpectationException(sprintf("Error logging in %s", $e->getMessage()), $this->getMainContext()->getSession());
        }
    }

    /**
     * @Given /^I fill in the "([^"]*)" with "([^"]*)"$/
     */
    public function iFillInTheWith($arg1, $arg2)
    {

        $arg1 = $this->fixStepArgument($arg1);
        $arg2 = $this->fixStepArgument($arg2);

        $this->getSession()->getPage()->fillField($arg1, $arg2);
    }

    /**
     * @Then /^I take a screenshot$/
     */
    public function takeAScreenShot()
    {
        $this->getAssetPath();
        $image = $this->getSession()->getDriver()->getScreenshot();
        $name = 'screenshot_' . time() . '.jpg';
        file_put_contents($this->getAssetPath() . '/' . $name, $image);
    }

    public function getAssetPrefix()
    {
        if($this->asset_prefix == null)
            $this->setAssetPrefix();
        return $this->asset_prefix;
    }

    public function setAssetPrefix($prefix = false)
    {
        if($prefix == null)
        {
            if(!isset($this->parameters['asset_prefix']))
            {
                $prefix = 'screenshot_' . time();
            } else {
                $prefix = $this->parameters['asset_prefix'];
            }
        }

        $this->asset_prefix = $prefix;
        return $this;
    }

    public function getAssetPath()
    {
        if($this->asset_path == null)
            $this->setAssetPath();
        return $this->asset_path;
    }

    public function setAssetPath($path = null)
    {
        if($path == null)
        {
            if(!isset($this->parameters['asset_path']))
            {
                $path = '/tmp/' . $this->getAssetPrefix();
            } else {
                $path = $this->parameters['asset_path'] . '/'  . $this->getAssetPrefix();
            }
        }

        if(!$this->file->exists($path))
        {
            $this->file->mkdir($path);
        }

        $this->asset_path = $path;
        return $this;
    }

    /**
     * from orig file
     */

    /**
     * Check if the port is 443 or 80 eg secure or not.
     *
     * @Then /^the page is secure$/
     */
    public function thePageIsSecure()
    {
        $current_url = $this->getSession()->getCurrentUrl();
        if(strpos($current_url, 'https') === false) {
            throw new Exception('Page is not using SSL and is not Secure');
        }
    }

    /**
     * @hidden
     *
     * @Given /^I click the "([^"]*)" social button$/
     */
    public function iClickTheSocialButton($key)
    {
        $key = $this->useSelector($key);
        $this->getSession()->getPage()->find('css', "#social-{$key} a")->click();
    }


    /**
     * @hidden
     *
     * @Given /^I click the element located at "([^"]*)"$/
     */
    public function iClickTheElementLocatedAt($item)
    {
        $item = $this->useSelector($item);
        $node = $this->getSession()->getPage()->find('css', $item);
        if($node) {
            $node->click();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * Click on the Javascript Alert Window
     *
     * @Then /^I click on the alert window$/
     */
    public function iClickOnTheAlertWindow() {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }

    /**
     * Check if class exists note: this does not include a path just a class name
     *
     * @Then /^I see this element exists "([^"]*)"$/
     */
    public function iSeeThisElementExists($item)
    {
        $item = $this->useSelector($item);
        $css = $this->getSession()->getPage()->find('css', $item);
        if($css) {
            //
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     *
     * @Then /^I see this element does not exist "([^"]*)"$/
     */
    public function iSeeThisElementDoesNotExist($item)
    {
        $item = $this->useSelector($item);
        $css = $this->getSession()->getPage()->find('css', $item);
        if($css)
        {
            throw new Exception('Element not found');
        }
    }

    /**
     * Use the path eg wiki/Main_Page to see if you are on that path
     *
     * @Then /^I should be on the path "([^"]*)"$/
     */
    public function iShouldBeOnThePath($page)
    {
        $this->assertPageAddress($page);
    }

    /**
     * @hidden
     *
     * @Then /^I see this class exists "([^"]*)"$/
     */
    public function iSeeThisClassExists2($item)
    {
        $item = $this->useSelector($item);
        $css = $this->getSession()->getPage()->find('css', $item);
        if($css) {
            //$xpath = $this->getSession()->getPage()->find('xpath', $item);
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * See if the element (name|label|id) is greater than the % of the window
     *
     * @Then /^the element "([^"]*)" should be "([^"]*)" percent or greater than the window$/
     */
    public function theElementShouldBePercentOrGreaterThanTheWindow($arg1, $arg2)
    {
        $arg1 = $this->useSelector($arg1);
        $arg2 = $this->useSelector($arg2);
        //@todo
        $javascript_check = <<<HEREDOC
        if(!jQuery('$arg1').length) { return "FAILED"; }
HEREDOC;

        if($javascript_check != "FAILED") {
            $javascipt = <<<HEREDOC
        var target = jQuery('$arg1').height();
        var window_height = jQuery(window).height();
        var totalOf = target / window_height * 100;
        if( totalOf >= $arg2 ) { return totalOf; } else { return "FAILED"; }
HEREDOC;

            $results = $this->getSession()->evaluateScript($javascipt);
            if($results == "FAILED") {
                throw new Exception('Element not the right size');
            }
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * Remove and element that may be in the way of another element using the ID
     *
     * @Then /^I remove the element located at "([^"]*)"$/
     */
    public function iRemoveTheElementLocatedAt($item) {
        $item = $this->useSelector($item);
        $node = $this->getSession()->getPage()->find('css', $item);
        if($node) {
            $javascipt = <<<HEREDOC
        jQuery('$item').remove();
HEREDOC;
            $this->getSession()->executeScript($javascipt);
        } else {
            throw new Exception('Element not found');
        }
    }


    /**
     * @hidden
     *
     * @Given /^I press the element located at "([^"]*)"$/
     */
    public function iPressTheElementLocatedAt($item)
    {
        $item = $this->useSelector($item);
        $node = $this->getSession()->getPage()->find('css', $item);
        if ($node) {
            $node->press();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @Given /^Is Checked "([^"]*)"$/
     */
    public function isChecked($item)
    {
        $item = $this->useSelector($item);
        $node = $this->getSession()->getPage()->find('css', $item);
        $node->press();
    }

    /**
     * @hidden
     *
     * @Given /^I check "([^"]*)" using token$/
     */
    public function iCheckUsingToken($item)
    {
        $item = $this->useSelector($item);
        $node = $this->getSession()->getPage()->find('css', $item);
        if (null === $node) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $item
            );
        }
        $node->check();
    }

    /**
     * @hidden
     *
     * @Given /^I saw page loaded$/
     */
    public function iSawPageLoaded()
    {
        $test = "'interactive' == document.readyState || 'complete' == document.readyState";
        $this->getSession()->wait(self::MAX_LOAD_WAIT, $test);
    }

    /**
     * @hidden
     *
     * @Given /^I am on the "([^"]*)" page$/
     */
    public function iAmOnTheXPage($page)
    {
        $page = $this->useSelector($page);
        if (empty($this->pageMap[$page]))
        {
            throw new Exception("Invalid page: '{$page}'");
        }

        $this->visit($this->pageMap[$page]);
    }

    /**
     * Performs a site search using standard Drupal Search.
     *
     * @When /^I search for "([^"]*)"$/
     */
    public function iSearchFor($text)
    {
        $text = $this->useSelector($text);
        $text = $this->fixStepArgument($text);
        $this->fillField('edit-search-block-form--2', $text);
        $this->pressButton('edit-submit');
    }

    /**
     * @hidden
     *
     * @When /^I click the continue leave to the site button$/
     */
    public function iClickTheContinueToLeaveTheSiteButton()
    {
        $this->getSession()->getPage()->find('xpath', '//input[@src="/img/button.continue.gif"]')->click();
    }

    /**
     * Hover over a menu item using the xpath
     *
     * @Given /^I hover over the "([^"]*)" menu item$/
     */
    public function iHoverOverTheMenuItem($item)
    {
        $item = $this->useSelector($item);
        if($this->getSession()->getPage()->find('xpath', '//li/a[@title="' . $item . '"]')) {
            $this->getSession()->getPage()->find('xpath', '//li/a[@title="' . $item . '"]')->mouseOver();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * Hover over a non link item using id|name|class
     *
     * @Given /^I hover over a non link item named "([^"]*)"$/
     */
    public function iHoverOverANonLinkItemNamed($item)
    {
        $item = $this->useSelector($item);
        if($this->getSession()->getPage()->find('css', $item)) {
            $this->getSession()->getPage()->find('css', $item)->mouseOver();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @Given /^I hover over the menu item named "([^"]*)"$/
     */
    public function iHoverOverTheMenuItemNamed($arg1)
    {
        $item = $this->useSelector($arg1);
        if($this->getSession()->getPage()->find('css', $item)) {
            $this->getSession()->getPage()->find('css', $item)->mouseOver();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * Hover over a non link item using id|name|class
     *
     * @Given /^I hover over a non link menu item named "([^"]*)"$/
     */
    public function iHoverOverANonLinkMenuItemNamed($arg1)
    {
        $item = $this->useSelector($arg1);
        if($this->getMainContext()->getSession()->getPage()->find('css', $item)) {
            $this->getMainContext()->getSession()->getPage()->find('css', $item)->mouseOver();
        } else {
            throw new Exception('Element not found');
        }
    }



    /**
     * See if Element has style eg p.padL8 has style font-size= 12px
     *
     * @Then /^the element "([^"]*)" should have style "([^"]*)"$/
     */
    public function theElementShouldHaveStyle($arg1, $arg2)
    {

        $arg1= $this->useSelector($arg1);
        $arg2 = $this->useSelector($arg2);
        $element = $this->getSession()->getPage()->find('css', $arg1);
        if($element) {
            if(strpos($element->getAttribute('style'), $arg2) === FALSE) {
                throw new Exception('Style not found');
            }
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @Then /^I fill in the form field with id "([^"]*)" number "([^"]*)" with the value of "([^"]*)"$/
     */
    public function fillInTheFormFieldWithIDNumberWithTheValueOf($arg1, $arg2, $arg3)
    {
        $arg1 = $this->fixStepArgument($arg1);
        $arg2 = $this->fixStepArgument($arg2);
        $arg3 = $this->fixStepArgument($arg3);

        $arg = $arg2 - 1; //most users do not start at 0
        $form_field = $this->getSession()->getPage()->findAll('css', $arg1);
        if($form_field) {
            $form_field[$arg]->setValue($arg3);
        } else {
            throw new Exception('Form Element not found');
        }
    }

    /**
     * @hidden
     *
     * @Given /^I get redirected to home page$/
     */
    public function iGetRedirectedToHomePage()
    {
        $this->getSession()->wait(3000,
            "jQuery('a.logout-link').text() == 'Log out'"
        );
    }

    /**
     * @hidden
     *
     * @Given /^I follow savedTest$/
     */
    public function iFollowSavedtest()
    {
        $test_file = $this->getSession()->getPage()->find('css', 'a#test-file')->getAttribute('href');
        $this->visit($test_file);
    }

    /**
     * @hidden
     *
     * @Given /^I fill in featuresTag$/
     */
    public function iFillInFeaturestag()
    {
        $registerForm = $this->getSession()->getPage()->find('css', 'form.scenario-builder');
        $el = $registerForm->find('css', 'input.ui-widget-content');
        $el->setValue('@local ');
    }

    /**
     * @hidden
     *
     * @Given /^I fill in sectionOneTag$/
     */
    public function iFillInSectiononetag()
    {
        $registerForm = $this->getSession()->getPage()->find('css', 'form.scenario-builder');
        $el = $registerForm->findAll('css', 'li.tagit-new input.ui-widget-content');
        $el[1]->setValue('@readonly ');
    }


    /**
     * @hidden
     *
     * @Given /^I fill in sectionTwoTag$/
     */
    public function iFillInSectiontwotag()
    {
        $registerForm = $this->getSession()->getPage()->find('css', 'form.scenario-builder');
        $el = $registerForm->findAll('css', 'li.tagit-new input.ui-widget-content');
        $el[2]->setValue('@anonymous ');
    }


    /**
     * Some pages have duplicate Submit buttons IDs so pass a number e.g 1 or 2 to try different elements
     *
     * @Given /^I click the submit button number "([^"]*)"$/
     */
    public function IClickTheSubmitButtonNumber($arg1)
    {
        $arg1 = $this->fixStepArgument($arg1);
        $arg = $arg1 - 1; //most users do not start at 0
        $submit = $this->getSession()->getPage()->findAll('css', 'input#edit-submit');
        //click $submit[$arg1]
        $submit[$arg]->press();
    }

    /**
     * @hidden
     *
     * @Given /^I get first test name$/
     */
    public function iGetFirstTestName()
    {
        $this->getSession()->visit($this->locatePath('/admin/behat/index'));
        $table = $this->getSession()->getPage()->find('css', 'table#admin-features');
        $el = $table->findAll('css', 'a');
        $link = $el[0]->getAttribute('href');
        $this->firstTestUrl = $link;
    }

    /**
     * @hidden
     *
     * @Given /^I click run button$/
     */
    public function iClickRunButton()
    {
        if(!$this->getSession()->getPage()->find('css', "#edit-run-test.hidden")) {
            $this->getSession()->getPage()->find('css', "#edit-run-test")->click();
        } else {
            $this->getSession()->getPage()->find('css', "#edit-saucelabs-run")->click();
        }
    }


    /**
     * @hidden
     *
     * @Given /^I view first test$/
     */
    public function iViewFirstTest()
    {
        $this->visit($this->firstTestUrl);
    }

    /**
     * @hidden
     *
     * @Then /^I wait for home page$/
     */
    public function iWaitForHomePage()
    {
        $this->getSession()->switchToWindow($this->originalWindowName);
        $this->getSession()->wait(3000,
            "jQuery('a.logout-link').text() == 'Log out'"
        );
    }

    /**
     * See and press logout
     *
     * @Then /^I see and press log out$/
     */
    public function iSeeAndPressLogOut()
    {
        $this->getSession()->switchToWindow($this->originalWindowName);
        $log_out = $this->getSession()->getPage()->find('css', 'a.logout-link')->getAttribute('href');
        $this->visit($log_out);
    }


    /**
     * @hidden we have the url match now
     *
     * @Then /^I wait till I see if page is redirected from "([^"]*)" to "([^"]*)"$/
     */
    public function iWaitTillISeeIfPageIsRedirectedFromTo($arg1, $arg2) {
        //There is a redirect after log back to /login that then goes to home
        //this tries to force that visit one mrore time
        $arg1 = $this->fixStepArgument($arg1);
        $arg2 = $this->fixStepArgument($arg2);

        //Relative URLS is best
        if(strpos($arg1, 'http') === FALSE) {
            $url = $this->getSession()->getCurrentUrl();
            $arg1 = $url . $arg1;
        }

        $this->getSession()->visit($arg1);

        if(strpos($arg2, 'http') === FALSE) {
            $url = $this->getSession()->getCurrentUrl();
            $arg2 = $url . $arg2;
        }

        if($this->getSession()->getCurrentUrl() == $arg2) {
            throw new Exception('Looks like you are not at the correct URL');
        }
    }

    /**
     * @hidden we have the url match now
     *
     * @Then /^the url should redirect to "([^"]*)"$/
     */
    public function theUrlShouldRedirectTo($arg1)
    {
        $arg1 = $this->fixStepArgument($arg1);
        if($this->getSession()->getCurrentUrl() != $arg1) {
            throw new Exception('You are not on the expected URL');
        }
    }


    /**
     * See if element is visible
     *
     * @Then /^element "([^"]*)" is visible$/
     */
    public function elementIsVisible($arg) {
        $arg = $this->fixStepArgument($arg);
        $el = $this->getSession()->getPage()->find('css', $arg);
        if($el) {
            if(!$el->isVisible()){
                throw new Exception('Element is not visible');
            }
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * See if element is not visible
     *
     * @Then /^element "([^"]*)" is not visible$/
     */
    public function elementIsNotVisible($arg) {
        $arg = $this->fixStepArgument($arg);
        $el = $this->getSession()->getPage()->find('css', $arg);
        if($el) {
            if($el->isVisible()){
                throw new Exception('Element is visible');
            }
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * This will cause a 3 second delay
     *
     * @Given /^I wait$/
     */
    public function iWait() {
        sleep(3);
    }


    /**
     * Set a waiting time in seconds
     *
     * @Given /^I wait for "([^"]*)" seconds$/
     */
    public function iWaitForSeconds($arg1) {
        $arg1 = $this->fixStepArgument($arg1);
        sleep($arg1);
    }

    /**
     * @hidden
     *
     * @Given /^I click the submit button with value "([^"]*)"$/
     */
    public function iClickTheSubmitButtonWithValue($arg1)
    {
        $arg1 = $this->fixStepArgument($arg1);
        $this->getSession()->getPage()->find('xpath', '//input[@value="' . $arg1 . '"]')->click();

    }

    /**
     * Click an element with an onlick handler
     *
     * @Given /^I click on element which has onclick handler located at "([^"]*)"$/
     */
    public function iClickOnElementWhichHasOnclickHandlerLocatedAt($item)
    {
        $item = $this->fixStepArgument($item);
        $node = $this->getSession()->getPage()->find('css', $item);
        if($node) {
            $this->getSession()->wait(3000,
                "jQuery('{$item}').trigger('click')"
            );
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @Given /^I switch to element located at "([^"]*)" and press enter key$/
     */
    public function iSwitchToElementLocatedAtAndPressEnterKey($item)
    {
        $item = $this->fixStepArgument($item);
        $node = $this->getSession()->getPage()->findAll('css', $item);
        if($node) {
            $node->focus();
            $node->keyUp('enter');
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * Some forms do not have a Submit button just pass the ID of the FOrm
     *
     * @Given /^I submit the form with id "([^"]*)"$/
     */
    public function iSubmitTheFormWithId($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $node = $this->getSession()->getPage()->find('css', $arg);
        if($node) {
            $this->getSession()->executeScript("jQuery('$arg').submit();");
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @When /^I press the element "([^"]*)"$/
     */
    public function iPressTheElement($arg)
    {
        $arg = $this->fixStepArgument($arg);

        $node = $this->getSession()->getPage()->find('css', $arg);
        if($node) {
            $this->getSession()->getPage()->find('css', $arg)->press();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @When /^I press the element "([^"]*)" and switch to popup$/
     */
    public function iPressTheElementAndSwitchToPopup($arg)
    {
        $arg = $this->fixStepArgument($arg);

        $this->setMainWindow();
        $this->iPressTheElement($arg);
        $popupName = $this->getNewPopup($this->originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * @Given /^I select Mock Site from select list$/
     */
    public function iSelectMockSiteFromSelectList() {
        $node = $this->getSession()->getPage()->find('xpath', "//*[@id='site-id']/div[1]");
        if($node) {
            $node->click();
            $this->getSession()->wait(2000);
            $this->getSession()->getPage()->find('xpath', "//*[@id='site-id']/div[2]/div/div/div[3]/div/span")->click();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @Given /^I select Mock Team from select list$/
     */
    public function iSelectMockTeamFromSelectList() {
        $node = $this->getSession()->getPage()->find('xpath', "//*[@id='team-id']/div[1]/input");
        if($node) {
            $node->click();
            $this->getSession()->wait(2000);
            $this->getSession()->getPage()->find('xpath', "//*[@id='team-id']/div[2]/div/div/div[3]/div")->click();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @When /^I click the element "([^"]*)"$/
     */
    public function iClickTheElement($arg)
    {
        $arg = $this->fixStepArgument($arg);
        $node = $this->getSession()->getPage()->find('css', $arg);
        if($node) {
            $this->getSession()->getPage()->find('css', $arg)->click();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @When /^I click the element "([^"]*)" and switch to popup$/
     */
    public function iClickTheElementAndSwitchToPopup($arg)
    {
        $arg = $this->fixStepArgument($arg);

        $this->setMainWindow();
        $this->iClickTheElement($arg);
        $popupName = $this->getNewPopup($this->originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }


    /**
     * @hidden
     *
     * @When /^I follow the element "([^"]*)"$/
     */
    public function iFollowTheElement($arg)
    {
        $arg = $this->fixStepArgument($arg);

        $node = $this->getSession()->getPage()->find('css', $arg);
        if($node) {
            $this->getSession()->getPage()->find('css', $arg)->click();
        } else {
            throw new Exception('Element not found');
        }
    }

    /**
     * @hidden
     *
     * @When /^I follow the element "([^"]*)" and switch to popup$/
     */
    public function iFollowTheElementAndSwitchToPopup($arg)
    {
        $arg = $this->fixStepArgument($arg);

        $this->setMainWindow();
        $this->iFollowTheElement($arg);
        $popupName = $this->getNewPopup($this->originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * Switch to a New Windows
     *
     * @When /^I switch to popup$/
     */
    public function iSwitchToPopup()
    {
        $this->setMainWindow();
        $popupName = $this->getNewPopup($this->originalWindowName);
        $this->getSession()->switchToWindow($popupName);
    }

    /**
     * Use the full css tag eg #form .class etc
     *
     * @Then /^I wait for the element "([^"]*)" to appear$/
     * @Then /^I wait for element "([^"]*)" to appear$/
     */
    public function iWaitForElementToAppear($arg1)
    {
        $arg1 = $this->fixStepArgument($arg1);

        $this->getSession()->wait(20000,
            "$('" + $arg1 + "').length > 0"
        );
    }



    /**
     * Use the full css tag eg #form .class etc
     *
     * @Then /^I wait for the element "([^"]*)" to not be there$/
     */
    public function iWaitForElementToNotBeThere($arg1)
    {
        $arg1 = $this->fixStepArgument($arg1);

        $this->getSession()->wait(20000,
            "$('" + $arg1 + "').length < 1"
        );
    }



    /**
     * @Given /^I have a file named "([^"]*)"$/
     */
    public function iHaveAFileNamed($arg1)
    {
        touch($arg1);
    }

    /**
     * @When /^I run "([^"]*)"$/
     */
    public function iRun($command)
    {
        exec($command, $output);
        $this->output = trim(implode("\n", $output));
    }

    /**
     * @Then /^I should get:$/
     */
    public function iShouldGet(PyStringNode $string)
    {
        if ((string) $string !== $this->output) {
            throw new Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }



    /**
     * @Given /^I reset the session$/
     */
    public function iResetTheSession() {
        $this->getSession()->reset();
    }

    /**
     * @Then /^I fill in wysiwyg on field "([^"]*)" with "([^"]*)"$/
     */
    public function iFillInWysiwygOnFieldWith($arg, $arg2)
    {
        $js = <<<HEREDOC
        jQuery("textarea[name='$arg']").css('visibility', 'visible');
        jQuery("textarea[name='$arg']").show();
HEREDOC;
        $this->getSession()->executeScript($js);
        $this->fillField($arg, $arg2);
    }



}
