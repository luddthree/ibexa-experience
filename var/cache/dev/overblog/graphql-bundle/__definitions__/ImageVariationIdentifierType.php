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
final class ImageVariationIdentifierType extends EnumType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ImageVariationIdentifier';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'values' => [
                'original' => [
                    'value' => 'original',
                ],
                'reference' => [
                    'value' => 'reference',
                ],
                'small' => [
                    'value' => 'small',
                ],
                'tiny' => [
                    'value' => 'tiny',
                ],
                'medium' => [
                    'value' => 'medium',
                ],
                'large' => [
                    'value' => 'large',
                ],
                'gallery' => [
                    'value' => 'gallery',
                ],
                'ezplatform_admin_ui_profile_picture_user_menu' => [
                    'value' => 'ezplatform_admin_ui_profile_picture_user_menu',
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