<?php
use Behat\Gherkin\Node\PyStringNode;
use Laracasts\TestDummy\Factory;

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
     * @Given I mock "([^"]*)" with properties:
     * @Given /^I mock "([^"]*)" with properties:$/
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
     * @return Factory
     */
    public function getFactory()
    {
        if($this->factory == null)
            $this->setFactory();
        return $this->factory;
    }

    /**
     * @param Factory $factory
     */
    public function setFactory($factory = null)
    {
        if($factory == null)
            $factory = new Factory();
        $this->factory = $factory;
    }

    /**
     * @Given /^I mock "([^"]*)" with id "([^"]*)"$/
     */
    public function iMockWithId($model, $id) {
        if(!$model::find($id))
            Factory::create($model, ['id' => $id]);
    }

    /**
     * @Given I clean out "([^"]*)" with id of "([^"]*)"
     * @Given /^I clean out "([^"]*)" with id of "([^"]*)"$/
     */
    public function iCleanOutIdOf($model, $id)
    {
        try
        {
            if($results = $model::find($id))
                $results->delete();
        }
        catch(\Exception $e)
        {
            $this->printDebug(sprintf("Could not find id %s using model %s", $id, $model));
        }
    }
}