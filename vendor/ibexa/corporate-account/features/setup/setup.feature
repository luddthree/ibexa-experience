@IbexaExperience @IbexaCommerce
Feature: Set up migrations

  Scenario: Set up Customer Group
    Given I execute a migration
    """
    -   type: customer_group
        mode: create
        identifier: customer_group_for_corporate_account
        names:
            eng-GB: Customer group for corporate account
        descriptions:
            eng-GB: Customer group description
        global_price_rate: 8
    """
