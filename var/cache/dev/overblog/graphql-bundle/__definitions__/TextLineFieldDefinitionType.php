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
final class TextLineFieldDefinitionType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'TextLineFieldDefinition';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa repository FieldDefinition.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::int(),
                    'description' => 'The id of the field definition.',
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return (($value->getName($args["language"])) ? ($value->getName($args["language"])) : (""));
                    },
                    'description' => 'The field definition name, either in the most prioritized language, or in the language given as an argument',
                    'args' => [
                        [
                            'name' => 'language',
                            'type' => fn() => $services->getType('RepositoryLanguage'),
                            'defaultValue' => null,
                        ],
                    ],
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return (($value->getDescription($args["language"])) ? ($value->getDescription($args["language"])) : (""));
                    },
                    'description' => 'The field definition description, either in the most prioritized language, or in the language given as an argument',
                    'args' => [
                        [
                            'name' => 'language',
                            'type' => fn() => $services->getType('RepositoryLanguage'),
                            'defaultValue' => null,
                        ],
                    ],
                ],
                'identifier' => [
                    'type' => Type::string(),
                    'description' => 'The system identifier of the field definition.',
                ],
                'fieldGroup' => [
                    'type' => Type::string(),
                    'description' => 'The field group name.',
                ],
                'position' => [
                    'type' => Type::int(),
                    'description' => 'The position of the field definition in the content type',
                ],
                'fieldTypeIdentifier' => [
                    'type' => Type::string(),
                    'description' => 'The identifier of the field type (ezstring, ezinteger...).',
                ],
                'isThumbnail' => [
                    'type' => Type::boolean(),
                    'description' => 'Indicates if this field can be a thumbnail.',
                ],
                'isTranslatable' => [
                    'type' => Type::boolean(),
                    'description' => 'Indicates if fields of this definition are translatable.',
                ],
                'isRequired' => [
                    'type' => Type::boolean(),
                    'description' => 'Indicates if this field is used for information collection',
                ],
                'isSearchable' => [
                    'type' => Type::boolean(),
                    'description' => 'Indicates if the content is searchable by this attribute',
                ],
                'constraints' => [
                    'type' => fn() => $services->getType('TextLineFieldDefinitionConstraints'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getValidatorConfiguration();
                    },
                ],
                'defaultValue' => [
                    'type' => Type::string(),
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