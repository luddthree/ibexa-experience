<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ArticleItemCreateInputType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ArticleItemCreateInput';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'title' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'shortTitle' => [
                    'type' => Type::string(),
                ],
                'author' => [
                    'type' => fn() => Type::listOf($services->getType('AuthorInput')),
                ],
                'intro' => [
                    'type' => fn() => Type::nonNull($services->getType('RichTextFieldInput')),
                ],
                'body' => [
                    'type' => fn() => $services->getType('RichTextFieldInput'),
                ],
                'enableComments' => [
                    'type' => Type::boolean(),
                ],
                'image' => [
                    'type' => Type::int(),
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