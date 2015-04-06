@wip
Feature: Reset Mock Data
  I should be able to reset mock data
  As a tester
  So I can have predictable data


  Background: Login
    Given I do basic auth

  Scenario: Update Mock
    Given I clean out "App\User" with id of "mock-dmp-1"
    Given I mock "App\User" with properties:
    """
    id: "mock-non-dmp-1"
    dmp_id: "mock-dmp-1"
    email: "mock-dmp-1@foo.com"
    """
    Given I have the payload:
    """
      { "data":
        {
           "name": "UPDATED PROJECT",
           "id": "FAKER_UUID",
           "site_id": "mock-site",
           "team_id": "cloud-apps-team",
           "branch": "development",
           "folder": "tests/behat",
           "site_object": {
             "id": "mock-site",
             "name": "Mock Site",
             "repo_name": "cccc_test",
             "active": 1,
             "account_name": "FAKER_USERNAME"
           },
           "urls": [
            {"name": "Url 1 Behat", "path": "FAKER_URL" },
            {"name": "Url 2 Behat", "path": "FAKER_URL", "default": "1" }
           ]
         }
       }
    """
    When I request "PUT /api/v1/projects/mock-project"
    Then I get a "200" response
    When I request "GET /api/v1/projects/mock-project"
    Then I get a "200" response
    And scope into the "data.name" property
    Then the "data.name" property equals "UPDATED PROJECT"
