<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Calendar.
 *
 * Example configuration:
 *
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          calendar:
 *              future_publication:
 *                  color: '#FF0000'
 *                  icon: '/assets/images/iconset.svg#holiday'
 *                  actions:
 *                      reschedule:
 *                          icon: '/assets/images/iconset.svg#reschdule'
 *                      unschedule:
 *                          icon: '/assets/images/iconset.svg#unschedule'
 * ```
 */
final class CalendarConfigParser extends AbstractParser
{
    private const EVENT_TYPES_PARAM_NAME = 'calendar.event_types';

    private const COLOR_PATTERN = '/^#[A-Fa-f0-9]{6}$/';

    private const INVALID_COLOR_FORMAT_ERROR = 'Expected color in the hex format e.g #FF0000';

    private const ROOT_NODE_KEY = 'calendar';
    private const ROOT_NODE_INFO = 'Calendar configuration';
    private const EVENT_TYPES_NODE_KEY = 'event_types';
    private const EVENT_TYPE_IDENTIFIER_NODE_KEY = 'identifier';
    private const EVENT_TYPE_COLOR_NODE_KEY = 'color';
    private const EVENT_TYPE_ICON_NODE_KEY = 'icon';
    private const EVENT_TYPE_ACTIONS_NODE_KEY = 'actions';
    private const EVENT_ACTION_IDENTIFIER_NODE_KEY = 'identifier';
    private const EVENT_ACTION_ICON_NODE_KEY = 'icon';

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $isColorFormatInvalid = static function (string $value): bool {
            return preg_match(self::COLOR_PATTERN, $value) !== 1;
        };

        $nodeBuilder
            ->arrayNode(self::ROOT_NODE_KEY)
                ->info(self::ROOT_NODE_INFO)
                ->children()
                    ->arrayNode(self::EVENT_TYPES_NODE_KEY)
                        ->useAttributeAsKey(self::EVENT_TYPE_IDENTIFIER_NODE_KEY)
                        ->prototype('array')
                        ->children()
                            ->scalarNode(self::EVENT_TYPE_COLOR_NODE_KEY)
                                ->defaultNull()
                                ->validate()
                                    ->ifTrue($isColorFormatInvalid)
                                    ->thenInvalid(self::INVALID_COLOR_FORMAT_ERROR)
                                ->end()
                            ->end()
                            ->scalarNode(self::EVENT_TYPE_ICON_NODE_KEY)
                                ->defaultNull()
                            ->end()
                            ->arrayNode(self::EVENT_TYPE_ACTIONS_NODE_KEY)
                                ->useAttributeAsKey(self::EVENT_ACTION_IDENTIFIER_NODE_KEY)
                                ->prototype('array')
                                ->children()
                                    ->scalarNode(self::EVENT_ACTION_ICON_NODE_KEY)
                                        ->defaultNull()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        // Workaround to be able to use Contextualizer::mapConfigArray() which only supports first level entries.
        if (isset($scopeSettings[self::ROOT_NODE_KEY][self::EVENT_TYPES_NODE_KEY])) {
            $scopeSettings[self::EVENT_TYPES_PARAM_NAME] = $scopeSettings[self::ROOT_NODE_KEY][self::EVENT_TYPES_NODE_KEY];
            unset($scopeSettings[self::ROOT_NODE_KEY][self::EVENT_TYPES_NODE_KEY]);
        }

        $contextualizer->setContextualParameter(
            self::EVENT_TYPES_PARAM_NAME,
            $currentScope,
            $scopeSettings[self::EVENT_TYPES_PARAM_NAME] ?? []
        );
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer): void
    {
        $contextualizer->mapConfigArray(self::EVENT_TYPES_PARAM_NAME, $config);
    }
}

class_alias(CalendarConfigParser::class, 'EzSystems\EzPlatformCalendarBundle\DependencyInjection\Configuration\Parser\CalendarConfigParser');
