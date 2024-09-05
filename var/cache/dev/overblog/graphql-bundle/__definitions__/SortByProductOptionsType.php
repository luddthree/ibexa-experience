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
final class SortByProductOptionsType extends EnumType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'SortByProductOptions';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'values' => [
                'name' => [
                    'value' => '\\Ibexa\\Contracts\\ProductCatalog\\Values\\Product\\Query\\SortClause\\ProductName',
                    'description' => 'Sort by product name',
                ],
                'code' => [
                    'value' => '\\Ibexa\\Contracts\\ProductCatalog\\Values\\Product\\Query\\SortClause\\ProductCode',
                    'description' => 'Sort by product code',
                ],
                'availability' => [
                    'value' => '\\Ibexa\\Contracts\\ProductCatalog\\Values\\Product\\Query\\SortClause\\ProductAvailability',
                    'description' => 'Sort by product availability',
                ],
                'created_at' => [
                    'value' => '\\Ibexa\\Contracts\\ProductCatalog\\Values\\Product\\Query\\SortClause\\CreatedAt',
                    'description' => 'Sort by product creation date',
                ],
                '_desc' => [
                    'value' => 'descending',
                    'description' => 'Reverse the previous sorting option',
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