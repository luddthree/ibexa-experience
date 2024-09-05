@IbexaExperience @IbexaCommerce
Feature: Targeting Block

  Background:
    Given I am logged as admin
    And I'm on Content view Page for root
    And I start creating a new Landing Page "Targeting"
    And I add Landing page blocks
      | blockType  |
      | Targeting |

  @javascript
  Scenario: Add a Targeting block to a new Landing Page
    Given I set "Media/Images" as default Content Item to the Targeting block
    And I add "Forms" Content Item to "test_segment_pb_1" Segment in Targeting block
    And I submit the block pop-up form
    And I see the "Targeting" "Targeting" block and its preview
      | parameter |
      | Images    |
    And I open the visibility tab
    When I select "test_segment_pb_1" Segment under Segment Group in visibility tab
    And I close the visibility tab
    Then I see the "Forms" "Targeting" block and its preview
      | parameter |
      | Forms     |
