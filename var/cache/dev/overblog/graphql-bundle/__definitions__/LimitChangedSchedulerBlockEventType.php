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
final class LimitChangedSchedulerBlockEventType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'LimitChangedSchedulerBlockEvent';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'id' => [
                    'type' => Type::string(),
                ],
                'type' => [
                    'type' => Type::string(),
                ],
                'dateTime' => [
                    'type' => fn() => $services->getType('DateTime'),
                ],
                'limit' => [
                    'type' => Type::int(),
                ],
            ],
            'interfaces' => fn() => [
                $services->getType('SchedulerBlockEvent'),
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