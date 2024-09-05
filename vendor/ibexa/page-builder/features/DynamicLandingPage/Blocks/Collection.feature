@IbexaExperience @IbexaCommerce
Feature: Collection Block

    Background:
        Given I am logged as admin
        Given I'm on Content view Page for root
        And I start creating a new Landing Page "Collection"
        And I add Landing page blocks
            | blockType  |
            | Collection |

    @javascript
    Scenario: Add a Collection block to a new Landing Page and delete item
        Given I add Content Items to the Collection block
            | item         |
            | Media        |
            | Media/Images |
            | Media/Files  |
        When I delete the "Images" item from Collection block
        And I submit the block pop-up form
        Then I see the "Collection" "Collection" block and its preview
            | parameter1  |
            | Media,Files |

    @javascript
    Scenario: Add a Collection block to a new Landing Page and reorder item
        Given I add Content Items to the Collection block
            | item         |
            | Media        |
            | Media/Images |
            | Media/Files  |
        When I move item "Media" behind the item "Files" in Collection block
        When I move item "Files" behind the item "Media" in Collection block
        And I submit the block pop-up form
        Then I see the "Collection" "Collection" block and its preview
            | parameter1         |
            | Images,Media,Files |
