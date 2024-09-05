@IbexaExperience @IbexaCommerce @javascript
Feature: Form administration
  As an administrator
  In order to manage forms on my site
  I want to add, delete and arrange fields in my forms.

  Scenario: You can go back to form edit from form builder without adding field
    Given I am logged as admin
      And I'm on Content view Page for "Forms"
      And I start creating a new content "Form"
      And I set content fields
        | label | value     |
        | Title | Test Form |
    When I start creating a new Form
      And I go back from "Form" form builder to content edit
    Then I should be on Content update page
      And I should see empty form preview

  Scenario: You can delete Form from Form content item
    Given I am logged as admin
      And I'm on Content view Page for "Forms"
      And I start creating a new content "Form"
      And I set content fields
        | label | value      |
        | Title | <formName> |
    When I start creating a new Form
      And I add the "<fieldName>" fields to form
      | formName          | fieldName           |
      | SingleLine Form   | Single line input   |
      | MultipleLine Form | Multiple line input |
      | Number Form       | Number              |
      | Checkbox Form     | Checkbox            |
      | Checkboxes Form   | Checkboxes          |
      | Radio Form        | Radio               |
      | Dropdown Form     | Dropdown            |
      | Email Form        | Email               |
      | URL Form          | URL                 |
      | File Form         | File                |
      | Captcha Form      | Captcha             |
      | Button            | Button              |
      And I go back from "Form" form builder to content edit
      And I delete the form from Form Content Item
    Then I should be on Content update page
      And I should see empty form preview

  @javascript
  Scenario: Field can be deleted from new form
    Given I am logged as admin
      And I'm on Content view Page for "Forms"
      And I start creating a new content "Form"
      And I set content fields
        | label | value      |
        | Title | <formName> |
    When I start creating a new Form
      And I add the "<fieldName>" fields to form
        | formName          | fieldName           |
        | SingleLine Form   | Single line input   |
        | MultipleLine Form | Multiple line input |
        | Number Form       | Number              |
        | Checkbox Form     | Checkbox            |
        | Checkboxes Form   | Checkboxes          |
        | Radio Form        | Radio               |
        | Dropdown Form     | Dropdown            |
        | Email Form        | Email               |
        | URL Form          | URL                 |
        | File Form         | File                |
        | Captcha Form      | Captcha             |
        | Button            | Button              |
      And I delete the "<fieldName>" fields from form
        | formName          | fieldName           |
        | SingleLine Form   | Single line input   |
        | MultipleLine Form | Multiple line input |
        | Number Form       | Number              |
        | Checkbox Form     | Checkbox            |
        | Checkboxes Form   | Checkboxes          |
        | Radio Form        | Radio               |
        | Dropdown Form     | Dropdown            |
        | Email Form        | Email               |
        | URL Form          | URL                 |
        | File Form         | File                |
        | Captcha Form      | Captcha             |
        | Button            | Button              |
      And I go back from "Form" form builder to content edit
    Then I should be on Content update page
      And I should see empty form preview

  Scenario: New field can be added to form during editing
    Given I create "form" Content items in "Forms" in "eng-GB"
        | title              | form               | 
        | FormToEditAddField | FormToEditAddField |
      And I am logged as admin
      And I'm on Content view Page for "Forms/FormToEditAddField"
      And I should see form preview with fields
        | fieldName           |
        | FormToEditAddField  |
        | Submit              |
      And I perform the "Edit" action
    When I start editing the form
      And I add the "Checkbox" field to form
      And I go back from "FormToEditAddField" form builder to content edit
      And I perform the "Publish" action
    Then success notification that "Content published." appears
      And I should be on Content view Page for "Forms/FormToEditAddField"
      And I should see form preview with fields
        | fieldName           |
        | Checkbox            |
        | FormToEditAddField  |
        | Submit              |

  Scenario: Field can be deleted from form during edition
    Given I create "form" Content items in "Forms" in "eng-GB"
        | title                 | form                  |
        | FormToEditDeleteField | FormToEditDeleteField |
      And I am logged as admin
      And I'm on Content view Page for "Forms/FormToEditDeleteField"
      And I should see form preview with fields
        | fieldName           |
        | FormToEditDeleteField  |
        | Submit              |
      And I perform the "Edit" action
    When I start editing the form
      And I delete the "FormToEditDeleteField" field from form
      And I go back from "FormToEditDeleteField" form builder to content edit
      And I perform the "Publish" action
    Then success notification that "Content published." appears
      And I should be on Content view Page for "Forms/FormToEditDeleteField"
      And I should see form preview with fields
        | fieldName |
        | Submit    |
      And I should see form preview without fields
        | fieldName             |
        | FormToEditDeleteField |

  Scenario: Form can be deleted during form content item editing
    Given I create "form" Content items in "Forms" in "eng-GB"
        | title                  | form                   |
        | FormToDeleteDuringEdit | FormToDeleteDuringEdit |
      And I am logged as admin
      And I'm on Content view Page for "Forms/FormToDeleteDuringEdit"
      And I perform the "Edit" action
      And I delete the form from Form Content Item
      And I perform the "Publish" action
    Then success notification that "Content published." appears
      And I should be on Content view Page for "Forms/FormToDeleteDuringEdit"
      And I should see empty form preview
