@api @wip
Feature: Sites
  This sites admin area
  As an authenticated user
  I should be able to see the sites I am on or admin them if I have that role

  Background: Login
    Given I make an access token

  Scenario: Get Site
    When I request "GET /api/v1/sites/mock-site-1?access_token=TOKEN_REPLACE"
    Then I get a "200" response
    And scope into the "data" property
    And the properties exist:
    """
    name
    base_url
    dmp_id
    site_override
    domains
    """

  Scenario: I should get one site via DMP id
    Given I clean out "App\Sites\Site" with id of "mock-dmp-1"
    Given I mock "App\Sites\Site" with properties:
    """
    id: "mock-non-dmp-1"
    name: "Mock Via DMP"
    base_url: "foo.com"
    dmp_id: "mock-dmp-1"
    """
    When I request "GET /api/v1/sites/mock-dmp-1?dmp=true&access_token=TOKEN_REPLACE"
    Then I get a "200" response
    And scope into the "data" property
    And the properties exist:
    """
    name
    base_url
    name
    """

  Scenario: Get Site with Override
    When I request "GET /api/v1/sites/mock-site-2?access_token=TOKEN_REPLACE"
    Then I get a "200" response
    And scope into the "data" property
    And the "dev_domain" property has the value "dev_domain-override.com"

  Scenario: Show Sites main urls are being overridden
    When I request "GET /api/v1/sites/mock-site-2?access_token=TOKEN_REPLACE"
    Then I get a "200" response
    And scope into the "data.site_override" property
    And the "dev_domain" property has the value "dev_domain-override.com"

  Scenario: Get Site All eg does not consider user
    When I request "GET /api/v1/sites/all?access_token=TOKEN_REPLACE"
    Then I get a "200" response
    And scope into the "data.0" property
    And the properties exist:
    """
    name
    base_url
    dmp_id
    """

  Scenario: Get Site Using Pagination Page 2
    When I request "GET /api/v1/sites/all?page=2&access_token=TOKEN_REPLACE"
    Then I get a "200" response
    And scope into the "pagination" property
    And the "current_page" property has the value "2"

  Scenario: Get Site All eg does not consider user but can use pagination
    When I request "GET /api/v1/sites/all?access_token=TOKEN_REPLACE"
    Then I get a "200" response
    And scope into the "data.0" property
    And the properties exist:
    """
    name
    base_url
    dmp_id
    """

  Scenario: Can Create a Site
    Given I have the payload:
    """
      {
         "name": "New Site via POST",
         "account_name": "foo_bar",
         "repo_name": "foo_bar",
         "active": 1,
         "live_domain": "foo",
         "stg_domain": "foo",
         "dev_domain": "foo",
         "prod_domain": "foo"
       }
    """
    When I request "POST /api/v1/sites?access_token=TOKEN_REPLACE"
    Then I get a "200" response

  Scenario: Update or Create Override on Site
    Given I have the payload:
    """
       {
           "live_domain": "foo_live2",
           "stg_domain": "foo_stage2",
           "dev_domain": "foo_dev2",
           "active": "1",
           "site_id": "sst-site"
       }
     """
    When I request "POST /api/v1/sites/mock-site-1/site-overrides?access_token=TOKEN_REPLACE"
    Then I get a "200" response


  Scenario: Update a Site
    Given I have the payload:
    """
      {
           "id": "mock-site-1",
           "name": "New Site via POST UPDATED",
           "account_name": "foo_bar",
           "repo_name": "foo_bar",
           "active": 1,
           "live_domain": "foo",
           "stg_domain": "foo",
           "dev_domain": "foo",
           "prod_domain": "foo"
       }
    """
    When I request "PUT /api/v1/sites/mock-site-1?access_token=TOKEN_REPLACE"
    Then I get a "200" response

