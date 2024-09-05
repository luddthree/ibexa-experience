@javascript @IbexaExperience @IbexaCommerce
Feature: As an administrator I can execute Workflow transitions for users with added limitations

  @javascript
  Scenario: User can create content item with limitations and edit permitted fields
    Given I open Login page in admin SiteAccess
    And I log in as "limitationEditor_fieldGroup" with password "Passw0rd-42"
    And I should be on Dashboard page
    And I go to "Content structure" in Content tab
    And I start creating a new Content "CustomWorkflowCT_limitation_fieldGroup"
    And I set content fields
      | label | value           |
      | Name  | Limitation test |
    When I switch to "Meta" field tab
    And the "Description" field cannot be edited due to limitation
    Then I transition to "Send to review" with message "Sending to Admin for review" for reviewer "Administrator User"

  @javascript
  Scenario: Content item can be assigned through workflow to user with limitations on field group
    Given I am logged as admin
    And I'm on Content view Page for "root"
    And I start creating a new Content "CustomWorkflowCT_limitation_fieldGroup"
    And I set content fields
      | label | value            |
      | Name  | Limitation test2 |
    When I transition to "Send to review" with message "Sending to limitationEditor_fieldGroup user for review" for reviewer "limitationEditor_fieldGroup limitationWorkflow"
    Then the dashboard review queue contains "Limitation test2" draft in "Quick review" stage

  @javascript
  Scenario: Content item with field group and language limitations can be sent to reviewer, for whom both those limitations are validated positively.
    Given I am logged as admin
    And I'm on Content view Page for "root"
    When I start creating a new Content "CustomWorkflowCT_limitation_fieldGroup" in "German" language
    And I set content fields
      | label | value            |
      | Name  | Limitation test3 |
    And I transition to "Send to review" with message "Sending to limitationEditor_fg_lang user for review" for reviewer "limitationEditor_fg_lang limitationWorkflow"
    Then the dashboard review queue contains "Limitation test3" draft in "Quick review" stage

  @javascript
  Scenario: Content item with field group and language limitations cannot be sent to reviewer, for whom field group limitation is validated, but language limitation is validated negatively.
    Given I am logged as admin
    And I'm on Content view Page for "root"
    When I start creating a new Content "CustomWorkflowCT_limitation_fieldGroup" in "French" language
    And I set content fields
      | label | value            |
      | Name  | Limitation test4 |
    Then User "limitationEditor_fg_lang limitationWorkflow" cannot be chosen as reviewer for "Send to review" workflow transition
