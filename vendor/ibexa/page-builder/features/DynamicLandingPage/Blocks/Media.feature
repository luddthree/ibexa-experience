@IbexaExperience @IbexaCommerce
Feature: Basic tests for Page Builder blocks

  Background:
        Given I am logged as admin
        And a "folder" Content item named "MediaBlocksContainer" exists in root
          | name                 | short_name           |
          | MediaBlocksContainer | MediaBlocksContainer |

    @javascript
    Scenario Outline: Block can be added to Page Builder and published
      Given I'm on Content view Page for MediaBlocksContainer
      And I start creating a new Landing Page "<blockName>"
      When I set up block "<blockName>" "<blockType>" with default testing configuration
      And I see the "<blockName>" "<blockType>" block and its preview
      And I publish the Landing Page
      Then success notification that "Content published." appears
      And I should be on Content view Page for "MediaBlocksContainer/<blockName>"

      Examples:
              | blockName             | blockType         |
              | VideoBlock            | Video             |
              | GalleryBlock          | Gallery           |
              | EmbedBlock            | Embed             |
 
    @javascript @admin
    Scenario: 'Banner' block with image can be added to Page Builder and published
      Given I create "image" Content items in "/Media/Images/" in "eng-GB"
        | name         | image                                                        |
        | BannerImage  | vendor/ibexa/behat/src/lib/Data/Images/small2.jpg  |
      And I'm on Content view Page for MediaBlocksContainer
      And I start creating a new Landing Page "BannerBlock"
      When I set up block "BannerBlock" "Banner" with default testing configuration
      And I see the "BannerBlock" "Banner" block and its preview
      And I publish the Landing Page
      Then success notification that "Content published." appears
      And I should be on Content view Page for "MediaBlocksContainer/BannerBlock"

  @javascript
  Scenario Outline: Page with each block can be edited and published again
    Given I'm on Content view Page for "MediaBlocksContainer/<pageName>"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "MediaBlocksContainer/<pageName>" Landing Page
    And I see the "<blockName>" "<blockType>" block and its preview
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "MediaBlocksContainer/<pageName>"

    Examples:
      | pageName              | blockType         |
      | VideoBlock            | Video             |
      | GalleryBlock          | Gallery           |
      | BannerBlock           | Banner            |
      | EmbedBlock            | Embed             |
