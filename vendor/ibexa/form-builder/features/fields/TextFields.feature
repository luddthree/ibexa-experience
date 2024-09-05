@IbexaExperience @IbexaCommerce @javascript
Feature: Form field configuration
  As an administrator
  In order to manage forms on my site
  I want to test basic fields configuration in my forms.

  Background:
    Given I am logged as admin
    And I'm on Content view Page for "Forms"

  Scenario Outline: Create form with field with custom configuration
    Given I start creating a new content "Form"
      And I set content fields
        | label | value                  |
        | Title | <formName> |
    When I start creating a new Form
      And I add the "<fieldType>" field to form
      And I start configuring the "<fieldType>" field
      And I set custom configuration for the "<fieldType>" field
      And I confirm the "<fieldType>" field configuration
      And I go back from "Form" form builder to content edit
      And I perform the "Publish" action
    Then success notification that "Content published." appears
      And I should be on Content view Page for "Forms/<formName>"
      And I should see form preview with fields
        | fieldName   |
        | <fieldName> |
      And I perform the "Edit" action
      And I start editing the form
      And I start configuring the "<fieldName>" field
      And I should see test configuration set on "<fieldType>" fields

    Examples:
      | formName                 | fieldType           | fieldName            |
      | SingleLine Custom Form   | Single line input   | Custom single line   |
      | MultipleLine Custom Form | Multiple line input | Custom multiple line |
      | Email Custom Form        | Email               | Custom email         |
      | URL Custom Form          | URL                 | Custom URL           |
