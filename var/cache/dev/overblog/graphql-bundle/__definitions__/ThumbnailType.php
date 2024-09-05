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
final class ThumbnailType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'Thumbnail';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'uri' => [
                    'type' => Type::string(),
                    'description' => 'The image\'s URI (example: \'https://example.com/var/site/storage/images/_aliases/small/9/8/1/0/189-1-eng-GB/image.png\')',
                ],
                'width' => [
                    'type' => Type::int(),
                    'description' => 'The width as number of pixels (example: 320)',
                ],
                'height' => [
                    'type' => Type::int(),
                    'description' => 'The height as number of pixels (example: 200)',
                ],
                'alternativeText' => [
                    'type' => Type::string(),
                ],
                'mimeType' => [
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