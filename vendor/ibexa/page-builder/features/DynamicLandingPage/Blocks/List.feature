@IbexaExperience @IbexaCommerce
Feature: Basic tests for Page Builder blocks

  Background:
        Given I am logged as admin
        And a "folder" Content item named "ListBlocksContainer" exists in root
          | name                | short_name          |
          | ListBlocksContainer | ListBlocksContainer |

    @javascript
    Scenario Outline: Block can be added to Page Builder and published
      Given I'm on Content view Page for ListBlocksContainer
      And I start creating a new Landing Page "<blockName>"
      When I set up block "<blockName>" "<blockType>" with default testing configuration
      And I see the "<blockName>" "<blockType>" block and its preview
      And I publish the Landing Page
      Then success notification that "Content published." appears
      And I should be on Content view Page for "ListBlocksContainer/<blockName>"

      Examples:
              | blockName             | blockType         |
              | ContentListBlock      | Content List      |
              | CollectionBlock       | Collection        |
              | ContentSchedulerBlock | Content Scheduler |

  @javascript
  Scenario Outline: Page with each block can be edited and published again
    Given I'm on Content view Page for "ListBlocksContainer/<pageName>"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "ListBlocksContainer/<pageName>" Landing Page
    And I see the "<blockName>" "<blockType>" block and its preview
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "ListBlocksContainer/<pageName>"

    Examples:
      | pageName              | blockType         |
      | ContentListBlock      | Content List      |
      | CollectionBlock       | Collection        |
      | ContentSchedulerBlock | Content Scheduler |
