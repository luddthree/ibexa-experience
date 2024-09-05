@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: As an editor I can use the default Workflow to review items

  Scenario: Content Item can be sent to review using Quick Review
    Given I open Login page in admin SiteAccess
    And I log in as "Creator" with password "Passw0rd-42"
    And I go to "Content structure" in Content tab
    And I start creating a new content "Folder"
    And I set content fields
      | label        | value             |
      | Name         | QuickReviewFolder |
    When I transition to "Send to review" with message "Please have a look and make this review quick" for reviewer "Administrator User"
    Then the dashboard review queue contains "QuickReviewFolder" draft in "Quick review" stage
    And "Drafts to review" tab in "My content" table contains "QuickReviewFolder" draft in "Quick review" stage

  Scenario: Content Item can be reviewied with Quick Review and sent for further reviewing
    Given I am logged as admin
    And I'm on Content view Page for "root"
    And there is an unread notification for current user
      And I go to user notifications
      And I go to user notification with details:
       | Type                   | Description                                                          |
       | Content review request | From: Creator Workflow Please have a look and make this review quick |
      And workflow history for "QuickReviewFolder" contains events:
       | Event                         | Message                                       |
       | Stage changed to Quick review | Please have a look and make this review quick |
    When I set content fields
        | label | value                     |
        | Name  | QuickReviewFolderRewieved |
    And I transition to "Send to review" with message "Small corrections" for reviewer "Administrator User"
    Then the dashboard review queue contains "QuickReviewFolderRewieved" draft in "Quick review" stage
    And I verify Workflow history table for "QuickReviewFolderRewieved":
      | Event                         | Message                                       |
      | Stage changed to Quick review | Small corrections                             |
      | Stage changed to Quick review | Please have a look and make this review quick |
