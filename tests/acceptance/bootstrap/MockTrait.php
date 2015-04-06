<?php
use Behat\Gherkin\Node\PyStringNode;

/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 3/31/15
 * Time: 2:15 PM
 */

trait MockTrait {

    protected $fields;
    protected $model;

    /**
     * @Given I mock :arg1 with properties:
     */
    public function iMockWithProperties($model, PyStringNode $properties)
    {
        try
        {
            $this->setModel($model);
            $this->makeFields($properties);
            $this->removeMockIfExists();

            $this->checkModelForTimeStamps();

            $this->createMock();
        }
        catch(\Exception $e)
        {
            $this->printDebug(sprintf("Error making mock", $e->getMessage()));
        }
    }

    public function createMock()
    {
        $this->getModel()->create($this->fields);
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = new $model;
    }

    private function checkModelForTimeStamps()
    {
        if($this->getModel()->timestamps)
        {
            $dateTime = new DateTime('-3 day');
            $created_at = $dateTime->format('Y-m-d H:i:s');
            $this->fields['created_at'] = $created_at;
            $this->fields['updated_at'] = $created_at;
        }
    }

    protected function removeMockIfExists()
    {
        if(isset($this->fields['id']) && $results = $this->getModel()->find($this->fields['id']))
            $results->delete();
    }

    protected function makeFields($properties)
    {
        foreach($properties->getStrings() as $value)
        {
            $field = explode(":", $value);
            $this->fields[trim($field[0])] = str_replace('"', '', trim($field[1]));
        }
    }

    /**
     * @Given /^I reset mock data$/
     */
    public function iResetMockData() {
        $this->getFactory()->type('mock')->reset();
    }

    /**
     * @Given /^I mock "([^"]*)" with id "([^"]*)"$/
     */
    public function iMockWithId($model, $id) {
        if(!$model::find($id))
            Factory::create($model, ['id' => $id]);
    }

}