@javascript @IbexaExperience @IbexaCommerce
Feature: As an administrator I can execute Publish Later transitions for users with added limitations

    @javascript
    Scenario: User with field group limitation  can use Publish Later
        Given I create a "CustomPublishLater_limitation_fieldGroup" content type in "Content" with "custom_publish_later_field_group" identifier
            | Field Type | Name        | Identifier  | Required | Searchable | Translatable | Category |
            | Text line  | Name        | name        | no       | yes	     | yes          | content  |
            | Text line  | Description | desc        | no       | yes	     | yes          | metadata |
        And I create a user "limitationEditor_fieldGroup_PublishLater" with last name "limitationPublishLater"
        And I create a role "limitationEditorRole_PublishLater" with policies
            | module   | function    |
            | user     | *           |
            | url      | *           |
            | workflow | *           |
            | content  | create      |
            | content  | versionread |
            | content  | diff        |
            | content  | publish     |
        And I add policy "content" "read" to "limitationEditorRole_PublishLater" with limitations
            | limitationType | limitationValue                                                                          |
            | Content Type   | user,article,folder,landing_page,custom_publish_later_field_group,dashboard_landing_page |
        And I add policy "content" "edit" to "limitationEditorRole_PublishLater" with limitations
            | limitationType | limitationValue |
            | Field Group    | content         |
        And I assign user "limitationEditor_fieldGroup_PublishLater" to role "limitationEditorRole_PublishLater"
        And I open Login page in admin SiteAccess
        And I log in as "limitationEditor_fieldGroup_PublishLater" with password "Passw0rd-42"
        And I should be on Dashboard page
        And I go to "Content structure" in Content tab
        And I start creating a new Content "CustomPublishLater_limitation_fieldGroup"
        And I set content fields
            | label | value                    |
            | Name  | Test Folder PublishLater |
        When I publish later
        And I am notified that content is scheduled for publishing
        Then I should be on Content view Page for root
