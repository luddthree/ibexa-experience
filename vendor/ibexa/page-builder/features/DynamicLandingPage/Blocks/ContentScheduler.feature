@IbexaExperience @IbexaCommerce
Feature: Content Scheduler

  Background:
    Given I am logged as admin
    And a "folder" Content item named "ContentSchedulerContainer" exists in root
    | name                      | short_name                |
    | ContentSchedulerContainer | ContentSchedulerContainer |

  @javascript
  Scenario: I can delete items from Content Scheduler block
    Given I'm on Content view Page for ContentSchedulerContainer
    And I start creating a new Landing Page "Content Scheduler"
    And I add Landing page blocks
      | blockType         |
      | Content Scheduler |
    And I add Content Items to the Content Scheduler block
      | item             |
      | Media/Files      |
      | Media/Images     |
      | Media/Multimedia |
    When I delete the "Images" item from Content Scheduler block
    And I submit the block pop-up form
    And I see the "Content Scheduler" "Content Scheduler" block and its preview
      | parameter1       |
      | Multimedia,Files |

  @javascript
  Scenario: I can reorder items in Content Scheduler block
    Given I'm on Content view Page for ContentSchedulerContainer
    And I start creating a new Landing Page "Content Scheduler"
    And I add Landing page blocks
      | blockType         |
      | Content Scheduler |
    And I add Content Items to the Content Scheduler block
      | item             |
      | Media/Files      |
      | Media/Images     |
      | Media/Multimedia |
    When I move item "Multimedia" behind the item "Files" in Content Scheduler block
    And I move item "Files" behind the item "Multimedia" in Content Scheduler block
    And I submit the block pop-up form
    Then I see the "Content Scheduler" "Content Scheduler" block and its preview
      | parameter1              |
      | Images,Multimedia,Files |

  @javascript
  Scenario: I can change Content Scheduler's item airtime
    Given I'm on Content view Page for ContentSchedulerContainer
    And I start creating a new Landing Page "ContentSchedulerAirtime"
    And I add Landing page blocks
      | blockType         |
      | Content Scheduler |
    And I add Content Items to the Content Scheduler block
      | item             |
      | Media/Files      |
      | Media/Images     |
    When I change airtime of "Files" item to a month later
    And I submit the block pop-up form
    Then I see the "Content Scheduler" "Content Scheduler" block and its preview
      | parameter1 |
      | Images     |
    And I publish the Landing Page
    And I should be on Content view Page for "ContentSchedulerContainer/ContentSchedulerAirtime"

#  @javascript
#  Scenario: I can navigate the Timeline in View mode
#    Given I open the "ContentSchedulerAirtime" Content Item in Page Builder
#    And I should be viewing "ContentSchedulerAirtime" in Page Editor
#      | blockType         | parameter1 | parameter2 |
#      | Content Scheduler | Images     |            |
#    When I open the timeline
#    And I go to scheduled upcoming event
#      | position  | event                                             |
#      | 1         | Folder 'Files' added to block Content Scheduler |
#    Then I am previewing Content in the future
#    And I should be viewing "ContentSchedulerAirtime" in Page Editor
#        | blockType         | parameter1   | parameter2 |
#        | Content Scheduler | Files,Images | default    |

  @javascript
  Scenario: I can navigate the Timeline in Edit mode
    Given I'm on Content view Page for "ContentSchedulerContainer/ContentSchedulerAirtime"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "ContentSchedulerContainer/ContentSchedulerAirtime" Landing Page
    And I see the "Content Scheduler" "Content Scheduler" block and its preview
      | parameter1 |
      | Images     |
    When I open the timeline
    And I go to scheduled upcoming event
      | position  | event                                             |
      | 1         | Folder 'Files' added to block Content Scheduler |
    Then I am previewing Content in the future
    And I see the "Content Scheduler" "Content Scheduler" block and its preview
      | parameter1   |
      | Files,Images |
