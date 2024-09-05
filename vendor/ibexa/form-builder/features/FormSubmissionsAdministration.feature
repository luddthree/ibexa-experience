@IbexaExperience @IbexaCommerce @javascript

Feature: Form administration
  As an administrator
  In order to manage forms on my site
  I want to add, delete and arrange fields in my forms.

  Scenario: New form with field can be published
    Given I am logged as admin
    And I'm on Content view Page for "Forms"
    And I start creating a new content "Form"
    And I set content fields
      | label | value               |
      | Title | TestSubmissionform  |
    When I start creating a new Form
    And I add the "Single line input" field to form
    And I add the "Button" field to form
    And I go back from "Form" form builder to content edit
    And I perform the "Publish" action
    Then I should be on Content view Page for "Forms/TestSubmissionform"
    And I should see form preview with fields
      | fieldName         |
      | Single line input |
      | Button            |

  Scenario Outline: We can add submissions to form
    Given I am on "/forms/TestSubmissionform"
    When I fill the form with data
        | label   | value   |
        | <label> | <value> |
      And I submit the form
      And I am logged as admin
      And I'm on Content view Page for "Forms/TestSubmissionform"
      And I switch to "Submissions" tab in Content structure
    Then I see submissions in the form
        | value   |
        | <value> |
    Then there is a total of <expectedSubmissionCount> submissions
    And I view the "<value>" submission
    And I see modal with correct submissions data
     | label   | value   |
     | <label> | <value> |

    Examples:
    | label             | value        | expectedSubmissionCount |
    | Single line input | test value 1 | 1                       |
    | Single line input | test value 2 | 2                       |
    | Single line input | test value 3 | 3                       |

  Scenario: Form submissions delete button is disabled when no submission is checked
    Given I am logged as admin
    And I'm on Content view Page for "Forms/TestSubmissionform"
    When I switch to "Submissions" tab in Content structure
    Then delete submissions button is disabled

  Scenario: Form submissions can be deleted
    Given I am logged as admin
    And I'm on Content view Page for "Forms/TestSubmissionform"
    When I switch to "Submissions" tab in Content structure
      And I see submissions in the form
        | value        |
        | test value 1 |
        | test value 2 |
        | test value 3 |
    When I delete the "test value 2" submission
    Then I see submissions in the form
        | value        |
        | test value 1 |
        | test value 3 |
      And the submissions don't exist
        | value        |
        | test value 2 |
