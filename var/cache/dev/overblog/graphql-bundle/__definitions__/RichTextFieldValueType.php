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
final class RichTextFieldValueType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'RichTextFieldValue';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'text' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                    'description' => 'String representation of the value',
                ],
                'xml' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                    'description' => 'The raw docbook xml',
                ],
                'plaintext' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("RichTextXmlToPlainText", ...[0 => $value->xml]);
                    },
                    'description' => 'Plain text representation of the value, without tags. Warning: the text representation may not be perfect.',
                ],
                'html5' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("RichTextXmlToHtml5", ...[0 => $value->xml]);
                    },
                    'description' => 'HTML5 representation.',
                ],
                'html5_edit' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("RichTextXmlToHtml5Edit", ...[0 => $value->xml]);
                    },
                    'description' => 'Editable HTML5 representation.',
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