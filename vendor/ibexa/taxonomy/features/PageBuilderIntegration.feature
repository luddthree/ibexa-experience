@javascript @IbexaExperience @IbexaCommerce
Feature: Integration of Taxonomy with Page Builder

  Scenario: Tags can be added when Landing Page is edited
    Given I create a "TaxonomyAssignPageBuilder" content type in "Content" with "TaxonomyAssignPageBuilder" identifier
      | Field Type                | Name  | Identifier | Required | Searchable | Translatable | Settings  |
      | Text line                 | Name  | name	   | yes      | yes	       | yes          |           |
      | Taxonomy Entry Assignment | Tags  | taxonomy   | no       | no	       | yes          | tags      |
      | Landing Page              | Page  | page       | no       | no	       | yes          | tags      |
    And I create "folder" Content items
      | name                | short_name        | parentPath      | language |
      | TaxonomyPageBuilder | TaxonomyPageBuilder   | root            | eng-GB   |
    And Tag with name "SomeOldTagLP1" and identifier "SomeOldTagLP1" exists under "root" Tag
    And Tag with name "SomeOldTagLP2" and identifier "SomeOldTagLP2" exists under "SomeOldTagLP1" Tag
    And Tag with name "SomeNewTagLP1" and identifier "SomeNewTagLP1" exists under "root" Tag
    And Tag with name "SomeNewTagLP2" and identifier "SomeNewTagLP2" exists under "SomeNewTagLP1" Tag
    And I create "TaxonomyAssignPageBuilder" Content items in TaxonomyPageBuilder in "eng-GB"
      | name          | taxonomy                     | page                |
      | TestTagLPEdit | SomeOldTagLP1,SomeOldTagLP2 | <h1>TestHeader</h1> |
    And I am logged as admin
    And I'm on Content view Page for "TaxonomyPageBuilder/TestTagLPEdit"
    And I start editing "TaxonomyPageBuilder/TestTagLPEdit" Landing Page draft
    And I set Landing Page properties
      | field | value                                                |
      | Tags  | root/SomeNewTagLP1, root/SomeNewTagLP1/SomeNewTagLP2 |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "TaxonomyPageBuilder/TestTagLPEdit"
    And content attributes equal
      | label  | value                        | fieldTypeIdentifier       |
      | Name   | TestTagLPEdit                | ezstring                  |
      | Tags   | SomeNewTagLP2, SomeNewTagLP1 | ibexa_taxonomy_assignment |
