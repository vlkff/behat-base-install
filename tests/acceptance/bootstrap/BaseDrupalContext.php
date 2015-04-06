<?php


class BaseDrupalContext extends \Kirschbaum\DrupalBehatRemoteAPIDriver\DrupalRemoteContext {


    /**
     * For javascript enabled scenarios, always wait for AJAX before clicking.
     *
     * @BeforeStep @javascript
     */
    public function beforeJavascriptStep($event) {
//    $text = $event->getStep()->getText();
//    if (preg_match('/(follow|press|click|submit)/i', $text)) {
//      $this->iWaitForAjaxToFinish();
//    }
    }

    /**
     * For javascript enabled scenarios, always wait for AJAX after clicking.
     *
     * @AfterStep @javascript
     */
    public function afterJavascriptStep($event) {
//    $text = $event->getStep()->getText();
//    if (preg_match('/(follow|press|click|submit)/i', $text)) {
//      $this->iWaitForAjaxToFinish();
//    }
    }


} 