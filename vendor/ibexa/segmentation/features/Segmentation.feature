@IbexaExperience @IbexaCommerce @javascript
Feature: Segmentation management
  As an administrator
  I want to manage Segments.

  Scenario: Create a segment group without segments under segments group
    Given I am logged as admin
    And I open "Segment Groups" page in admin SiteAccess
    When I start creating a new segment group
    And I fill "testname" name field and "testid" identifier field during segment group fields configuration
    And I confirm creation of new segment group
    And success notification that "Segment Group 'testname' created." appears
    Then There's segment group with "testname" name and "testid" identifier in Details section

  Scenario: Create a segment group with segments under segments group
    Given I am logged as admin
    And I open "Segment Groups" page in admin SiteAccess
    And I start creating a new segment group
    When I fill "testname2" name field and "testid2" identifier field during segment group fields configuration
    And I add segment with "testsegname2" name and "testsegid2" identifier to segment group during segment group creation
    And I confirm creation of new segment group
    And success notification that "Segment Group 'testname2' created." appears
    And There's segment group with "testname2" name and "testid2" identifier in Details section
    Then There's segment with "testsegname2" name and "testsegid2" identifier in Segments Under This Group section

  Scenario: Delete segment from segment group
    Given I am logged as admin
    And I execute a migration
      """
      -
        type: segment_group
        mode: create
        name: 'testname3'
        identifier: testid3
      -
        type: segment
        mode: create
        name: 'testsegname3'
        identifier: testsegid3
        group:
            identifier: testid3
      """
    And I open segment group with "testid3" identifier
    And There's segment group with "testname3" name and "testid3" identifier in Details section
    And There's segment with "testsegname3" name and "testsegid3" identifier in Segments Under This Group section
    When I delete "testsegname3" segment from Segments group
    And success notification that "Segment 'testsegname3' removed." appears
    Then There's no segment with "testsegname3" name

  Scenario: Add segment to segment group
    Given I am logged as admin
    And I execute a migration
      """
      -
        type: segment_group
        mode: create
        name: 'testname4'
        identifier: testid4
      """
    And I open segment group with "testid4" identifier
    And There's segment group with "testname4" name and "testid4" identifier in Details section
    When I add segment with "testsegname4" name and "testsegid4" identifier to segment group
    And success notification that "Segment 'testsegname4' added." appears
    Then There's segment with "testsegname4" name and "testsegid4" identifier in Segments Under This Group section

  Scenario: Delete segment group
    Given I am logged as admin
    And I execute a migration
    """
    -
      type: segment_group
      mode: create
      name: 'testname5'
      identifier: testid5
    """
    And I open "Segment Groups" page in admin SiteAccess
    When I delete "testname5" segment group
    And success notification that "Segment Group 'testname5' removed." appears
    Then There's no segment group with "testname5" name
