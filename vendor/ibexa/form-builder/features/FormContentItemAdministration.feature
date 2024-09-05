@IbexaExperience @IbexaCommerce @javascript
Feature: Form administration
  As an administrator
  In order to manage forms on my site
  I want to create, edit, copy, move and delete forms.

  Background:
    Given I am logged as admin

  Scenario: New form creation can be closed
    Given I'm on Content view Page for "Forms"
    When I start creating a new content "Form"
    And I set content fields
      | label | value     |
      | Title | Test Form |
    And I perform the "Delete draft" action
    Then I should be on Content view Page for "Forms"
    And there's no "Test Form" "Form" on Subitems list

  Scenario: New form draft can be saved
    Given I'm on Content view Page for "Forms"
    When I start creating a new content "Form"
    And I set content fields
      | label | value     |
      | Title | Test Form |
    And I perform the "Save" action from the "Save and close" group
    Then success notification that "Content draft saved." appears
    And I should be on Content update page for "Test Form"
    And I open the "Dashboard" page in admin SiteAccess
    And there's draft "Test Form" on Dashboard list

  Scenario: Form draft can be published
    Given I'm on Content view Page for "Forms"
    Given I start creating a new content "Form"
    And I set content fields
      | label | value      |
      | Title | Test Form2 |
    And I perform the "Save" action from the "Save and close" group
    And I should be on Content update page for "Test Form2"
    When I perform the "Publish" action
    Then I should be on Content view Page for "Forms/Test Form2"
    And I go to dashboard
    And there's no draft "Test Form2" on Dashboard list

  Scenario: Form draft can be deleted
    Given I open the "Dashboard" page in admin SiteAccess
    And there's draft "Test Form" on Dashboard list
    When I start editing content draft "Test Form"
    And I set content fields
      | label | value            |
      | Title | Test Form edited |
    And I perform the "Delete draft" action
    Then I should be on Content view Page for "Forms"
    And I go to dashboard
    And there's no draft "Test Form edited" on Dashboard list

  Scenario: Empty form can be published
    Given I'm on Content view Page for "Forms"
    When I start creating a new content "Form"
    And I set content fields
      | label | value         |
      | Title | TestEmptyForm |
    And I perform the "Publish" action
    Then success notification that "Content published." appears
    Then I should be on Content view Page for "Forms/TestEmptyForm"
    And content attributes equal
      | label | value         |
      | Title | TestEmptyForm |

  Scenario: Form edit draft can be deleted
    Given I'm on Content view Page for "Forms/TestEmptyForm"
    When I perform the "Edit" action
    And I set content fields
      | label | value            |
      | Title | Test Form edited |
    And I perform the "Delete draft" action
    Then I should be on Content view Page for "Forms/TestEmptyForm"
    And I go to dashboard
    And there's no draft "Test Form edited" on Dashboard list

  Scenario: Content draft can be edited from dashboard
    Given I'm on Content view Page for "Forms/TestEmptyForm"
    And I perform the "Edit" action
    And I set content fields
      | label | value            |
      | Title | Test Form edited |
    And I perform the "Save and close" action
    When I go to dashboard
    And there's draft "Test Form edited" on Dashboard list
    And I start editing content draft "Test Form edited"
    Then I should be on Content update page for "Test Form edited"

  Scenario: Form edit draft can be saved
    Given I open the "Dashboard" page in admin SiteAccess
    And there's draft "Test Form edited" on Dashboard list
    When I start editing content draft "Test Form edited"
    And I set content fields
      | label | value             |
      | Title | Test Form edited2 |
    And I perform the "Save" action from the "Save and close" group
    Then success notification that "Content draft saved." appears
    And I should be on Content update page for "Test Form edited2"
    And I open the "Dashboard" page in admin SiteAccess
    And there's draft "Test Form edited2" on Dashboard list

  Scenario: Form draft can be created and published through draft list modal
    Given I'm on Content view Page for "Forms/TestEmptyForm"
    When I perform the "Edit" action
    And I start creating new draft from draft conflict modal
    And I set content fields
      | label | value             |
      | Title | Test Form edited3 |
    And I perform the "Publish" action
    Then success notification that "Content published." appears
    Then I should be on Content view Page for "Forms/Test Form edited3"
    And content attributes equal
      | label | value             |
      | Title | Test Form edited3 |

  Scenario: Form draft from draft list modal can be published
    Given I'm on Content view Page for "Forms/Test Form edited3"
    And I perform the "Edit" action
    And I should be on Content update page for "Test Form edited3"
    And I perform the "Save and close" action
    And I'm on Content view Page for "Forms/Test Form edited3"
    When I perform the "Edit" action
    And I start editing draft with version number "4" from draft conflict modal
    And I set content fields
      | label | value             |
      | Title | Test Form edited4 |
    And I perform the "Publish" action
    Then success notification that "Content published." appears
    Then I should be on Content view Page for "Forms/Test Form edited4"
    And content attributes equal
      | label | value             |
      | Title | Test Form edited4 |

  Scenario: Form moving can be cancelled
    Given I'm on Content view Page for "Forms/Test Form edited4"
    When I perform the "Move" action
    And I select content "Media/Images" through UDW
    And I close the UDW window
    Then I should be on Content view Page for "Forms/Test Form edited4"
    And content attributes equal
      | label | value             |
      | Title | Test Form edited4 |

  Scenario: Form can be moved
    Given I'm on Content view Page for "Forms/Test Form edited4"
    When I perform the "Move" action
    And I select content "Media/Images" through UDW
    And I confirm the selection in UDW
    Then success notification that "'Test Form edited4' moved to 'Images'" appears
    Then I should be on Content view Page for "Media/Images/Test Form edited4"
    And content attributes equal
      | label | value             |
      | Title | Test Form edited4 |
    And I go to "Forms" in "Content" tab
    And I should be on Content view Page for "Forms"
    And there's no "Test Form edited4" "Form" on Subitems list

  Scenario: Form copying can be cancelled
    Given I'm on Content view Page for "Media/Images/Test Form edited4"
    When I perform the "Copy" action
    And I select content "Forms" through UDW
    And I close the UDW window
    Then I should be on Content view Page for "Forms/Test Form edited4"
    And content attributes equal
      | label | value             |
      | Title | Test Form edited4 |
    And I go to "Forms" in "Content" tab
    And I should be on Content view Page for "Forms"
    And there's no "Test Form edited4" "Form" on Subitems list

  Scenario: Form can be copied
    Given I'm on Content view Page for "Media/Images/Test Form edited4"
    When I perform the "Copy" action
    And I select content "Forms" through UDW
    And I confirm the selection in UDW
    Then success notification that "'Test Form edited4' copied to 'Forms'" appears
    And I should be on Content view Page for "Forms/Test Form edited4"
    And content attributes equal
      | label | value             |
      | Title | Test Form edited4 |
    And I'm on Content view Page for "Media/Images"
    And there's a "Test Form edited4" "Form" on Subitems list

  Scenario: Form can be moved to trash from Content
    Given I'm on Content view Page for "Media/Images/Test Form edited4"
    When I send content to trash
    Then success notification that "Location 'Test Form edited4' moved to Trash." appears
    And I should be on Content view Page for "Media/Images"
    And there's no "Test Form edited4" "Form" on Subitems list
    And I open "Trash" page in admin SiteAccess
    And there is a "Form" "Test Form edited4" on Trash list

  Scenario: Form can be moved to trash from Forms
    Given I'm on Content view Page for "Forms/Test Form2"
    When I send content to trash
    Then success notification that "Location 'Test Form2' moved to Trash." appears
    And I should be on Content view Page for "Forms"
    And there's no "Form" "Test Form2" on Subitems list
    And I open "Trash" page in admin SiteAccess
    And there is a "Form" "Test Form2" on Trash list
