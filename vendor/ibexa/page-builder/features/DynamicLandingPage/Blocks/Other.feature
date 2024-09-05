@IbexaExperience @IbexaCommerce
Feature: Basic tests for Page Builder blocks

  Background:
        Given I am logged as admin
        And a "folder" Content item named "OtherBlocksContainer" exists in root
          | name                 | short_name           |
          | OtherBlocksContainer | OtherBlocksContainer |

    @javascript
    Scenario Outline: Block can be added to Page Builder and published
      Given I'm on Content view Page for OtherBlocksContainer
      And I start creating a new Landing Page "<blockName>"
      When I set up block "<blockName>" "<blockType>" with default testing configuration
      And I see the "<blockName>" "<blockType>" block and its preview
      And I publish the Landing Page
      Then success notification that "Content published." appears
      And I should be on Content view Page for "OtherBlocksContainer/<blockName>"

      Examples:
              | blockName             | blockType         |
              | RSSBlock              | RSS               |
              | CodeBlock             | Code              |
              | TextBlock             | Text              |

  @javascript
  Scenario Outline: Page with each block can be edited and published again
    Given I'm on Content view Page for "OtherBlocksContainer/<pageName>"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "OtherBlocksContainer/<pageName>" Landing Page
    And I see the "<blockName>" "<blockType>" block and its preview
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "OtherBlocksContainer/<pageName>"

    Examples:
      | pageName              | blockType         |
      | RSSBlock              | RSS               |
      | CodeBlock             | Code              |
      | TextBlock             | Text              |
