@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: Basic taxonomy operations

  Scenario: Root tag cannot be deleted or moved
    Given I am logged as admin
    When I am viewing the Taxonomy Tag named "Root"
    Then the "Delete" button is not visible
    And the "Move" button is not visible
    And the "Create" button is visible
    And the buttons are disabled
     | buttonName |
     | Edit       |

  Scenario: Existing Tag can be moved
    Given Tag with name "TestMove" and identifier "TestMove" exists under "root" Tag
    And Tag with name "TestMoveChild" and identifier "TestMoveChild" exists under "TestMove" Tag
    And Tag with name "TestMoveTarget" and identifier "TestMoveTarget" exists under "root" Tag
    And I am logged as admin
    And I am viewing the Taxonomy Tag named "TestMove"
    When I move the Tag under "TestMoveTarget"
    Then success notification that "Entry 'TestMove' moved to 'TestMoveTarget'." appears
    And I should be viewing the Taxonomy Tag named "TestMove"
    And Tag "Root/TestMove" doesn't exist in the Taxonomy tree
    And Tag "Root/TestMove/TestMoveChild" doesn't exist in the Taxonomy tree
    And Tag "Root/TestMoveTarget/TestMove" exists in the Taxonomy tree
    And Tag "Root/TestMoveTarget/TestMove/TestMoveChild" exists in the Taxonomy tree

  Scenario: New Tag can be created under root node
    Given I am logged as admin
    And I am viewing the Taxonomy Tag named "Root"
    When I start creating a new Tag
    And I set content fields
      | label      | value         |
      | Identifier | TagIdentifier |
      | Name       | TagName       |
      | Description | TagDescription |
    And I click the edit action bar button "Save and close"
    Then success notification that "Content published." appears
    And I should be viewing the Taxonomy Tag named "TagName"
    And Tag "Root/TagName" exists in the Taxonomy tree

  Scenario: New Tag can be created under chosen nested node
    Given Tag with name "TestNestedCreateParent" and identifier "TestNestedCreateParent" exists under "root" Tag
    And I am logged as admin
    And I am viewing the Taxonomy Tag named "root"
    When I start creating a new Tag
    And I set content fields
      | label      | value                  |
      | Identifier | TestNestedCreate       |
      | Name       | TestNestedCreate       |
      | Parent     | TestNestedCreateParent |
    And I click the edit action bar button "Save and close"
    Then success notification that "Content published." appears
    And I should be viewing the Taxonomy Tag named "TestNestedCreate"
    And Tag "Root/TestNestedCreateParent/TestNestedCreate" exists in the Taxonomy tree

  Scenario: A tag with existing identifier cannot be added
    Given Tag with name "TestExisting" and identifier "TestExisting" exists under "root" Tag
    And I am logged as admin
    And I am viewing the Taxonomy Tag named "Root"
    When I start creating a new Tag
    And I set content fields
      | label      | value        |
      | Identifier | TestExisting |
    And I click the edit action bar button "Save and close"
    Then field "Identifier" contains validation error 'Taxonomy Entry with identifier "TestExisting" already exists in "tags" taxonomy tree. Please use unique identifier.'

  Scenario: Existing Tag with a child can be edited
    Given Tag with name "TestEdit" and identifier "TestEdit" exists under "root" Tag
    And Tag with name "TestEditChild" and identifier "TestEditChild" exists under "TestEdit" Tag
    And I am logged as admin
    And I am viewing the Taxonomy Tag named "TestEdit"
    When I click the edit action bar button "Edit"
    And I set content fields
      | label      | value      |
      | Identifier | TestEdited |
      | Name       | TestEdited |
    And I click the edit action bar button "Save and close"
    Then success notification that "Content published." appears
    And I should be viewing the Taxonomy Tag named "TestEdited"
    And Tag "Root/TestEdited" exists in the Taxonomy tree
    And Tag "Root/TestEdited/TestEditChild" exists in the Taxonomy tree

  Scenario: Existing Tag can be deleted
    Given Tag with name "TestDelete" and identifier "TestDelete" exists under "root" Tag
    And Tag with name "TestDeleteChild" and identifier "TestDeleteChild" exists under "TestDelete" Tag
    And I am logged as admin
    And I am viewing the Taxonomy Tag named "TestDelete"
    When I delete the Tag
    Then success notification that "Entry 'TestDelete' deleted." appears
    And I should be viewing the Taxonomy Tag named "Root"
    And Tag "Root/TestDelete" doesn't exist in the Taxonomy tree
    And Tag "Root/TestDelete/TestDeleteChild" doesn't exist in the Taxonomy tree

  Scenario: Tagged Content Items are displayed on Tag details page
    Given Tag with name "TestRelation" and identifier "TestRelation" exists under "root" Tag
    And I create a "TaxonomyAssign" content type in "Content" with "TaxonomyAssign" identifier
      | Field Type                | Name  | Identifier | Required | Searchable | Translatable | Settings  |
      | Text line                 | Name  | name	     | yes      | yes	       | yes          |           |
      | Taxonomy Entry Assignment | Tags  | taxonomy   | no       | no	       | yes          | tags      |
    And I create "folder" Content items
      | name           | short_name       | parentPath      | language |
      | TaxonomyAssign | TaxonomyAssign   | root            | eng-GB   |
    And I create "TaxonomyAssign" Content items in TaxonomyAssign in "eng-GB"
      | name           | taxonomy     |
      | TestTagAssign1 | TestRelation |
      | TestTagAssign2 | TestRelation |
    And I am logged as admin
    And I am viewing the Taxonomy Tag named "TestRelation"
    When I switch to Content tab in Content structure
    Then Content Items are displayed as assigned to that Tag
      | itemName       |
      | TestTagAssign1 |
      | TestTagAssign2 |
