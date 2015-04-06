@javascript
Feature: Using Faker
  So you can make emails, username etc on the fly
  As a tester
  So I can fill in forms without repeat

  Scenario: Your First Scenario
    Given I am on "/behat_testing.html"
    And I wait
    And I fill in the "EMAIL_FIELD" with "FAKER_EMAIL"
    And I should see "Hello"
    And I wait for "3" seconds
    And I fill in the "Email address" with "foo@foo.com"
    And I wait for "10" seconds