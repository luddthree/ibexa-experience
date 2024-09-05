@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: As an editor I can utilize reverse transition to return to previous stage in workflow

  Scenario: Reverse transition can be used in workflow on content item and it is properly displayed in dashboard
    Given I open Login page in admin SiteAccess
    And I log in as "reverseTransitionEditor" with password "Passw0rd-42"
    And I should be on Dashboard page
    And I go to "Content structure" in Content tab
    And I start creating a new Content "CustomWorkflowContentType_reverse"
    And I set content fields
      | label        | value                   |
      | Name         | Reverse transition test |
      | Description  | test                    |
    And I transition to "Go to the end!" with message "Going to the end." for reviewer "Administrator User"
    And the dashboard review queue contains "Reverse transition test" draft in "End" stage
    And I log out of back office
    And I open Login page in admin SiteAccess
    And I log in as admin with password publish
    And I should be on Dashboard page
    And the dashboard review queue contains "Reverse transition test" draft in "End" stage
    And I start reviewing "Reverse transition test" item
    And I transition to "Go to the start!" with message "Going to the start." for reviewer "Administrator User"
    When the dashboard review queue contains "Reverse transition test" draft in "Start" stage
    And I log out of back office
    And I open Login page in admin SiteAccess
    And I log in as "reverseTransitionEditor" with password "Passw0rd-42"
    And I should be on Dashboard page
    Then the dashboard review queue contains "Reverse transition test" draft in "Start" stage
