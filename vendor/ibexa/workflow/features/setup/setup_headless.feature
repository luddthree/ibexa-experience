@IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: Set up example workflow and users

        @APIUser:admin @part1
        Scenario: Setup Worfklow
        Given I create a "CustomWorkflowContentType" content type in "Content" with "custom_workflow" identifier
            | Field Type | Name        | Identifier  | Required | Searchable | Translatable |
            | Text line  | Name        | name	     | yes      | yes	     | yes          |
            | Rich text  | Description | description | yes      | no	     | yes          |
        And I create a "CustomWorkflowContentType_reverse" content type in "Content" with "custom_workflow_reverse" identifier
            | Field Type | Name        | Identifier  | Required | Searchable | Translatable |
            | Text line  | Name        | name	     | yes      | yes	     | yes          |
            | Rich text  | Description | description | yes      | no	     | yes          |
        And I set configuration to "default" siteaccess under "workflows" key
        """
        custom_workflow:
            name: Custom Workflow
            matchers:
                content_type: [custom_workflow]
                content_status: draft
            stages:
                draft:
                    label: Draft
                    color: '#f15a10'
                review:
                    label: Technical review
                    color: '#10f15a'
                    actions:
                        notify_reviewer: ~
                done:
                    label: Done
                    color: '#301203'
                publish:
                    label: Publish
                    color: '#301203'
                    last_stage: true
            initial_stage: draft
            transitions:
                to_review:
                    from: draft
                    to: review
                    label: To review
                    icon: '/bundles/ibexaicons/img/all-icons.svg#comment'
                    notification:
                        user_group: 12
                    reviewers:
                        required: true
                to_done_from_draft:
                    from: draft
                    to: done
                    label: To done
                    icon: '/bundles/ibexaicons/img/all-icons.svg#comment'
                to_done_from_review:
                    from: review
                    to: done
                    label: To done
                    icon: '/bundles/ibexaicons/img/all-icons.svg#comment'
                to_publish_from_done:
                    from: done
                    to: publish
                    label: To publish
                    icon: '/bundles/ibexaicons/img/all-icons.svg#comment'
                    actions:
                        publish: ~
        """
        And I append configuration to "default" siteaccess under "workflows" key
        """
          workflow_service:
              name: Quick Review1
              matchers:
                  # Which content types can use this workflow
                  content_type: ['article']
                  # Which status of the Content item can use this workflow. Available statuses are draft and published.
              stages:
                  draft:
                      label: Draft
                  review:
                      label: Quick review
                  done:
                      label: Done
                      last_stage: true
              initial_stage: draft
              transitions:
                  to_review:
                      from: draft
                      to: review
                      label: Send to review
                      icon: '/bundles/ibexaicons/img/all-icons.svg#comment'
                  re_review:
                      from: review
                      to: review
                      label: Send to review
                      icon: '/bundles/ibexaicons/img/all-icons.svg#comment'
                  done:
                      from: review
                      to: done
                  edit:
                      reverse: done
        """
        And I append configuration to "default" siteaccess under "workflows" key
        """
          workflow_reverse_transition:
              name: Reverse check
              matchers:
                  # Which content types can use this workflow
                  content_type: ['custom_workflow_reverse']
                  # Which status of the Content item can use this workflow. Available statuses are draft and published.
              stages:
                  start:
                       label: Start
                  end:
                       label: End
              initial_stage: start
              transitions:
                  to_end:
                      from: start
                      to: end
                      label: Go to the end!
                  to_start:
                      reverse: to_end
                      label: Go to the start!
        """

    @APIUser:admin @part2
    Scenario: Create Workflow Creator user
        Given I create a user group "WorkflowEditors"
        And I create a user "Creator" with last name "Workflow" in group "WorkflowEditors"
        And I create a role "creatorRole" with policies
            | module      | function    |
            | content     | create      |
            | content     | versionread |
            | content     | edit        |
            | section     | assign      |
            | user        | login       |
            | site        | view        |
        And I add policy "content" "read" to "creatorRole" with limitations
        | limitationType | limitationValue                                             |
        | Content Type   | article,folder,user,custom_workflow,custom_workflow_reverse |
        And I add policy "workflow" "change_stage" to "creatorRole" with limitations
            | limitationType      | limitationValue                                   |
            | Workflow Transition | custom_workflow:to_review,quick_review:to_review | 
        And I assign user "Creator" to role "creatorRole"

    @APIUser:admin @part2
    Scenario: Create Workflow Publisher user
        Given I create a user "Publisher" with last name "Workflow" in group "WorkflowEditors"
        And I create a role "publisherRole" with policies
            | module      | function    |
            | content     | versionread |
            | section     | assign      |
            | user        | login       |
            | site        | view       |
        And I add policy "content" "publish" to "publisherRole" with limitations
            | limitationType  | limitationValue      |
            | Workflow Stage  | custom_workflow:done |
        And I add policy "content" "read" to "publisherRole" with limitations
        | limitationType | limitationValue                                             |
        | Content Type   | article,folder,user,custom_workflow,custom_workflow_reverse |
        And I add policy "workflow" "change_stage" to "publisherRole" with limitations
            | limitationType      | limitationValue                      |
            | Workflow Transition | custom_workflow:to_publish_from_done |
        And I add policy "content" "edit" to "publisherRole" with limitations
            | limitationType  | limitationValue      |
            | Workflow Stage  | custom_workflow:done |
        And I assign user "Publisher" to role "publisherRole"

    @APIUser:admin @part2
    Scenario: Create Workflow reverseTransitionEditor user
        Given I create a user "reverseTransitionEditor" with last name "Workflow" in group "WorkflowEditors"
        And I create a role "reverseTransitionEditorRole" with policies
            | module  | function    |
            | content | create      |
            | content | versionread |
            | content | diff        |
            | content | edit        |
            | content | publish     |
            | user    | login       |
            | url     | *           |
        And I add policy "content" "read" to "reverseTransitionEditorRole" with limitations
            | limitationType | limitationValue                             |
            | Content Type   | user,article,folder,custom_workflow_reverse |
        And I add policy "workflow" "change_stage" to "reverseTransitionEditorRole" with limitations
            | limitationType      | limitationValue                    |
            | Workflow Transition | workflow_reverse_transition:to_end |
        And I assign user "reverseTransitionEditor" to role "reverseTransitionEditorRole"
