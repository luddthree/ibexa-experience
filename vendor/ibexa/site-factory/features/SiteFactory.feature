Feature: Create Site Factory sites

    @admin @create
    Scenario: Create Sites
    Given I set up 5 Sites

    @admin @verify
    Scenario: Verify Sites
    Given There are 5 Sites with correct data
