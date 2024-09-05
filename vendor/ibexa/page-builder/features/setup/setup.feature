@IbexaExperience @IbexaCommerce @setup
Feature: Set up segments

  Scenario: Set up Segments
    Given I execute a migration
    """
    -
      type: segment_group
      mode: create
      name: 'test_segment_group_1_pb'
      identifier: test_segment_group_1_pb
    -
      type: segment
      mode: create
      name: 'test_segment_pb_1'
      identifier: test_segment_1_pb
      group:
        identifier: test_segment_group_1_pb
    -
      type: segment_group
      mode: create
      name: 'test_segment_group_2_pb'
      identifier: test_segment_group_2_pb
    -
      type: segment
      mode: create
      name: 'test_segment_pb_2'
      identifier: test_segment_2_pb
      group:
        identifier: test_segment_group_2_pb
    """
