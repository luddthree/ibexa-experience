<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Calendar\UI;

use Ibexa\Bundle\Calendar\UI\CalendarConfigProvider;
use Ibexa\Calendar\EventType\EventTypeRegistryInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventAction;
use Ibexa\Tests\Calendar\Calendar\Stubs\TestEventType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;

class CalendarConfigProviderTest extends TestCase
{
    /** @var \Ibexa\Calendar\EventType\EventTypeRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $eventTypesRegistry;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Symfony\Component\Asset\Packages|\PHPUnit\Framework\MockObject\MockObject */
    private $packages;

    /** @var \Ibexa\Bundle\Calendar\UI\CalendarConfigProvider */
    private $configProvider;

    protected function setUp(): void
    {
        $this->eventTypesRegistry = $this->createMock(EventTypeRegistryInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->packages = $this->createMock(Packages::class);
        $this->packages
            ->method('getUrl')
            ->willReturnCallback(static function (string $uri) {
                return 'https://cdn.example.com' . $uri;
            });

        $this->configProvider = new CalendarConfigProvider(
            $this->eventTypesRegistry,
            $this->configResolver,
            $this->packages
        );
    }

    /**
     * @dataProvider dataProviderForGetConfig
     */
    public function testGetConfig(array $config, array $types, array $expectedConfig): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('calendar.event_types')
            ->willReturn($config);

        $this->eventTypesRegistry
            ->method('getTypes')
            ->willReturn($types);

        $this->assertEquals($expectedConfig, $this->configProvider->getConfig());
    }

    public function dataProviderForGetConfig(): array
    {
        $defaults = [
            'label' => null,
            'color' => null,
            'actions' => [],
            'isSelectable' => false,
            'icon' => null,
        ];

        return [
            [
                [],
                [],
                ['types' => []],
            ],
            [
                [],
                [
                    new TestEventType('foo'),
                    new TestEventType('bar'),
                    new TestEventType('baz'),
                ],
                [
                    'types' => [
                        'foo' => array_replace($defaults, ['label' => 'foo']),
                        'bar' => array_replace($defaults, ['label' => 'bar']),
                        'baz' => array_replace($defaults, ['label' => 'baz']),
                    ],
                ],
            ],
            [
                [
                    'foo' => [
                    ],
                    'bar' => [
                        'color' => '#FF0000',
                        'icon' => '/assets/images/iconset.svg#icon-bar',
                    ],
                    'baz' => [
                        'color' => '#FFFFFF',
                        'icon' => '/assets/images/iconset.svg#icon-baz',
                        'actions' => [
                            'rab' => [
                                'icon' => '/assets/images/iconset.svg#icon-rab',
                            ],
                        ],
                    ],
                ],
                [
                    new TestEventType('foo'),
                    new TestEventType('bar'),
                    new TestEventType('baz', [
                        new TestEventAction('oof'),
                        new TestEventAction('rab'),
                    ]),
                ],
                [
                    'types' => [
                        'foo' => array_replace($defaults, ['label' => 'foo']),
                        'bar' => array_replace($defaults, [
                            'label' => 'bar',
                            'color' => '#FF0000',
                            'icon' => 'https://cdn.example.com/assets/images/iconset.svg#icon-bar',
                        ]),
                        'baz' => array_replace($defaults, [
                            'label' => 'baz',
                            'color' => '#FFFFFF',
                            'actions' => [
                                'oof' => [
                                    'icon' => null,
                                    'label' => 'oof',
                                ],
                                'rab' => [
                                    'icon' => 'https://cdn.example.com/assets/images/iconset.svg#icon-rab',
                                    'label' => 'rab',
                                ],
                            ],
                            'icon' => 'https://cdn.example.com/assets/images/iconset.svg#icon-baz',
                            'isSelectable' => true,
                        ]),
                    ],
                ],
            ],
        ];
    }
}

class_alias(CalendarConfigProviderTest::class, 'EzSystems\EzPlatformCalendarBundle\Tests\UI\CalendarConfigProviderTest');
