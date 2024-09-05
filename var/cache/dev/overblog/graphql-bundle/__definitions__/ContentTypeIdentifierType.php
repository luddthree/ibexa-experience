<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\EnumType;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ContentTypeIdentifierType extends EnumType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ContentTypeIdentifier';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'values' => [
                'article' => [
                    'description' => 'No description available',
                    'value' => 'article',
                ],
                'folder' => [
                    'description' => 'No description available',
                    'value' => 'folder',
                ],
                'form' => [
                    'description' => 'No description available',
                    'value' => 'form',
                ],
                'tag' => [
                    'description' => 'No description available',
                    'value' => 'tag',
                ],
                'landing_page' => [
                    'value' => 'landing_page',
                ],
                'product_category_tag' => [
                    'description' => 'No description available',
                    'value' => 'product_category_tag',
                ],
                'editor' => [
                    'value' => 'editor',
                ],
                'user' => [
                    'description' => 'No description available',
                    'value' => 'user',
                ],
                'user_group' => [
                    'description' => 'No description available',
                    'value' => 'user_group',
                ],
                'file' => [
                    'description' => 'No description available',
                    'value' => 'file',
                ],
                'image' => [
                    'description' => 'No description available',
                    'value' => 'image',
                ],
                'customer_portal' => [
                    'description' => 'No description available',
                    'value' => 'customer_portal',
                ],
                'customer_portal_page' => [
                    'description' => 'No description available',
                    'value' => 'customer_portal_page',
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