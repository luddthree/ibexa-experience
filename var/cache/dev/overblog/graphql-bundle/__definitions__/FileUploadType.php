<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\CustomScalarType;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class FileUploadType extends CustomScalarType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'FileUpload';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'scalarType' => (new \ReflectionClass("Overblog\\GraphQLBundle\\Upload\\Type\\GraphQLUploadType"))->newInstanceArgs([]),
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