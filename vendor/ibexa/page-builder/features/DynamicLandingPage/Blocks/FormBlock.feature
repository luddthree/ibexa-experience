@IbexaExperience @IbexaCommerce
Feature: Basic interaction tests for Form Block

  @javascript @APIUser:admin
  Scenario: Form block can be added to Page Builder and published
    Given I create "form" Content items in "Forms" in "eng-GB"
      | title             |
      | FormToEmbed       |
    And a "folder" Content item named "FormBlockContainer" exists in root
      | name               | short_name         |
      | FormBlockContainer | FormBlockContainer |
    And I am logged as admin
    Given I'm on Content view Page for FormBlockContainer
    And I start creating a new Landing Page "FormBlock"
    When I set up block "FormBlock" "Form" with default testing configuration
    And I see the "FormBlock" "Form" block and its preview
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "FormBlockContainer/FormBlock"


  @javascript
  Scenario: Page with each block can be edited and published again
    Given I am logged as admin
    And I'm on Content view Page for "FormBlockContainer/FormBlock"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "FormBlockContainer/FormBlock" Landing Page
    And I see the "FormBlock" "Form" block and its preview
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "FormBlockContainer/FormBlock"

  @javascript
  Scenario: We can add submissions to form
    Given I am on "/forms/formtoembed"
    When I fill the form with data
      | label  | value      |
      | Test   | test value |
    And I submit the form
    And I am logged as admin
    And I'm on Content view Page for "Forms/FormToEmbed"
    And I switch to "Submissions" tab in Content structure
    Then I see submissions in the form
      | value      |
      | test value |
    And there is a total of 1 submissions
