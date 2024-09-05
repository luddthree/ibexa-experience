@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript
Feature: Product catalog management

    Background:
        Given I am logged as admin

    Scenario: Attribute can be added
        Given I open "Attributes" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        And I select "Color" Attribute Type
        When I set fields
            | label       | value                    |
            | Name        | Attribute Test           |
            | Identifier  | AttributeIdentifierTest  |
            | Description | Description              |
            | Position    | 2                        |
        And I select the "TestAttributeGroup" Attribute Group for the Attribute
        And I click on the edit action bar button "Save and close"
        And success notification that "Attribute 'Attribute Test' created." appears
        Then I should see an Attribute with values
            | Name             | Identifier               | Description  | Type   | Group               | Position  |
            | Attribute Test   | AttributeIdentifierTest  | Description  | Color  | TestAttributeGroup  | 2         |

    Scenario: Attribute can be edited
        Given I open "Attributes" page in admin SiteAccess
        And there is an Attribute "Attribute Test"
        And I edit "Attribute Test" Attribute
        When I set fields
            | label       | value                          |
            | Name        | Attribute Test Edited          |
            | Identifier  | AttributeIdentifierTestEdited  |
            | Description | DescriptionEdited              |
            | Position    | 1                              |
        And I click on the edit action bar button "Save and close"
        Then I should see an Attribute with values
            | Name                   | Identifier                     | Description         | Type   | Group               | Position  |
            | Attribute Test Edited  | AttributeIdentifierTestEdited  | DescriptionEdited   | Color  | TestAttributeGroup  | 1         |

    Scenario: Attribute can be deleted
        Given I open "Attribute Test Edited" Attribute page in admin SiteAccess
        And I should see an Attribute with values
            | Name                   | Identifier                     | Description        | Type   | Group               | Position  |
            | Attribute Test Edited  | AttributeIdentifierTestEdited  | DescriptionEdited  | Color  | TestAttributeGroup  | 1         |
        When I delete the Attribute
        And success notification that "Attribute definition 'Attribute Test Edited' deleted." appears
        Then I should be on "Attributes" page
