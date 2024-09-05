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
final class MediaFieldInputType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'MediaFieldInput';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'file' => [
                    'type' => fn() => Type::nonNull($services->getType('FileUpload')),
                    'description' => 'The media file',
                ],
                'fileName' => [
                    'type' => Type::string(),
                    'description' => 'The media\'s name. Will use the upload file\'s name if not provided.',
                ],
                'hasController' => [
                    'type' => Type::boolean(),
                    'description' => 'If the media has a controller when being displayed',
                    'defaultValue' => false,
                ],
                'autoplay' => [
                    'type' => Type::boolean(),
                    'description' => 'If the media should be automatically played',
                    'defaultValue' => false,
                ],
                'loop' => [
                    'type' => Type::boolean(),
                    'description' => 'If the media should be played in a loop',
                    'defaultValue' => false,
                ],
                'height' => [
                    'type' => Type::int(),
                    'description' => 'Height of the media',
                ],
                'width' => [
                    'type' => Type::int(),
                    'description' => 'Width of the media',
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