<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Calendar\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Calendar\DependencyInjection\Configuration\Parser\CalendarConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class CalendarConfigParserTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([new CalendarConfigParser()]),
        ];
    }

    /**
     * @dataProvider dataProviderForSettings
     */
    public function testSettings(array $config, array $expected): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => $config,
            ],
        ]);

        foreach ($expected as $key => $val) {
            $this->assertConfigResolverParameterValue($key, $val, 'ibexa_demo_site');
        }
    }

    public function dataProviderForSettings(): array
    {
        return [
            [
                [],
                ['calendar.event_types' => []],
            ],
            [
                [
                    'calendar' => [
                        'event_types' => [
                            'foo' => [
                                'color' => '#FF0000',
                                'icon' => '/assets/images/iconset.svg#icon-foo',
                                'actions' => [
                                    'a' => [],
                                    'b' => [
                                        'icon' => '/assets/images/iconset.svg#icon-a',
                                    ],
                                ],
                            ],
                            'bar' => [
                                'color' => '#000000',
                            ],
                        ],
                    ],
                ],
                [
                    'calendar.event_types' => [
                        'foo' => [
                            'color' => '#FF0000',
                            'icon' => '/assets/images/iconset.svg#icon-foo',
                            'actions' => [
                                'a' => [
                                    'icon' => null,
                                ],
                                'b' => [
                                    'icon' => '/assets/images/iconset.svg#icon-a',
                                ],
                            ],
                        ],
                        'bar' => [
                            'color' => '#000000',
                            'icon' => null,
                            'actions' => [
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForColorSettingsWithInvalidFormatThrowsException
     */
    public function testColorSettingsWithInvalidFormatThrowsException(array $config): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Expected color in the hex format e.g #FF0000');

        $this->load([
            'system' => [
                'ibexa_demo_site' => $config,
            ],
        ]);
    }

    public function dataProviderForColorSettingsWithInvalidFormatThrowsException(): array
    {
        return [
            [
                [
                    'calendar' => [
                        'event_types' => [
                            'foo' => [
                                'color' => '#XXXXXX',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

class_alias(CalendarConfigParserTest::class, 'EzSystems\EzPlatformCalendarBundle\Tests\DependencyInjection\Configuration\Parser\CalendarConfigParserTest');
