<?php

use Drupal\DrupalExtension\Context\DrupalSubContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Must be a unique class per project eg RepoNameProjectName
 */
class ExampleFooBarSubContext extends BehatContext implements Drupal\DrupalExtension\Context\DrupalSubContextInterface {
  
    /**
     *
     * Setup the needed SubContext alias
     * must be unique for each project
     * repo_name_project_name
    */
    public static function getAlias() {
        return 'example_repo_name';
    }
    
     /**
      * @Given /^I click the foo bar button$/
      *
     */
    public function iClickFooBarButton()
    {
        //Since we are in a subcontext (fooFinder)
        // I getMainContext to reach outside of this class
        // more can be seen at http://docs.behat.org/en/v2.5/guides/4.context.html
        /**
         * $key = $this->useSelector('foo');
         * $this->getMainContext()->getSession()->getPage()->find('css', "#social-{$key} a")->click();
        */
    }

}
