<?php
use Behat\Gherkin\Node\PyStringNode;

/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 4/6/15
 * Time: 5:37 AM
 */

trait ApiTrait {


    /**
     * @Given /^the "([^"]*)" property has the value "([^"]*)"$/
     */
    public function thePropertyHasTheValue($arg1, $arg2)
    {
        $request = $this->getScopePayload();

        try
        {
            if($request->{$arg1} != $arg2)
            {
                throw new \Exception(
                    "Fields $arg1 does not equal $arg2 but instead equals {$request->data->$arg1}");
            }
        } catch(\Exception $e)
        {
            throw new \Exception(
                "Fields $arg1 does not exist");
        }
    }

    /**
     * @Given /^I have the payload:$/
     */
    public function iHaveThePayload(PyStringNode $requestPayload)
    {
        $this->requestPayload = $this->fixStepArgument($requestPayload);
    }




    /**
     * @When /^I request "(GET|PUT|POST|DELETE) ([^"]*)"$/
     */
    public function iRequest($httpMethod, $resource)
    {
        $this->resource = $resource;
        $this->checkForAccessTokenInUrl();


        $method = strtolower($httpMethod);

        try {
            switch ($httpMethod) {
                case 'PUT':
                    $payload = json_decode($this->requestPayload, true);
                    $this->response = $this
                        ->client
                        ->$method($this->resource, array('json' => $payload, 'verify' => false));
                    break;
                case 'DELETE':
                    $payload = json_decode($this->requestPayload, true);
                    $this->response = $this
                        ->client
                        ->$method($this->resource, array('json' => $payload, 'verify' => false));
                    break;
                case 'POST':

                    $post = json_decode($this->requestPayload, true);
                    $this->response = $this
                        ->client
                        ->$method($this->resource, array('body' => $post, 'verify' => false));
                    break;
                default:
                    $this->response = $this
                        ->client
                        ->$method($this->resource, ['verify' => false]);
            }
        } catch (BadResponseException $e) {

            $response = $e->getResponse();

            // Sometimes the request will fail, at which point we have
            // no response at all. Let Guzzle give an error here, it's
            // pretty self-explanatory.
            if ($response === null) {
                throw $e;
            }

            $this->response = $e->getResponse();
        }
    }



    /**
     * @Then /^I get a "([^"]*)" response$/
     */
    public function iGetAResponse($statusCode)
    {
        $response = $this->getResponse();
        $contentType = $response->getHeader('Content-Type');

        if ($contentType === 'application/json') {
            $bodyOutput = $response->getBody();
        } else {
            $bodyOutput = 'Output is '.$contentType.', which is not JSON and is therefore scary. Run the request manually.';
        }
        assertSame((int) $statusCode, (int) $this->getResponse()->getStatusCode(), $bodyOutput);
    }

    /**
     * Get the response entity body
     *
     * @param bool $asString Set to TRUE to return a string of the body rather than a full body object
     *
     * @return EntityBodyInterface|string
     */
    public function getBody($asString = false)
    {
        return $asString ? (string) $this->body : $this->body;
    }

    public function getHeader($header)
    {
        return $this->headers[$header];
    }

    /**
     * @Given /^scope into the "([^"]*)" property$/
     */
    public function scopeIntoTheProperty($scope)
    {
        $this->scope = $scope;
    }

    /**
     * @Given /^the properties exist:$/
     */
    public function thePropertiesExist(PyStringNode $propertiesString)
    {
        foreach (explode("\n", (string) $propertiesString) as $property) {
            $this->thePropertyExists($property);
        }
    }

    /**
     * @Given /^the "([^"]*)" property exists$/
     */
    public function thePropertyExists($property)
    {
        $payload = $this->getScopePayload();

        $message = sprintf(
            'Asserting the [%s] property exists in the scope [%s]: %s',
            $property,
            $this->scope,
            json_encode($payload)
        );
        if (is_object($payload)) {
            assertTrue(array_key_exists($property, get_object_vars($payload)), $message);
        } else {
            if($payload == null)
                throw new \Exception(sprintf("Scope returned null might be due to no results for that scope %s", $this->scope));
            assertTrue(array_key_exists($property, $payload), $message);
        }
    }

    /**
     * Returns the payload from the current scope within
     * the response.
     *
     * @return mixed
     */
    protected function getScopePayload()
    {
        $payload = $this->getResponsePayload();

        if (! $this->scope) {
            return $payload;
        }

        return $this->arrayGet($payload, $this->scope);
    }

