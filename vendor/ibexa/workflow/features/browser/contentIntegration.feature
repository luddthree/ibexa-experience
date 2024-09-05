@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: As an editor I can execute actions related to Workflow from AdminUI

  Scenario: Content Item can be moved in Workflow
    Given I open Login page in admin SiteAccess
    And I log in as "Creator" with password "Passw0rd-42"
    And I go to "Content structure" in Content tab
    And I start creating a new content "CustomWorkflowContentType"
    And I set content fields
      | label        | value                       |
      | Name         | First iteration             |
    And the "To done" button is not visible
    When I transition to "To review" with message "Please have a look" for reviewer "Administrator User"
    Then the dashboard review queue contains "First iteration" draft in "Technical review" stage
   And "Drafts to review" tab in "My content" table contains "First iteration" draft in "Technical review" stage

  Scenario: Content Item can be reviewied from notifications panel
    Given I am logged as admin
    And I'm on Content view Page for "root"
    And there is an unread notification for current user
      And I go to user notifications
      And I go to user notification with details:
       | Type                   | Description                               |
       | Content review request | From: Creator Workflow Please have a look |
      And workflow history for "First iteration" contains events:
       | Event                             | Message            |
       | Stage changed to Technical review | Please have a look |
    When I set content fields
        | label       | value            |
        | Name        | Second iteration |
        | Description | TestDescription  |
      And I transition to "To done" with message "Ready to be published"
    Then the dashboard review queue contains "Second iteration" draft in "Done" stage

  Scenario: Content Item can be published and disappears from Workflow queue
    Given I open Login page in admin SiteAccess
    And I log in as "Publisher" with password "Passw0rd-42"
    And I verify Workflow history table for "Second iteration":
      | Event                             | Message               |
      | Stage changed to Done             | Ready to be published |
      | Stage changed to Technical review | Please have a look    |
    When I start reviewing "Second iteration" item
    And workflow history for "Second iteration" contains events:
      | Event                             | Message               |
      | Stage changed to Done             | Ready to be published |
      | Stage changed to Technical review | Please have a look    |
    And I set content fields
      | label | value           |
      | Name  | Third iteration |
    When I transition to "To publish" with message "Good job!"
   Then the Dashboard review queue does not contain an item named "Third iteration"
    And there exists Content view Page for "Third iteration"
    And content attributes equal
      | label       | value           |
      | Name        | Third iteration |
      | Description | TestDescription |

  Scenario: Go to Admin and see Workflow
    Given I am logged as admin
    And I'm on Content view Page for "root"
    And I go to "Workflow" in Admin tab
    And there is a "Custom Workflow" workflow with Stages "Draft,Technical review,Done,Publish"
    When I go to "Custom Workflow" details
    Then there is a workflow diagram
    And the item "Third iteration" is in "Publish" stage
