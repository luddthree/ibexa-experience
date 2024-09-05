@IbexaExperience @IbexaCommerce
Feature: Displaying scheduled block reveal in calendar view

  Background:
    Given I am logged as admin

  @javascript
  Scenario: Getting and counting upcoming reveal events by query
    Given I'm on Content view Page for root
    And I start creating a new Landing Page "RevealBlock"
    And I add Landing page blocks
      | blockType |
      | Code      |
    And I enter "__REVEAL_BLOCK_NAME__" as "Name" field value in the block
    And I enter "Test" as "Content" field value in the block
    And I set reveal date to a month later
    And I submit the block pop-up form
    And I publish the Landing Page
    And success notification that "Content published." appears
    And I should be on Content view Page for "RevealBlock"
    When I ask for next month block reveal events
    And I ask for quantity of next month block reveal events
    Then I receive "1" reveal event for "__REVEAL_BLOCK_NAME__" block

  @javascript
  Scenario: Getting particular reveal event by its ID
    Given I'm on Content view Page for root
    And I start creating a new Landing Page "RevealBlock2"
    And I add Landing page blocks
      | blockType |
      | Code      |
    And I enter "__REVEAL_BLOCK_NAME_2__" as "Name" field value in the block
    And I enter "Test" as "Content" field value in the block
    And I set reveal date to a month later
    And I submit the block pop-up form
    And I publish the Landing Page
    And success notification that "Content published." appears
    And I should be on Content view Page for "RevealBlock2"
    When I ask for reveal event with name "__REVEAL_BLOCK_NAME_2__" by its ID
    Then I receive proper block reveal event