    /**
     * Return the response payload from the current response.
     *
     * @throws Exception
     * @return  mixed
     */
    protected function getResponsePayload()
    {
        if (! $this->responsePayload) {
            $json = json_decode($this->getResponse()->getBody(true));
            if (json_last_error() !== JSON_ERROR_NONE) {
                $message = 'Failed to decode JSON body ';

                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        $message .= '(Maximum stack depth exceeded).';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $message .= '(Underflow or the modes mismatch).';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $message .= '(Unexpected control character found).';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $message .= '(Syntax error, malformed JSON).';
                        break;
                    case JSON_ERROR_UTF8:
                        $message .= '(Malformed UTF-8 characters, possibly incorrectly encoded).';
                        break;
                    default:
                        $message .= '(Unknown error).';
                        break;
                }

                throw new Exception($message);
            }

            $this->responsePayload = $json;
        }

        return $this->responsePayload;
    }

    /**
     * Checks the response exists and returns it.
     *
     * @return  Guzzle\Http\Message\Response
     */
    protected function getResponse()
    {
        if (! $this->response) {
            throw new Exception("You must first make a request to check a response.");
        }

        return $this->response;
    }

    /**
     * @Then /^I dump the response body/
     */
    public function iDumpTheResponseBody()
    {
        $response = $this->getResponse();

        $this->printDebug(print_r(json_decode($response->getBody(), true), 1));
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @copyright   Taylor Otwell
     * @link        http://laravel.com/docs/helpers
     * @param       array   $array
     * @param       string  $key
     * @return      mixed
     */
    protected function arrayGet($array, $key)
    {
        if (is_null($key)) {
            return $array;
        }

        foreach (explode('.', $key) as $segment) {

            if (is_object($array)) {
                if (! isset($array->{$segment})) {
                    return;
                }
                $array = $array->{$segment};

            } elseif (is_array($array)) {
                if (! array_key_exists($segment, $array)) {
                    return;
                }
                $array = $array[$segment];
            }
        }

        return $array;
    }




    /**
     * @Given /^I convert results data to an array$/
     */
    public function iConvertResultsDataToAnArray()
    {
        $results = $this->getResponsePayload();
        $this->scope_array = (array) $results->data;
    }

    /**
     * @Given /^scope_array has key "([^"]*)"$/
     */
    public function scopeArrayHasKey($arg1)
    {
        if(!isset($this->scope_array[$arg1]))
        {
            throw new \Exception(sprintf("Key not found %s", $arg1));
        }
    }

    /**
     * @TODO super ugly
     * @Given /^scope_array child object "([^"]*)" exists in "([^"]*)"$/
     */
    public function scropeArrayChildObjectExistsIn($arg1, $arg2)
    {
        if(!isset($this->scope_array[$arg2]))
        {
            throw new \Exception(sprintf("Parent %s not found or does not contain value %s", $arg1, $arg2));
        }

        if(!$this->scope_array[$arg2][0]->$arg1)
        {
            throw new \Exception(sprintf("Child %s not found or does not contain value %s", $arg1, $arg2));
        }
    }

    /**
     * @Given /^scope_array key "([^"]*)" contains "([^"]*)"$/
     */
    public function scopeArrayKeyContains($arg1, $arg2)
    {
        if(!isset($this->scope_array[$arg1]) && assertContains($arg2, $this->scope_array[$arg1]))
        {
            throw new \Exception(sprintf("Key %s not found or does not contain value %s", $arg1, $arg2));
        }
    }

    /**
     * @Given /^scope_array key "([^"]*)" count is (\d+) items$/
     */
    public function scopeArrayKeyCountIsItems($arg1, $arg2)
    {
        if(!isset($this->scope_array[$arg1]))
        {
            throw new \Exception(sprintf("Key %s not found or does not contain value %s", $arg1, $arg2));
        }

        if(assertCount($arg2, $this->scope_array[$arg1]))
        {
            throw new \Exception(sprintf("The count  %d was not as expected but was", $arg2, count($this->scope_array[$arg1])));
        }
    }

    /**
     * @Given /^scope key "([^"]*)" is not empty$/
     */
    public function scopeKeyIsNotEmpty($arg1)
    {
        if(empty($this->scope_array[$arg1]))
        {
            throw new \Exception(sprintf("Key not found %s", $arg1));
        }
    }






    /**
     * @Given /^the "([^"]*)" property equals "([^"]*)"$/
     */
    public function thePropertyEquals($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        assertEquals(
            $actualValue,
            $expectedValue,
            "Asserting the [$property] property in current scope equals [$expectedValue]: ".json_encode($payload)
        );
    }


    /**
     * @Given /^the "([^"]*)" property contains "([^"]*)"$/
     */
    public function thePropertyContains($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        assertContains(
            $actualValue,
            $expectedValue,
            "Asserting the [$property] property in current scope contains [$expectedValue]: ".json_encode($payload)
        );
    }


    /**
     * @Given /^the "([^"]*)" property is an array$/
     */
    public function thePropertyIsAnArray($property)
    {
        $payload = $this->getScopePayload();

        $actualValue = $this->arrayGet($payload, $property);

        assertTrue(
            is_array($actualValue),
            "Asserting the [$property] property in current scope [{$this->scope}] is an array: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an object$/
     */
    public function thePropertyIsAnObject($property)
    {
        $payload = $this->getScopePayload();

        $actualValue = $this->arrayGet($payload, $property);

        assertTrue(
            is_object($actualValue),
            "Asserting the [$property] property in current scope [{$this->scope}] is an object: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an empty array$/
     */
    public function thePropertyIsAnEmptyArray($property)
    {
        $payload = $this->getScopePayload();
        $scopePayload = $this->arrayGet($payload, $property);

        assertTrue(
            is_array($scopePayload) and $scopePayload === [],
            "Asserting the [$property] property in current scope [{$this->scope}] is an empty array: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property contains (\d+) items$/
     */
    public function thePropertyContainsItems($property, $count)
    {
        $payload = $this->getScopePayload();

        assertCount(
            $count,
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property contains [$count] items: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is an integer$/
     */
    public function thePropertyIsAnInteger($property)
    {
        $payload = $this->getScopePayload();

        isType(
            'int',
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property in current scope [{$this->scope}] is an integer: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a string$/
     */
    public function thePropertyIsAString($property)
    {
        $payload = $this->getScopePayload();

        isType(
            'string',
            $this->arrayGet($payload, $property),
            "Asserting the [$property] property in current scope [{$this->scope}] is a string: ".json_encode($payload)
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a string equalling "([^"]*)"$/
     */
    public function thePropertyIsAStringEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();

        $this->thePropertyIsAString($property);

        $actualValue = $this->arrayGet($payload, $property);
        assertSame(
            $actualValue,
            $expectedValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is a string equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a boolean$/
     */
    public function thePropertyIsABoolean($property)
    {
        $payload = $this->getScopePayload();

        assertTrue(
            gettype($this->arrayGet($payload, $property)) == 'boolean',
            "Asserting the [$property] property in current scope [{$this->scope}] is a boolean."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a boolean equalling "([^"]*)"$/
     */
    public function thePropertyIsABooleanEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        if (! in_array($expectedValue, ['true', 'false'])) {
            throw new \InvalidArgumentException("Testing for booleans must be represented by [true] or [false].");
        }

        $this->thePropertyIsABoolean($property);

        assertSame(
            $actualValue,
            $expectedValue == 'true',
            "Asserting the [$property] property in current scope [{$this->scope}] is a boolean equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is a integer equalling "([^"]*)"$/
     */
    public function thePropertyIsAIntegerEqualling($property, $expectedValue)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $this->thePropertyIsAnInteger($property);

        assertSame(
            $actualValue,
            (int) $expectedValue,
            "Asserting the [$property] property in current scope [{$this->scope}] is an integer equalling [$expectedValue]."
        );
    }

    /**
     * @Given /^the "([^"]*)" property is either:$/
     */
    public function thePropertyIsEither($property, PyStringNode $options)
    {
        $payload = $this->getScopePayload();
        $actualValue = $this->arrayGet($payload, $property);

        $valid = explode("\n", (string) $options);

        assertTrue(
            in_array($actualValue, $valid),
            sprintf(
                "Asserting the [%s] property in current scope [{$this->scope}] is in array of valid options [%s].",
                $property,
                implode(', ', $valid)
            )
        );
    }

    /**
     * @Given /^scope into the first "([^"]*)" property$/
     */
    public function scopeIntoTheFirstProperty($scope)
    {
        $this->scope = "{$scope}.0";
    }


    /**
     * @Given /^reset scope$/
     */
    public function resetScope()
    {
        $this->scope = null;
    }

    /**
     * @Transform /^(\d+)$/
     */
    public function castStringToNumber($string)
    {
        return intval($string);
    }



    /**
     * @Given /^reports all has (\d+) items/
     */
    public function reportsAllHasItems($arg)
    {
        $results = $this->getResponsePayload();
        assertCount($arg, $results->data->reports_all, "Asserting the results have a count of [$arg].");
    }


    /**
     * @Given /^data component "([^"]*)" has (\d+) items/
     */
    public function dataComponentHasItems($component, $count)
    {
        $results = $this->getResponsePayload();
        assertCount($count, $results->data->$component);
    }

    /**
     * @Then /^the data component should return:$/
     */
    public function theDataComponentShouldReturn(PyStringNode $string)
    {
        $results = $this->getResponsePayload();
        assertContains((string) $string, $results->data);
    }

    /**
     * @Given /^data component "([^"]*)" is "([^"]*)"/
     */
    public function dataComponentEquals($component, $value)
    {
        $results = $this->getResponsePayload();
        assertEquals($value, $results->data->$component);
    }

    /**
     * @Given /^data results contains:$/
     */
    public function dataContains(PyStringNode $value)
    {
        $results = $this->getResponsePayload();
        assertContains((string) $value, $results->data);
    }

    /**
     * @Given /^message results contains:$/
     */
    public function messageContains(PyStringNode $value)
    {
        $results = $this->getResponsePayload();
        assertContains((string) $value, $results->message);
    }

    /**
     * @Given /^data component "([^"]*)" value has an ID of "([^"]*)"/
     */
    public function dataComponentValueHasAnIdOf($component, $id)
    {
        $results = $this->getResponsePayload();
        assertEquals($id, $results->data->$component->id);
    }

    /**
     * @Given /^data is not null$/
     */
    public function dataNotNull()
    {
        $results = $this->getResponsePayload();
        assertNotNull($results->data);
    }

    /**
     * @Given /^data has (\d+) items/
     */
    public function dataHasItems($arg)
    {
        $results = $this->getResponsePayload();
        assertCount($arg, $results->data);
    }

    /**
     * @Given /^data should have key "([^"]*)" with value "([^"]*)"$/
     */
    public function dataShouldHaveKeyWithValue($key, $value)
    {
        $results = $this->getResponsePayload();
        $found = false;
        foreach($results->data as $content)
        {
            if(isset($content->{$key}))
            {
                if($content->{$key} == $value)
                {
                    $found = true;
                }
            }
        }

        $message = sprintf("The value %s does not exists and should", $value);
        assertTrue($found, $message);
    }

    /**
     * @Given /^data should not have key "([^"]*)" with value "([^"]*)"$/
     */
    public function dataShouldNotHaveKeyWithValue($key, $value)
    {
        $results = $this->getResponsePayload();

        foreach($results->data as $content)
        {
            if(isset($content->{$key}))
            {
                $message = sprintf("The value %s exists and should not", $value);
                assertNotEquals($content->{$key}, $value, $message);
            }
        }
    }

    /**
     * @Given /^data has more than (\d+) items/
     */
    public function dataHasMoreThanItems($arg)
    {
        $results = $this->getResponsePayload();
        assertGreaterThanOrEqual($arg, $results->data);
    }

    /**
     * @Given /^data is string and contains "([^"]*)"$/
     */
    public function dataIsStringAndContains($arg)
    {
        $results = $this->getResponsePayload();
        assertContains($arg, $results->data, sprintf("String %s Not found in results", $arg));
    }

    /**
     * @Given /^jobs has (\d+) items/
     */
    public function jobsHasItems($arg)
    {
        $results = $this->getResponsePayload();
        assertCount($arg, $results->data->jobs);
    }

    /**
     * @Given /^the "([^"]*)" property count is (\d+)$/
     */
    public function thePropertyCountIs($arg1, $arg2)
    {
        $results = $this->getResponsePayload();
        $countable = (array) $results->data->$arg1;
        assertCount($arg2, $countable);
    }

    /**
     * @Given /^the "([^"]*)" property is not empty$/
     */
    public function thePropertyIsNotEmpty($arg)
    {
        $results = $this->getResponsePayload();
        assertNotEmpty($results->data->$arg);
    }



}