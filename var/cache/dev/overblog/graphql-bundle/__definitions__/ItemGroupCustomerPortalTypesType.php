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
final class ItemGroupCustomerPortalTypesType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupCustomerPortalTypes';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'customerPortal' => [
                    'type' => fn() => $services->getType('CustomerPortalItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "customer_portal"]]);
                    },
                ],
                'customerPortalPage' => [
                    'type' => fn() => $services->getType('CustomerPortalPageItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "customer_portal_page"]]);
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