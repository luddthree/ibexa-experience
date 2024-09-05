# Changes in 1.1.0

Below changes were made because all classes and services were related to legacy Landing Page fieldtype. They were left in the codebase by mistake and they weren't working as is.

## Removed classes
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\BlockDefinitionFactory`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\BlockDefinition`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\BlockAttributeDefinitionFactory`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\BlockAttributeDefinition`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\AbstractDefinitionFactory`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\BlockAttribute\BlockAttributeDefinitionFactory`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\BlockAttribute\ContentTypeDefinition`
- namespace: `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Processor` 
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\AbstractBlockType`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\BlockType`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\ConfigurableBlockType`
- `\EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\PageService`
- `\EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\ScheduleEventParserCompilerPass`
- namespace: `EzSystems\EzPlatformPageFieldType\Enum`
- `\EzSystems\EzPlatformPageFieldType\Exception\CircularOverflowException`
- `\EzSystems\EzPlatformPageFieldType\Exception\InvalidBlockArgumentException`
- `\EzSystems\EzPlatformPageFieldType\Exception\InvalidXsdValidationException`
- `\EzSystems\EzPlatformPageFieldType\Exception\NoContentTypeException`
- `\EzSystems\EzPlatformPageFieldType\Exception\InvalidJSONException`
- `\EzSystems\EzPlatformPageFieldType\Exception\ZoneNotFoundException`

## Removed services:
- `ezpublish.landing_page.schedule_block.event_parser_registry`
- `ezpublish.landing_page.schedule_block.event_parser.item_added`
- `ezpublish.landing_page.schedule_block.event_parser.item_removed`
- `ezpublish.landing_page.schedule_block.event_parser.item_overflow`
- `ezpublish.landing_page.schedule_block.event_parser.item_duplicate_removed`
- `ezpublish.landing_page.schedule_block.schedule_service`
- `ezpublish.landing_page.schedule_block.snapshot_service`
- `ezpublish.landing_page.schedule_block.tree_visitor`
- `ezpublish.landing_page.schedule_block.timeline.event_factory`
- `ezpublish.landing_page.schedule_block.schedule_group_factory`
- `ezpublish.landing_page.schedule_block.storage_parser_registry`
- `ezpublish.landing_page.schedule_block.storage_parser.legacy_storage_parser`
- `ezpublish.landing_page.schedule_block.storage_parser.storage_parser`
- `ezpublish.landing_page.block_definition_processor`
- `ezpublish.fieldtype.ezlandingpage.xml_converter`
- `ezpublish.landing_page.page_service`
- `EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\XmlConverter`
- `EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\PageService`
