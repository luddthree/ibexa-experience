@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: Integration of Taxonomy with the Content model

  Scenario: Tags can be added when creating Content
    Given I create a "TaxonomyAssignContent" content type in "Content" with "TaxonomyAssignContent" identifier
      | Field Type                | Name  | Identifier | Required | Searchable | Translatable | Settings  |
      | Text line                 | Name  | name	   | yes      | yes	       | yes          |           |
      | Taxonomy Entry Assignment | Tags  | taxonomy   | no       | no	       | yes          | tags      |
    And I create "folder" Content items
      | name            | short_name        | parentPath      | language |
      | TaxonomyContent | TaxonomyContent   | root            | eng-GB   |
    And Tag with name "TestCreateTag1" and identifier "TestCreateTag1" exists under "root" Tag
    And Tag with name "TestCreateTag2" and identifier "TestCreateTag2" exists under "TestCreateTag1" Tag
    And I am logged as admin
    And I'm on Content view Page for TaxonomyContent
    When I start creating a new Content "TaxonomyAssignContent"
    And I set content fields
      | label  | value      |
      | Name   | TestCreate |
      | Tags   | root/TestCreateTag1, root/TestCreateTag1/TestCreateTag2 |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "TaxonomyContent/TestCreate"
    And content attributes equal
      | label  | value                          | fieldTypeIdentifier       |
      | Name   | TestCreate                     | ezstring                  |
      | Tags   | TestCreateTag2, TestCreateTag1 | ibexa_taxonomy_assignment |

  Scenario: Tags can be added when Content is edited
    Given Tag with name "SomeOldTag1" and identifier "SomeOldTag1" exists under "root" Tag
    And Tag with name "SomeOldTag2" and identifier "SomeOldTag2" exists under "SomeOldTag1" Tag
    And Tag with name "SomeNewTag1" and identifier "SomeNewTag1" exists under "root" Tag
    And Tag with name "SomeNewTag2" and identifier "SomeNewTag2" exists under "SomeNewTag1" Tag
    And I create "TaxonomyAssignContent" Content items in TaxonomyContent in "eng-GB"
      | name        | taxonomy                |
      | TestTagEdit | SomeOldTag1,SomeOldTag2 |
    And I am logged as admin
    And I'm on Content view Page for "TaxonomyContent/TestTagEdit"
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label  | value                                          |
      | Tags   | root/SomeNewTag1, root/SomeNewTag1/SomeNewTag2 |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "TaxonomyContent/TestTagEdit"
    And content attributes equal
      | label  | value                    | fieldTypeIdentifier       |
      | Name   | TestTagEdit              | ezstring                  |
      | Tags   | SomeNewTag2, SomeNewTag1 | ibexa_taxonomy_assignment |

  Scenario: Tags can be removed from Content
    Given Tag with name "TagToRemove1" and identifier "TagToRemove1" exists under "root" Tag
    And Tag with name "TagToRemove2" and identifier "TagToRemove2" exists under "TagToRemove1" Tag
    And I create "TaxonomyAssignContent" Content items in TaxonomyContent in "eng-GB"
      | name      | taxonomy                  |
      | RemoveTag | TagToRemove1,TagToRemove2 |
    And I am logged as admin
    And I'm on Content view Page for "TaxonomyContent/RemoveTag"
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label  | value |
      | Tags   | empty |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "TaxonomyContent/RemoveTag"
    And content attributes equal
      | label  | value               | fieldTypeIdentifier        |
      | Name   | RemoveTag           | ezstring                   |
      | Tags   | This field is empty1 | ibexa_taxonomy_assignment |
