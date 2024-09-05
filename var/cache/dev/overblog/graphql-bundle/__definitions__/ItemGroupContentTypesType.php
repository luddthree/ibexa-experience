<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\ObjectType;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ItemGroupContentTypesType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupContentTypes';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'article' => [
                    'type' => fn() => $services->getType('ArticleItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "article"]]);
                    },
                ],
                'folder' => [
                    'type' => fn() => $services->getType('FolderItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "folder"]]);
                    },
                ],
                'form' => [
                    'type' => fn() => $services->getType('FormItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "form"]]);
                    },
                ],
                'tag' => [
                    'type' => fn() => $services->getType('TagItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "tag"]]);
                    },
                ],
                'landingPage' => [
                    'type' => fn() => $services->getType('LandingPageItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "landing_page"]]);
                    },
                ],
                'productCategoryTag' => [
                    'type' => fn() => $services->getType('ProductCategoryTagItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "product_category_tag"]]);
                    },
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