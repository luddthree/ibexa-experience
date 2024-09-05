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
final class DateFormatConstantType extends EnumType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'DateFormatConstant';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'values' => [
                'atom' => [
                    'value' => \constant("DateTime::ATOM"),
                    'description' => 'Y-m-d\\TH:i:sP',
                ],
                'cookie' => [
                    'value' => \constant("DateTime::COOKIE"),
                    'description' => 'l, d-M-Y H:i:s T',
                ],
                'iso8601' => [
                    'value' => \constant("DateTime::ISO8601"),
                    'description' => 'Y-M-D\\TH:I:SO',
                ],
                'rfc822' => [
                    'value' => \constant("DateTime::RFC822"),
                    'description' => 'D, D M Y H:I:S O',
                ],
                'rfc850' => [
                    'value' => \constant("DateTime::RFC850"),
                    'description' => 'L, D-M-Y H:I:S T',
                ],
                'rfc1036' => [
                    'value' => \constant("DateTime::RFC1036"),
                    'description' => 'D, D M Y H:I:S O',
                ],
                'rfc1123' => [
                    'value' => \constant("DateTime::RFC1123"),
                    'description' => 'D, D M Y H:I:S O',
                ],
                'rfc2822' => [
                    'value' => \constant("DateTime::RFC2822"),
                    'description' => 'D, D M Y H:I:S O',
                ],
                'rfc3339' => [
                    'value' => \constant("DateTime::RFC3339"),
                    'description' => 'Y-M-D\\TH:I:SP',
                ],
                'rfc3339_extended' => [
                    'value' => \constant("DateTime::RFC3339_EXTENDED"),
                    'description' => 'Y-M-D\\TH:I:S.VP',
                ],
                'rss' => [
                    'value' => \constant("DateTime::RSS"),
                    'description' => 'D, D M Y H:I:S O',
                ],
                'w3c' => [
                    'value' => \constant("DateTime::W3C"),
                    'description' => 'Y-M-D\\TH:I:SP',
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