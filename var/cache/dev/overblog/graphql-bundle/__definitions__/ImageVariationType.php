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
final class ImageVariationType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ImageVariation';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'uri' => [
                    'type' => Type::string(),
                    'description' => 'The image\'s URI (example: \'https://example.com/var/site/storage/images/_aliases/small/9/8/1/0/189-1-eng-GB/image.png\')',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'The name of the image alias (example: \'original\')',
                ],
                'mimeType' => [
                    'type' => Type::string(),
                    'description' => 'The MIME type (for example \'image/png\')',
                ],
                'fileName' => [
                    'type' => Type::string(),
                    'description' => 'The name of the file (for example \'my_image.png\')',
                ],
                'lastModified' => [
                    'type' => fn() => $services->getType('DateTime'),
                    'description' => 'When the variation was last modified',
                ],
                'width' => [
                    'type' => Type::int(),
                    'description' => 'The width as number of pixels (example: 320)',
                ],
                'height' => [
                    'type' => Type::int(),
                    'description' => 'The height as number of pixels (example: 200)',
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