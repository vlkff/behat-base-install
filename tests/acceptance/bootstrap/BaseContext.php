<?php
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Faker\Factory as Faker;
use GuzzleHttp\Client;
use OrangeDigital\BusinessSelectorExtension\Context\BusinessSelectorContext;
use GuzzleHttp\Exception\BadResponseException;


class BaseContext extends MinkContext {

    use OauthHelper;
    use SeedHelper;
    use TokenFaker;
    use MockTrait;
    use BreakPointTrait;
    use WindowTraits;
    use CookieTrait;
    use IframeTrait;
    use AuthHelpers;
    use ApiTrait;

    protected $parameters;
    protected $tokenSelector;
    protected $refresh_token;
    protected $userName;
    protected $passWord;
    protected $userNameDemo;
    protected $passWordDemo;

    /**
     * The current resource
     */
    protected $resource;

    /**
     * The request payload
     */
    protected $requestPayload;

    /**
     * The Guzzle HTTP Response.
     * @var GuzzleHttp\Message\Response
     */
    protected $response;

    /**
     * The decoded response object.
     */
    protected $responsePayload;

    /**
     * The current scope within the response payload
     * which conditions are asserted against.
     */
    protected $scope;
    protected $headers = [];
    protected $body;

    /**
     * @var \Faker\Generator
     */
    public $faker;

    /**
     * @var Client
     */
    protected $client;

    protected $scope_array;

    protected $asset_path;
    protected $asset_prefix;



    public function __construct($parameters = [])
    {
        $this->faker = Faker::create();
        $config = ['base_url' => $parameters['base_url']];
        $this->client   = new Client($config);
        $this->parameters = $parameters;
        $this->setCredentials($parameters);
    }

    /**
     * @Given I remove :arg1 of :arg2
     */
    public function iRemove($model, $id)
    {
        try
        {
            $results = $model::find($id);
            if($results)
            {
                $results->delete();
            }
        } catch(\Exception $e)
        {
            throw new \Exception(sprintf("Error deleting your model %s with id %s message %s", $model, $id, $e->getMessage()));
        }
    }

    //Given I clean out "App\User" with id of "foo-bar-foo-baz-person-make"
    /**
     * @Given I clean out :arg1 with id of :arg2
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


    protected function setCredentials()
    {
        $this->userName = getenv('ADMIN_EMAIL');
        $this->passWord = getenv('ADMIN_PASSWORD');

        $this->userNameDemo = getenv('DEMO_EMAIL');
        $this->passWordDemo = getenv('DEMO_PASSWORD');
    }


    /**
     * Prints beautified debug string.
     *
     * @param string $string debug string
     */
    public function printDebug($string)
    {
        echo "\n\033[36m|  " . strtr($string, array("\n" => "\n|  ")) . "\033[0m\n\n";
    }


    /**
     * @Given /^parent results of batch is (\d+)$/
     */
    public function parentResultsOfBatchIs($arg)
    {
        assertCount($arg, $this->scope_array['batch_parent_requests'][0]->batch_child_requests);
    }

    protected function fixStepArgument($argument)
    {
        $arg = str_replace('\\"', '"', $argument);
        $arg =  $this->checkForTokens($arg);
        $arg =  $this->useSelector($arg);
        return $arg;
    }

    protected function useSelector($item)
    {
        $this->setUpSelector();
        if($this->tokenSelector) {
            $item = $this->tokenSelector->getSelectorFromString($item, false);
        }
        return $item;
    }

    protected function setUpSelector()
    {
        if($this->getSubcontext('BusinessSelectors')) {
            $this->tokenSelector = $this->getSubcontext('BusinessSelectors')->getSubcontext('UIBusinessSelector');
        } else {
            $this->tokenSelector = false;
        }
    }
}