@javascript
Feature: Testing
  Showing the Faker Feature in action
  As a tester
  I can use these FAKER tokens to make up random content

  Scenario: Testing Faker Feature
    Given I am on "/behat_testing.html"
    Then I fill in "Email address" with "FAKER_EMAIL"
    Then I fill in "Text Area Example" with "FAKER_PARAGRAPH"
    Then I fill in "Text Area Example" with "FAKER_USERNAME"
    And I wait for "2" seconds
    Then I fill in "Text Area Example" with "FAKER_URL"
    And I wait for "2" seconds
    Then I fill in "Text Area Example" with "FAKER_UUID"
    And I wait for "5" seconds
