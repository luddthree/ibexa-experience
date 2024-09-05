@javascript @subtreeEditor @IbexaExperience @IbexaCommerce
Feature: Verify that an Editor with Subtree limitations can perform all his tasks in Page Builder

  Background:
    Given I open Login page in admin SiteAccess
    And I log in as "SubtreeEditor" with password "Passw0rd-42"
    And I should be on "Dashboard" page
    And I go to "Content structure" in "Content" tab

  Scenario Outline: I can create and publish Landing Pages in locations I'm allowed
    Given I navigate to content "<parentContentItemName>" of type "DedicatedFolder" in "<contentPath>"
    And I should be on Content view Page for "<contentPath>/<parentContentItemName>"
    And I start creating a new Landing Page "<contentName>"
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "<contentPath>/<parentContentItemName>/<contentName>"

    Examples:
      | parentContentItemName | contentPath                         | contentName |
      | FolderParent          | root/FolderGrandParent              | NewLandingPageContent1 |
      | FolderChild1          | root/FolderGrandParent/FolderParent | NewLandingPageContent2 |

  Scenario Outline: I can edit Landing Pages in locations I'm allowed
    Given I navigate to content "<contentName>" of type "DedicatedFolder" in "<contentPath>"
    When I perform the "Edit" action
    Then I should be in Landing Page Edit mode for "<contentPath>/<contentName>" Landing Page
    And I set up block "CodeBlock" "Code" with default testing configuration
    And I see the "CodeBlock" "Code" block and its preview
    And I publish the Landing Page
    Then success notification that "Content published." appears
    And I should be on Content view Page for "<contentPath>/<contentName>"

    Examples:
      | contentPath                                      | contentName            | 
      | root/FolderGrandParent/FolderParent              | NewLandingPageContent1 |
      | root/FolderGrandParent/FolderParent/FolderChild1 | NewLandingPageContent2 |

  Scenario Outline: I can create and publish later Content Landing Pages in locations I'm allowed
    Given I navigate to content "<parentContentItemName>" of type "DedicatedFolder" in "<contentPath>"
    And I should be on Content view Page for "<contentPath>/<parentContentItemName>"
    And I start creating a new Landing Page "<contentName>"
    When I publish later from Page Builder
    Then I am notified that content is scheduled for publishing
    And I should be on Content view Page for "<contentPath>/<parentContentItemName>"

    Examples:
      | parentContentItemName | contentPath                         | contentName                         |
      | FolderParent          | root/FolderGrandParent              | NewLandingPageSendForReviewContent1 |
      | FolderChild1          | root/FolderGrandParent/FolderParent | NewLandingPageSendForReviewContent2 |
