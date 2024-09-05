@IbexaExperience @IbexaCommerce
Feature: As an Editor I want to perform basic Content Management tasks (removing, versioning) in Page Builder

  Background:
    Given I am logged as admin
    And a "landing_page" Content item named "PageBuilderActionsContainer" exists in root
      | name                        | description                 |
      | PageBuilderActionsContainer | PageBuilderActionsContainer |

#  @javascript
#  Scenario: Create an empty Landing Page and delete the draft
#    Given I open the "root" Content Item in Page Builder
#    And I start creating a new Landing Page from Page Builder named "Delete draft"
#    When I delete the draft
#    Then I should be on Content view Page for root

  @javascript
  Scenario: Create an empty Landing Page and save it as a new draft
    Given I'm on Content view Page for PageBuilderActionsContainer
    And I start creating a new Landing Page "Save draft"
    When I save the draft
    Then success notification that "Content draft saved." appears
    And I should be in Landing Page edit mode for Landing Page draft called "Save draft" of type "landing_page"

  @javascript
  Scenario: Landing Page can be previewed after saving the draft
    Given I'm on Content view Page for PageBuilderActionsContainer
    And I start creating a new Landing Page "PreviewSaveDraft"
    And I set up block "CodeBlock" "Code" with default testing configuration
    When I save the draft
    Then success notification that "Content draft saved." appears
    And I see the "CodeBlock" "Code" block and its preview
  
  @javascript
  Scenario: Delete a draft of an existing Landing Page
    Given I'm on Content view Page for PageBuilderActionsContainer
    And I start creating a new Landing Page "LandingPageDeleteDraft"
    And I publish the Landing Page
    And success notification that "Content published." appears
    And I should be on Content view Page for "PageBuilderActionsContainer/LandingPageDeleteDraft"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "PageBuilderActionsContainer/LandingPageDeleteDraft" Landing Page
    When I delete the draft
    Then I should be on Content view Page for "PageBuilderActionsContainer/LandingPageDeleteDraft"

  @javascript
  Scenario: Publish an empty Landing Page
    Given I'm on Content view Page for PageBuilderActionsContainer
    And I start creating a new Landing Page "VersionsLandingPage"
    When I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "PageBuilderActionsContainer/VersionsLandingPage"

  @javascript
  Scenario: Test the removal of draggable blocks from the landing page dropzone
    Given I'm on Content view Page for PageBuilderActionsContainer
    And I start creating a new Landing Page "VariousBlocks"
    And I add a series of Landing page blocks
      | blockType    |
      | Embed        |
      | Code         |
      | RSS          |
      | Video        |
      | Gallery      |
      | Banner       |
      | Content List |
      | Text         |
    When I remove Landing page blocks
      | blockName         |
      | Code #1:         |
      | RSS #1:          |
      | Video #1:        |
      | Gallery #1:      |
      | Banner #1:       |
      | Content List #1: |
      | Text #1:         |
      | Embed #1:        |
    Then there are no blocks displayed

  @javascript
  Scenario: I can see the preview of upcoming event
    Given I'm on Content view Page for PageBuilderActionsContainer
    And I start creating a new Landing Page "RevealBlock"
    And I add Landing page blocks
      | blockType |
      | Code      |
    And I enter "TestBlock" as "Name" field value in the block
    And I enter "Test" as "Content" field value in the block
    And I select "Default block layout" block layout
    And I set reveal date to a month later
    And I submit the block pop-up form
    When I open the timeline
    And I go to scheduled upcoming event
      | position  | event        |
      | 1         | Reveal block |
    And I close the timeline
    Then I am previewing Content in the future
    And I see the "TestBlock" "Code" block and its preview
      | parameter1   |
      | Test         |

  @javascript
  Scenario: I can check length assertion of Name field in Code block
    Given I'm on Content view Page for PageBuilderActionsContainer
    And I start creating a new Landing Page "NameFieldLengthAssertionBlock"
    And I add Landing page blocks
      | blockType |
      | Code      |
    When I enter "257charactersstringG46SyFS7HzkzLnaqwW7K8vCL2KQi0Lwgu06q6Ggp9M4artRQWv5Tdd9JvZYfAU959zRQTVFGvx1dw7wpAu4EtjkXRiVEezxqJQW76MUGhPGQ8gjgdqL3n2SCBaVM5VmnWLJ9vr7Uk8kmDaEc7XYkU0Cc7mHfvnbqdEa0nubuZgNpnKU3WR3TWwj9DDtaidbmeDFpjFK9uzKMBdJMSX3HQQDfarAv4GJ1R7eQtez8U6GX1T" as "Name" field value in the block
    And I enter "Test" as "Content" field value in the block
    Then I submit the block pop-up form with Name field length assertion

  @javascript
  Scenario: Edit existing Landing Page multiple times
    Given I'm on Content view Page for "PageBuilderActionsContainer/VersionsLandingPage"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "PageBuilderActionsContainer/VersionsLandingPage" Landing Page
    And success notification that "Created a new draft for 'VersionsLandingPage'." appears
    And I set up block "CodeBlock" "Code" with default testing configuration
    And I see the "CodeBlock" "Code" block and its preview
    When I publish the Landing Page
    And success notification that "Content published." appears
    And I should be on Content view Page for "PageBuilderActionsContainer/VersionsLandingPage"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "PageBuilderActionsContainer/VersionsLandingPage" Landing Page
    And success notification that "Created a new draft for 'VersionsLandingPage'." appears
    And I set up block "VideoBlock" "Video" with default testing configuration
    And I see the "VideoBlock" "Video" block and its preview
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "PageBuilderActionsContainer/VersionsLandingPage"

  @javascript
  Scenario: Create a Draft of an existing Page
    Given I'm on Content view Page for "PageBuilderActionsContainer/VersionsLandingPage"
    And I perform the "Edit" action
    And I should be in Landing Page Edit mode for "PageBuilderActionsContainer/VersionsLandingPage" Landing Page
    When I save the draft
    Then success notification that "Content draft saved." appears

#  @javascript
#  Scenario: It's possible to preview Landing Page versions
#    Given I open the "VersionsLandingPage" Content Item in Page Builder
#    And I open Versions table
#    And I see versions data
#    | version | labels            |
#    | 1       | Archived          |
#    | 2       | Archived          |
#    | 3       | Published,Viewing |
#    | 4       | Draft             |
#    When I preview version 1
#    And I open versions table
#    Then I see versions data
#      | version | labels            |
#      | 1       | Archived,Viewing  |
#      | 2       | Archived          |
#      | 3       | Published         |
#      | 4       | Draft             |

#  @javascript
#  Scenario: Landing Page can be sent to Trash
#    Given I open the "VersionsLandingPage" Content Item in Page Builder
#    When I send the to Trash from Page Builder
#    And success notification that "Location 'VersionsLandingPage' moved to Trash." appears
#    Then I should be viewing "root" in Page Editor
#    And I open "Trash" page in admin SiteAccess
#    And there is a "Landing page" "VersionsLandingPage" on Trash list
