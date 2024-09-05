@javascript @IbexaExperience @IbexaCommerce
Feature: As an editor I can execute actions related to Workflow from Page Builder

  Scenario: Content Item can be moved in Workflow from Page Editor
    Given I open Login page in admin SiteAccess
    And I log in as "Creator" with password "Passw0rd-42"
    And I go to "Content structure" in Content tab
    And I start creating a new Landing Page "Creator Page" of type "CustomWorkflowContentTypeWithPage"
    And I set up block "CodeBlockName" "Code" with default testing configuration
    And the "To done" button is not visible
    When I transition to "To review" with message "Please have a look at this page" for reviewer "Administrator User"
    Then I should be on Dashboard page
    And the dashboard review queue contains "Creator Page" draft in "Technical review" stage
    And "Drafts to review" tab in "My content" table contains "Creator Page" draft in "Technical review" stage

  Scenario: Content Item with Page fieldtype can be reviewied from notifications panel
    Given I am logged as admin
    And I'm on Content view Page for "root"
    And there is an unread notification for current user
      And I go to user notifications
      And I go to Landing Page draft called "Creator Page" of type "custom_workflow_page" with user notification with details:
       | Type                    | Description                                            |
       | Content review request  | From: Creator Workflow Please have a look at this page |
      And workflow history contains events for viewed Page "Creator Page"
       | Event                             | Message                         |
       | Stage changed to Technical review | Please have a look at this page |
      And I set Landing Page properties
        | field       | value                   |
        | Title       | Corrected by Admin Page |
        | Description | Corrected by Admin Page |
      And I set up block "Text" "Text" with default testing configuration
      And I transition to "To done" with message "Page ready to be published"
    Then I should be on Dashboard page
    And the dashboard review queue contains "Corrected by Admin Page" draft in "Done" stage

  Scenario: Content Item with Page fieldtype can be published and disappears from Workflow queue
    Given I open Login page in admin SiteAccess
    And I log in as "Publisher" with password "Passw0rd-42"
    And I verify Workflow history table for "Corrected by Admin Page":
      | Event                             | Message                         |
      | Stage changed to Done             | Page ready to be published      |
      | Stage changed to Technical review | Please have a look at this page |
    And I start reviewing Landing Page draft called "Corrected by Admin Page" of type "custom_workflow_page"
    And workflow history contains events for viewed Page "Corrected by Admin Page"
      | Event                             | Message                         |
      | Stage changed to Done             | Page ready to be published      |
      | Stage changed to Technical review | Please have a look at this page |
    And I set Landing Page properties
        | field       | value             |
        | Title       | Publisher Changes |
    And I transition to "To publish" with message "Good job!"
    Then I should be on Dashboard page
    And the Dashboard review queue does not contain an item named "CustomWorkflowContentTypeWithPage"
