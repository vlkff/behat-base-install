@api @wip
Feature: Using Oauth
  Logging into the site and refreshing your token
  As a user
  So I can log in to get stuff but also refresh my token every 24 hours

  Scenario: How do I get a token
    Given I have the payload:
    """
    {
      "password": "ADMIN_PASS",
      "grant_type": "password",
      "client_id": "CLIENT_ID",
      "client_secret": "CLIENT_SECRET",
      "username": "ADMIN_USERNAME"
    }
    """
    And I request "POST /oauth/access_token"
    Then I get a "200" response

  ######
  ## You can also see in setHeader method in OauthHelper trait how I can set this in the header
  ## Once the client is done all of this will be through the client
  ######
  Scenario: How do I make an access token in one step using env settings this could be your Background step
    Given I make an access token using args "CLIENT_ID:ADMIN_USERNAME:CLIENT_SECRET:ADMIN_PASS"

  Scenario: How do I use refresh_token to get a new token
    Given I make an access token using args "CLIENT_ID:ADMIN_USERNAME:CLIENT_SECRET:ADMIN_PASS"
    Given I have the payload:
    """
    {
      "grant_type": "refresh_token",
      "client_id": "CLIENT_ID",
      "client_secret": "CLIENT_SECRET",
      "refresh_token": "REFRESH_TOKEN_FROM_STATE"
    }
    """
    And I request "POST /oauth/access_token"
    Then I get a "200" response

  Scenario: How to use the token in the query string
  Scenario: How to use the token in the header

