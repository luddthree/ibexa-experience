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
final class ContentTypeType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ContentType';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa repository ContentType.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The content type\'s unique ID.',
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return (($value->getDescription($args["language"])) ? ($value->getDescription($args["language"])) : (""));
                    },
                    'description' => 'The content type\'s description',
                    'args' => [
                        [
                            'name' => 'language',
                            'type' => fn() => $services->getType('RepositoryLanguage'),
                            'defaultValue' => null,
                        ],
                    ],
                ],
                'fieldDefinitions' => [
                    'type' => fn() => Type::listOf($services->getType('FieldDefinition')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getFieldDefinitions();
                    },
                    'description' => 'The ContentType\'s Field Definitions.',
                ],
                'status' => [
                    'type' => Type::int(),
                    'description' => 'The status of the content type. One of ContentType::STATUS_DEFINED|ContentType::STATUS_DRAFT|ContentType::STATUS_MODIFIED.',
                ],
                'identifier' => [
                    'type' => Type::string(),
                    'description' => 'The identifier of the content type.',
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return (($value->getName($args["language"])) ? ($value->getName($args["language"])) : (""));
                    },
                    'description' => 'The content type\'s name in the main language',
                    'args' => [
                        [
                            'name' => 'language',
                            'type' => fn() => $services->getType('RepositoryLanguage'),
                            'defaultValue' => null,
                        ],
                    ],
                ],
                'names' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => 'The content type\'s names in all languages',
                ],
                'creationDate' => [
                    'type' => fn() => $services->getType('DateTime'),
                    'description' => 'The date of the creation of this content type.',
                ],
                'modificationDate' => [
                    'type' => fn() => $services->getType('DateTime'),
                    'description' => 'the date of the last modification of this content type.',
                ],
                'creatorId' => [
                    'type' => Type::int(),
                    'description' => 'The user id of the creator of this content type.',
                ],
                'creator' => [
                    'type' => fn() => $services->getType('User'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserById", ...[0 => $value->creatorId]);
                    },
                    'description' => 'The user who created this content type.',
                ],
                'modifierId' => [
                    'type' => Type::int(),
                    'description' => 'The user id of the user which has last modified this content type',
                ],
                'modifier' => [
                    'type' => fn() => $services->getType('User'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserById", ...[0 => $value->modifierId]);
                    },
                    'description' => 'The user which has last modified this content type',
                ],
                'remoteId' => [
                    'type' => Type::string(),
                    'description' => 'A global unique id of the content type.',
                ],
                'urlAliasSchema' => [
                    'type' => Type::string(),
                    'description' => 'URL alias schema. If nothing is provided, nameSchema will be used instead.',
                ],
                'nameSchema' => [
                    'type' => Type::string(),
                    'description' => 'The name schema.',
                ],
                'isContainer' => [
                    'type' => Type::boolean(),
                    'description' => 'This flag hints to UIs if type may have children or not.',
                ],
                'mainLanguageCode' => [
                    'type' => Type::string(),
                    'description' => 'The main language of the content type names and description used for fallback.',
                ],
                'defaultAlwaysAvailable' => [
                    'type' => Type::boolean(),
                    'description' => 'If an instance of a content type is created the always available flag is set by default this this value.',
                ],
                'defaultSortField' => [
                    'type' => Type::int(),
                    'description' => 'Specifies which property the child locations should be sorted on by default when created. Valid values are found at {@link Location::SORT_FIELD_*}',
                ],
                'defaultSortOrder' => [
                    'type' => Type::int(),
                    'description' => 'Specifies whether the sort order should be ascending or descending by default when created. Valid values are {@link Location::SORT_ORDER_*}',
                ],
                'groups' => [
                    'type' => fn() => Type::listOf($services->getType('ContentTypeGroup')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->contentTypeGroups;
                    },
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