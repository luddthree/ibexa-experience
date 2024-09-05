@IbexaExperience @IbexaCommerce
Feature: Page Builder editor can be opened from the Content tab and standard Content editing can be opened from Page Builder editor

    Background:
        Given I am logged as admin
        And a "folder" Content item named "ContentIntegrationContainer" exists in root
          | name                        | short_name                  |
          | ContentIntegrationContainer | ContentIntegrationContainer |

    @javascript
    Scenario: Publishing a Landing Page redirects to that Page
        Given I'm on Content view Page for ContentIntegrationContainer
        And I start creating a new Landing Page "CreatedLandingPage"
        And I should be in Landing Page "page view" editor launch mode
        When I publish the Landing Page
        And success notification that "Content published." appears
        And I should be on Content view Page for "ContentIntegrationContainer/CreatedLandingPage"

    @javascript
    Scenario: Editing existing LP redirects to Page Builder Editor
        Given I'm on Content view Page for "ContentIntegrationContainer/CreatedLandingPage"
        When I perform the "Edit" action
        Then I should be in Landing Page Edit mode for "ContentIntegrationContainer/CreatedLandingPage" Landing Page

#    @javascript
#    Scenario: Publishing Content from Page Builder Editor redirects to Content View of that Content
#        Given I open the "root" Content Item in Page Builder
#        And I start creating a new Content Item "Folder" from Page Builder
#        And I set content fields
#            | label      | value             |
#            | Name       | Test Folder       |
#            | Short name | Test Folder Short |
#        When I perform the "Publish" action
#        Then I should be on Content view Page for "Test Folder Short"

#    @javascript
#    Scenario: Deleting draft of Content redirects to Content View of the parent
#        Given I open the "Site Skeletons" Content Item in Page Builder
#        And I start creating a new Content Item "Folder" from Page Builder
#        And I set content fields
#            | label      | value                 |
#            | Name       | Test Folder Deleted   |
#            | Short name | Test Folder Deleted     |
#        When I perform the "Delete draft" action
#        Then I should be on Content view Page for "Site Skeletons"
#        And there's no "Test Folder Deleted" "Folder" on Subitems list

    @javascript
    Scenario: Deleting draft of a Landing Page redirects to Content View of the parent
        Given I'm on Content view Page for "Media"
        And I start creating a new Landing Page "Delete LandingPage Draft"
        When I delete the draft
        Then I should be on Content view Page for "Media"
        And there's no "Test Folder Deleted" "Folder" on Subitems list

#    @javascript @APIUser:admin
#    Scenario: User can preview non-Page Content in Page Builder
#        And I create "folder" Content items in root in "eng-GB"
#            | name           | short_name     |
#            | TestNavigation | TestNavigation |
#        Given I'm on Content view Page for "TestNavigation"
#        When I go to Page Builder
#        Then I should be viewing "TestNavigation" in Page Editor

    @javascript @APIUser:admin
    Scenario: User can deselect blocks shown in Page Builder editor for Landing Page content type
        Given I create a "CustomLandingPage" content type in "Content" with "custom_landing_page" identifier
            | Field Type    | Name        | Identifier  | Required | Searchable | Translatable |
            | Text line     | Title       | title	    | no       | yes	    | yes          |
            | Rich text     | Description | description | no      | no	        | yes          |
            | Landing Page  | Page        | page        | no       | no	        | yes          |
        And I'm on content type Page for "Content" group
        And I start editing content type "CustomLandingPage"
        When I check "video" block in ezlandingpage field blocks section
        And I perform the "Save and close" action
        And I create "custom_landing_page" Content items in ContentIntegrationContainer in "eng-GB"
            | title                 | description | page                |
            | blockVisibilityTestLP | test_desc   | <h1>TestHeader</h1> |
        And I'm on Content view Page for "ContentIntegrationContainer/blockVisibilityTestLP"
        And I perform the "Edit" action
        Then I should be in Landing Page Edit mode for "ContentIntegrationContainer/blockVisibilityTestLP" Landing Page
        And It's not possible to add "Video" block to the Landing Page

    @javascript @APIUser:admin
    Scenario: User can select in which editor launch mode will Landing Page be opened
        Given I create a "CustomLaunchModeLandingPage" content type in "Content" with "custom_launch_mode_landing_page" identifier
            | Field Type   | Name        | Identifier  | Required | Searchable | Translatable |
            | Text line    | Title       | title       | no       | yes	       | yes          |
            | Rich text    | Description | description | no       | no	       | yes          |
            | Landing Page | Page        | page        | no       | no	       | yes          |
        And I'm on content type Page for "Content" group
        And I start editing content type "CustomLaunchModeLandingPage"
        When I select "field_view_mode" editor launch mode in ezlandingpage field options
        And I perform the "Save and close" action
        And I create "custom_launch_mode_landing_page" Content items in ContentIntegrationContainer in "eng-GB"
            | title            | description | page                |
            | launchModeTestLP | test_desc   | <h1>TestHeader</h1> |
        And I'm on Content view Page for "ContentIntegrationContainer/launchModeTestLP"
        And I perform the "Edit" action
        Then I should be in Landing Page "field view" editor launch mode
