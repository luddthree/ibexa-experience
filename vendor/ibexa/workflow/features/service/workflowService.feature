@IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: Workflow management

  @admin
  Scenario: Load ongoing workflow
    Given I create "article" Content items in root in "eng-GB"
      | title              | short_title     | intro           |
      | Test Article       | Test Article    | Lorem ipsum     |
      | Trashed Article    | Trashed Article | Lorem ipsum     |
    And I transition "Test Article" through "to_review"
    And I transition "Trashed Article" through "to_review"
    When I send "Trashed Article" to the Trash
    Then Workflow data does not contain Trashed items

  Scenario: Load ongoing workflow originated by User
    Given I am using the API as "admin"
    And I create "article" Content items in root in "eng-GB"
      | title               | short_title       | intro           |
      | Test Article 2      | Test Article 2    | Lorem ipsum     |
      | Trashed Article 2   | Trashed Article 2 | Lorem ipsum     |
    And I transition "Test Article 2" through "to_review"
    And I transition "Trashed Article 2" through "to_review"
    When I send "Trashed Article 2" to the Trash
    Then Workflow data originated by user "Admin" does not contain Trashed items
