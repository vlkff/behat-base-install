@javascript @wip @need_2_get_working_on_codeship_works_locally
Feature: Using Tokens
  So a user can reuse a test
  As a tester
  Tokens can be used for different sites, etc

  Scenario: Your First Scenario
    Given I am on "/behat_testing.html"
    And I wait
    And I fill in the "EMAIL_FIELD" with "FAKER_EMAIL"
    And I should see "Hello"
    And I wait for "3" seconds
    And I fill in the "Email address" with "foo@foo.com"
    And I wait for "10" seconds