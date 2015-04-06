<?php

use Behat\Gherkin\Node\PyStringNode;

trait OauthHelper {


    protected $access_token;


    /**
     * @Given I make an access token
     */
    public function iMakeAnAccessToken()
    {
        //@TODO set up seed for this?
        $this->requestPayload = new PyStringNode([
            "{",
            '"password": "' . $this->passWord . '",',
            '"grant_type": "password",',
            '"client_id": "client1id",',
            '"client_secret": "client1secret",',
            '"username": "' . $this->userName . '"',
            "}"
        ], 0);

        $this->iRequest("POST", "/oauth/access_token");
        assertEquals(200, $this->response->getStatusCode(), sprintf("Error Getting Response Token %s", $this->response->getReasonPhrase()));
        $this->setTokenFromResponse();

    }

    protected function setTokenFromResponse()
    {
        $response   = $this->getResponse();

        $body       = json_decode($response->getBody(), true);

        $this->setAccessToken($body['access_token']);
    }

    protected function checkForAccessToken()
    {
        $resource =  str_replace('TOKEN_REPLACE', $this->getAccessToken(), $this->resource);
        $this->resource = $resource;
        $this->setHeader();
    }

    /**
     * POST needs this
     */
    protected function setHeader()
    {
        $this->client->setDefaultOption('headers', ['Authorization' => "Bearer {$this->getAccessToken()}"]);
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @param mixed $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }
}