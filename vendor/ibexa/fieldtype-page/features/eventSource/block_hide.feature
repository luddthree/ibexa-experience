@IbexaExperience @IbexaCommerce
Feature: Displaying scheduled block hide in calendar view

  Background:
    Given I am logged as admin

  @javascript
  Scenario: Getting and counting upcoming hide events by query
    Given I'm on Content view Page for root
    And I start creating a new Landing Page "HideBlock"
    And I add Landing page blocks
      | blockType |
      | Code      |
    And I enter "__HIDE_BLOCK_NAME__" as "Name" field value in the block
    And I enter "Test" as "Content" field value in the block
    And I set hide date to a month later
    And I submit the block pop-up form
    And I publish the Landing Page
    And success notification that "Content published." appears
    And I should be on Content view Page for "HideBlock"
    When I ask for next month block hide events
    And I ask for quantity of next month block hide events
    Then I receive "1" hide event for "__HIDE_BLOCK_NAME__" block

  @javascript
  Scenario: Getting particular hide event by its ID
    Given I'm on Content view Page for root
    And I start creating a new Landing Page "HideBlock2"
    And I add Landing page blocks
      | blockType |
      | Code      |
    And I enter "__HIDE_BLOCK_NAME_2__" as "Name" field value in the block
    And I enter "Test" as "Content" field value in the block
    And I set hide date to a month later
    And I submit the block pop-up form
    And I publish the Landing Page
    And success notification that "Content published." appears
    And I should be on Content view Page for "HideBlock2"
    When I ask for hide event with name "__HIDE_BLOCK_NAME_2__" by its ID
    Then I receive proper block hide event
