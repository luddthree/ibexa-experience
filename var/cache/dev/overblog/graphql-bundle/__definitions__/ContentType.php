<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ContentType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'Content';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa repository ContentInfo.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::int(),
                    'description' => 'The Content item\'s unique ID.',
                ],
                'contentTypeId' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The content type ID of the Content item.',
                ],
                'contentType' => [
                    'type' => fn() => $services->getType('ContentType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentTypeById", ...[0 => $value->contentTypeId]);
                    },
                    'description' => 'The content type of the Content item.',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'The computed name (via name schema) in the main language of the Content item.',
                ],
                'section' => [
                    'type' => fn() => $services->getType('Section'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("SectionById", ...[0 => $value->sectionId]);
                    },
                    'description' => 'The section to which the Content object is assigned.',
                ],
                'currentVersionNo' => [
                    'type' => Type::int(),
                    'description' => 'Version number of the published version, or 1 for a newly created draft.',
                ],
                'currentVersion' => [
                    'type' => fn() => $services->getType('Version'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("CurrentVersion", ...[0 => $value]);
                    },
                    'description' => 'The currently published version',
                ],
                'versions' => [
                    'type' => fn() => Type::listOf($services->getType('Version')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentVersions", ...[0 => $value->id]);
                    },
                    'description' => 'All content versions.',
                ],
                'published' => [
                    'type' => Type::boolean(),
                    'description' => 'If the Content item has a published version.',
                ],
                'ownerId' => [
                    'type' => Type::int(),
                    'description' => 'The user id of the owner of the Content object',
                ],
                'owner' => [
                    'type' => fn() => $services->getType('User'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserById", ...[0 => $value->ownerId]);
                    },
                    'description' => 'The owner user of the Content object',
                ],
                'modificationDate' => [
                    'type' => fn() => $services->getType('DateTime'),
                    'description' => 'Date the Content item was last modified on.',
                ],
                'publishedDate' => [
                    'type' => fn() => $services->getType('DateTime'),
                    'description' => 'Date the Content item was first published on.',
                ],
                'alwaysAvailable' => [
                    'type' => Type::boolean(),
                    'description' => 'Indicates if the Content object is shown in the mainlanguage if its not present in an other requested language.',
                ],
                'remoteId' => [
                    'type' => Type::string(),
                    'description' => 'A global unique id of the Content object',
                ],
                'mainLanguageCode' => [
                    'type' => Type::string(),
                    'description' => 'The main language code of the Content object. If the available flag is set to true the Content is shown in this language if the requested language does not exist.',
                ],
                'mainLocationId' => [
                    'type' => Type::int(),
                    'description' => 'Identifier of the Content item\'s main location.',
                ],
                'mainLocation' => [
                    'type' => fn() => $services->getType('Location'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationById", ...[0 => $value->mainLocationId]);
                    },
                    'description' => 'Content item\'s main location.',
                ],
                'locations' => [
                    'type' => fn() => Type::listOf($services->getType('Location')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationsByContentId", ...[0 => $value->id]);
                    },
                    'description' => 'All the locations of the Content item',
                ],
                'relations' => [
                    'type' => fn() => Type::listOf($services->getType('ContentRelation')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentRelations", ...[0 => $value]);
                    },
                    'description' => 'Relations from this Content',
                ],
                'reverseRelations' => [
                    'type' => fn() => Type::listOf($services->getType('ContentRelation')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentReverseRelations", ...[0 => $value]);
                    },
                    'description' => 'Relations to this Content',
                ],
                'states' => [
                    'type' => fn() => Type::listOf($services->getType('ObjectState')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ObjectStateByContentInfo", ...[0 => $value]);
                    },
                    'description' => 'Content States.',
                ],
            ],
        ];
        
        parent::__construct($configProcessor->process($config));
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getAliases(): array
    {
        return [self::NAME];
    }
}