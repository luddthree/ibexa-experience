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
final class FolderItemCreateInputType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'FolderItemCreateInput';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'shortName' => [
                    'type' => Type::string(),
                ],
                'shortDescription' => [
                    'type' => fn() => $services->getType('RichTextFieldInput'),
                ],
                'description' => [
                    'type' => fn() => $services->getType('RichTextFieldInput'),
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