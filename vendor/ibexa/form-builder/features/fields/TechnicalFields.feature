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
      | Captcha Custom Form      | Captcha             | Custom captcha       |

  Scenario: Create form with Hidden field with custom configuration
    Given I start creating a new content "Form"
    And I set content fields
      | label | value                  |
      | Title | Hidden Custom Form |
    When I start creating a new Form
    And I add the "Hidden field" field to form
    And I start configuring the "Hidden field" field
    And I set custom configuration for the "Hidden field" field
    And I confirm the "Hidden field" field configuration
    And I go back from "Form" form builder to content edit
    And I perform the "Publish" action
    Then I should be on Content view Page for "Forms/Hidden Custom Form"
    And form preview contains a hidden field
    And I perform the "Edit" action
    And I start editing the form
    And I start configuring the "Custom hidden" field
    And I should see test configuration set on "Hidden field" fields
