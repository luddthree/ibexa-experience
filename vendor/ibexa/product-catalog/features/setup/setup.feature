@IbexaHeadless @IbexaExperience @IbexaCommerce
Feature: Set up migrations

    Scenario: Set up Products
        Given I execute a migration
        """
        -
            type: content_type
            mode: create
            metadata:
                identifier: ProductType
                mainTranslation: eng-GB
                nameSchema: '<name>'
                contentTypeGroups:
                    - product
                translations:
                    eng-GB:
                        name: ProductType
            fields:
            -   identifier: name
                type: ezstring
                required: true
                translations:
                    eng-GB:
                        name: Name
            -   identifier: specification
                type: ibexa_product_specification
                required: true
                translations:
                    eng-GB:
                        name: Product Specification
                fieldSettings:
                    attributes_definitions: ~
            -   identifier: description
                type: ezrichtext
                translations:
                    eng-GB:
                        name: Description
                        description: ''
                required: false
                searchable: true
                translatable: true
            -
                identifier: main_image
                type: ezimageasset
                required: false
                translations:
                    eng-GB:
                        name: Main image
                        description: ''
                translatable: true
                thumbnail: true

        -   type: attribute_group
            mode: create
            identifier: mouse_attributes
            names:
                eng-GB: Mouse attributes
        -   type: attribute
            mode: create
            identifier: mouse_type
            attribute_type_identifier: selection
            attribute_group_identifier: mouse_attributes
            names:
                eng-GB: Mouse Type
            options:
                choices:
                    -  value: MouseType0
                       label:
                          "eng-GB": "Wireless"
                    -  value: MouseType1
                       label:
                          "eng-GB": "Wired"
        -   type: attribute
            mode: create
            identifier: mouse_number_of_buttons
            attribute_type_identifier: integer
            attribute_group_identifier: mouse_attributes
            names:
                eng-GB: Number of buttons
        -   type: attribute
            mode: create
            identifier: mouse_weight
            attribute_type_identifier: float
            attribute_group_identifier: mouse_attributes
            names:
                eng-GB: Weight
        -
            type: content_type
            mode: create
            metadata:
                identifier: ProductTypeWithAttribute
                mainTranslation: eng-GB
                nameSchema: '<name>'
                contentTypeGroups:
                    - product
                translations:
                    eng-GB:
                        name: ProductTypeWithAttribute
            fields:
            -   identifier: name
                type: ezstring
                required: true
                translations:
                    eng-GB:
                        name: Name
            -   identifier: specification
                type: ibexa_product_specification
                required: true
                translations:
                    eng-GB:
                        name: Product Specification
                fieldSettings:
                    attributes_definitions:
                        dimensions:
                            - { attributeDefinition: mouse_type, required: false, discriminator: false }
                            - { attributeDefinition: mouse_number_of_buttons, required: false, discriminator: false }
                            - { attributeDefinition: mouse_weight, required: false, discriminator: false }
            -   identifier: description
                type: ezrichtext
                translations:
                    eng-GB:
                        name: Description
                        description: ''
                required: false
                searchable: true
                translatable: true
            -
                identifier: main_image
                type: ezimageasset
                required: false
                translations:
                    eng-GB:
                        name: Main image
                        description: ''
                translatable: true
                thumbnail: true
        -
            type: currency
            mode: update
            criteria:
                type: field_value
                field: code
                value: EUR
                operator: '='
            enabled: true
        -
            type: currency
            mode: update
            criteria:
                type: field_value
                field: code
                value: USD
                operator: '='
            enabled: true
        """

    Scenario: Set up Product Types
        Given I execute a migration
        """
         -   type: attribute_group
             mode: create
             identifier: TestPTAttributeGroupIdentifier
             names:
                 eng-GB: TestPT Attribute Group

         -   type: attribute
             mode: create
             identifier: TestPTAttributeIdentifier
             attribute_type_identifier: integer
             attribute_group_identifier: TestPTAttributeGroupIdentifier
             names:
                 eng-GB: TestPT Attribute
         """

    Scenario: Set up Attributes
        Given I execute a migration
        """
        -   type: attribute_group
            mode: create
            identifier: TestAttributeGroup
            names:
                eng-GB: TestAttributeGroup
        """

    Scenario: Set up Catalog Filters
        Given I execute a migration
        """
        -   type: attribute_group
            mode: create
            identifier: AttributeGroupForFilters
            names:
                eng-GB: Attribute Group For Filters

        -   type: attribute
            mode: create
            identifier: number_of_usb_ports
            attribute_type_identifier: integer
            attribute_group_identifier: AttributeGroupForFilters
            names:
                eng-GB: Number of USB ports

        -   type: attribute
            mode: create
            identifier: memory
            attribute_type_identifier: integer
            attribute_group_identifier: AttributeGroupForFilters
            names:
                eng-GB: Memory

        -   type: content_type
            mode: create
            metadata:
                identifier: ProductTypeForFilters
                mainTranslation: eng-GB
                nameSchema: '<name>'
                contentTypeGroups:
                    - product
                translations:
                    eng-GB:
                        name: Product Type For Filters
            fields:
                -   identifier: name
                    type: ezstring
                    required: true
                    translations:
                        eng-GB:
                            name: 'Name'
                -   identifier: specification
                    type: ibexa_product_specification
                    required: true
                    translations:
                        eng-GB:
                            name: Specification
                    fieldSettings:
                        attributes_definitions:
                            dimensions:
                                - { attributeDefinition: number_of_usb_ports, required: true, discriminator: false }
                                - { attributeDefinition: memory, required: false, discriminator: false }
                -   identifier: description
                    type: ezrichtext
                    translations:
                        eng-GB:
                            name: Description
                            description: ''
                    required: false
                    searchable: true
                    translatable: true
                -
                    identifier: main_image
                    type: ezimageasset
                    required: false
                    translations:
                        eng-GB:
                            name: Main image
                            description: ''
                    translatable: true
                    thumbnail: true

        -   type: content_type
            mode: create
            metadata:
                identifier: ProductTypeForFilterType
                mainTranslation: eng-GB
                nameSchema: '<name>'
                contentTypeGroups:
                    - product
                translations:
                    eng-GB:
                        name: Product Type For Filter Type
            fields:
                -   identifier: name
                    type: ezstring
                    required: true
                    translations:
                        eng-GB:
                            name: 'Name'
                -   identifier: specification
                    type: ibexa_product_specification
                    required: true
                    translations:
                        eng-GB:
                            name: Specification
                    fieldSettings:
                        attributes_definitions:
                            dimensions:
                                - { attributeDefinition: number_of_usb_ports, required: true, discriminator: false }
                                - { attributeDefinition: memory, required: false, discriminator: false }
                -   identifier: description
                    type: ezrichtext
                    translations:
                        eng-GB:
                            name: Description
                            description: ''
                    required: false
                    searchable: true
                    translatable: true
                -
                    identifier: main_image
                    type: ezimageasset
                    required: false
                    translations:
                        eng-GB:
                            name: Main image
                            description: ''
                    translatable: true
                    thumbnail: true

        -   type: content
            mode: create
            metadata:
                contentType: ProductTypeForFilters
                mainTranslation: eng-GB
                remoteId: 'ProductWithMemory1024'
                alwaysAvailable: true
                section: 1
            location:
                parentLocationRemoteId: ibexa_product_catalog_root
                locationRemoteId: 'ProductWithMemory1024'
            fields:
                -   fieldDefIdentifier: name
                    languageCode: eng-GB
                    value: 'ProductWithMemory1024'
                -   fieldDefIdentifier: specification
                    languageCode: eng-GB
                    value:
                        code: '0442'
                        attributes:
                            memory: 1024
                            number_of_usb_ports: 3

        -   type: product_availability
            mode: create
            product_code: "0442"
            is_available: true
            is_infinite: false
            stock: 100

        -   type: product_price
            mode: create
            product_code: "0442"
            currency_code: 'EUR'
            amount: '105'

        -   type: content
            mode: create
            metadata:
                contentType: ProductTypeForFilters
                mainTranslation: eng-GB
                remoteId: 'ProductWithMemory2048'
                alwaysAvailable: true
                section: 1
            location:
                parentLocationRemoteId: ibexa_product_catalog_root
                locationRemoteId: 'ProductWithMemory2048'
            fields:
                -   fieldDefIdentifier: name
                    languageCode: eng-GB
                    value: 'ProductWithMemory2048'
                -   fieldDefIdentifier: specification
                    languageCode: eng-GB
                    value:
                        code: '0239'
                        attributes:
                            memory: 2048
                            number_of_usb_ports: 5

        -   type: product_price
            mode: create
            product_code: "0239"
            currency_code: 'EUR'
            amount: '36'

        -   type: content
            mode: create
            metadata:
                contentType: ProductTypeForFilters
                mainTranslation: eng-GB
                remoteId: 'ProductWithMemory4096'
                alwaysAvailable: true
                section: 1
            location:
                parentLocationRemoteId: ibexa_product_catalog_root
                locationRemoteId: 'ProductWithMemory4096'
            fields:
                -   fieldDefIdentifier: name
                    languageCode: eng-GB
                    value: 'ProductWithMemory4096'
                -   fieldDefIdentifier: specification
                    languageCode: eng-GB
                    value:
                        code: '0888'
                        attributes:
                            memory: 4096
                            number_of_usb_ports: 7

        -   type: product_availability
            mode: create
            product_code: "0888"
            is_available: true
            is_infinite: false
            stock: 50

        -   type: product_price
            mode: create
            product_code: "0888"
            currency_code: 'USD'
            amount: '10'

        -   type: content
            mode: create
            metadata:
                contentType: ProductTypeForFilterType
                mainTranslation: eng-GB
                remoteId: 'PT Memory4096'
                alwaysAvailable: true
                section: 1
            location:
                parentLocationRemoteId: ibexa_product_catalog_root
                locationRemoteId: 'PT Memory4096'
            fields:
                -   fieldDefIdentifier: name
                    languageCode: eng-GB
                    value: 'PT Memory4096'
                -   fieldDefIdentifier: specification
                    languageCode: eng-GB
                    value:
                        code: '9217'
                        attributes:
                            memory: 4096
                            number_of_usb_ports: 5

        -   type: product_availability
            mode: create
            product_code: "9217"
            is_available: true
            is_infinite: false
            stock: 25

        -   type: product_price
            mode: create
            product_code: "9217"
            currency_code: 'USD'
            amount: '75'
        """

    Scenario: Set up Attribute group and Attributes for Product Type
        Given I execute a migration
        """
         -   type: attribute_group
             mode: create
             identifier: ProcessorAttributes
             names:
                 eng-GB: Processor Attributes

         -   type: attribute
             mode: create
             identifier: processor_options_second
             attribute_type_identifier: checkbox
             attribute_group_identifier: ProcessorAttributes
             names:
                     eng-GB: Processor options
         -   type: attribute
             mode: create
             identifier: processor_color_second
             attribute_type_identifier: color
             attribute_group_identifier: ProcessorAttributes
             names:
                 eng-GB: Processor color
         """

    Scenario: Set up Attribute group, Attributes and Product Type for Product Variants
        Given I execute a migration
        """
        -   type: attribute_group
            mode: create
            identifier: ProcessorAttributesForProductVariants
            names:
                eng-GB: Processor Attributes for Product Variants

        -   type: attribute
            mode: create
            identifier: processor_options
            position: 10
            attribute_type_identifier: checkbox
            attribute_group_identifier: ProcessorAttributesForProductVariants
            names:
                eng-GB: Processor options
        -   type: attribute
            mode: create
            identifier: processor_color
            position: 20
            attribute_type_identifier: color
            attribute_group_identifier: ProcessorAttributesForProductVariants
            names:
                eng-GB: Processor color
        -   type: attribute
            mode: create
            identifier: processor_cache
            position: 30
            attribute_type_identifier: float
            attribute_group_identifier: ProcessorAttributesForProductVariants
            names:
                eng-GB: Cache amount
        -   type: attribute
            mode: create
            identifier: processor_socket
            position: 40
            attribute_type_identifier: selection
            attribute_group_identifier: ProcessorAttributesForProductVariants
            names:
                eng-GB: Processor socket
            options:
                choices:
                    -  value: ProcessorSocket0
                       label:
                          "eng-GB": "Socket 1150"
                    -  value: ProcessorSocket1
                       label:
                          "eng-GB": "Socket 1200"

        -   type: content_type
            mode: create
            metadata:
                identifier: ProductTypeForProductVariants
                mainTranslation: eng-GB
                nameSchema: '<name>'
                contentTypeGroups:
                    - product
                translations:
                    eng-GB:
                        name: Product Type For Product Variants
            fields:
                -   identifier: name
                    type: ezstring
                    required: true
                    translations:
                        eng-GB:
                            name: 'Name'
                -   identifier: description
                    type: ezrichtext
                    translations:
                        eng-GB:
                            name: Description
                            description: ''
                    required: false
                    searchable: true
                    translatable: true
                -   identifier: image
                    type: ezimageasset
                    required: false
                    translations:
                        eng-GB:
                            name: Image
                            description: ''
                    translatable: true
                    thumbnail: true
                -   identifier: category
                    type: ibexa_taxonomy_entry_assignment
                    translations:
                        eng-GB:
                            name: Category
                    required: false
                    searchable: true
                    infoCollector: false
                    translatable: false
                    category: ''
                    defaultValue:
                        taxonomy_entries_identifiers: [product_root]
                        taxonomy: product_categories
                    fieldSettings:
                        taxonomy: product_categories
                -   identifier: specification
                    type: ibexa_product_specification
                    required: true
                    translations:
                        eng-GB:
                            name: Product Specification
                    fieldSettings:
                        attributes_definitions:
                            dimensions:
                                - { attributeDefinition: processor_options, required: true, discriminator: true }
                                - { attributeDefinition: processor_color, required: true, discriminator: true }
                                - { attributeDefinition: processor_cache, required: true, discriminator: true }
                                - { attributeDefinition: processor_socket, required: true, discriminator: true }
        """

    Scenario: Set up default currency for default site access configuration
        Given I append configuration to "default" siteaccess
            | key                        | value |
            | product_catalog.currencies | EUR   |

    Scenario: Set up region with VAT categories for default site access configuration
        Given I append configuration to "ibexa.repositories.default.product_catalog.regions.germany.vat_categories"
      """
          standard: 19
          reduced: 6
      """

    Scenario: Set up region with VAT categories for multirepository
        Given I append configuration to "ibexa.repositories.new_repository.product_catalog.regions.germany.vat_categories"
      """
          standard: 19
          reduced: 6
      """
