Feature: Basic interaction tests for publishing in the future

    Background:
        Given I am logged as admin

    @javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
    Scenario: Create a new article and use Publish Later
        Given I'm on Content view Page for root
        And I start creating a new Content "Folder"
        And I set content fields
            | label       | value            |
            | Name        | Test Folder      |
            | Description | Test Description |
        When I publish later
        Then I am notified that content is scheduled for publishing
        Then I should be on Content view Page for root

    @javascript @IbexaExperience @IbexaCommerce
    Scenario: Create a new Landing Page and use Publish Later
        Given I'm on Content view Page for root
        And I start creating a new Landing Page "Publish Later New"
        When I publish later from Page Builder
        Then I am notified that content is scheduled for publishing
        And I should be on Content view Page for root
