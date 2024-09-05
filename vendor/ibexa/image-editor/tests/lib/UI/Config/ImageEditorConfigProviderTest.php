<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ImageEditor\UI\Config;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\ImageEditor\UI\Config\ImageEditorConfigProvider;
use PHPUnit\Framework\TestCase;

class ImageEditorConfigProviderTest extends TestCase
{
    /** @var \Ibexa\ImageEditor\UI\Config\ImageEditorConfigProvider */
    private $provider;

    /** @var \PHPUnit\Framework\MockObject\Builder\InvocationMocker */
    private $configResolver;

    protected function setUp(): void
    {
        $this->configResolver = $this
            ->createMock(ConfigResolverInterface::class);

        $this->configResolver
            ->method('getParameter')
            ->withConsecutive(
                ['image_editor.action_groups'],
                ['image_editor.image_quality'],
            )
            ->willReturnOnConsecutiveCalls(
                [
                    'default' => [
                        'label' => 'Default',
                        'actions' => [
                            'rotate' => [
                                'visible' => true,
                                'priority' => -100,
                                'buttons' => [],
                            ],
                            'flip' => [
                                'visible' => false,
                                'priority' => 0,
                                'buttons' => [],
                            ],
                            'crop' => [
                                'visible' => true,
                                'priority' => 100,
                                'buttons' => [
                                    'last' => [
                                        'label' => 'Last button',
                                        'visible' => true,
                                        'ratio' => ['x' => 0, 'y' => 2],
                                        'priority' => 0,
                                    ],
                                    'hiddenButton' => [
                                        'label' => 'Hidden Button',
                                        'ratio' => ['x' => 0, 'y' => 1],
                                        'visible' => false,
                                    ],
                                    'first' => [
                                        'label' => 'First button',
                                        'visible' => true,
                                        'priority' => 100,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                0.5
            );

        $this->provider = new ImageEditorConfigProvider($this->configResolver);
    }

    public function testGetConfig()
    {
        $this->assertEquals(
            [
            'imageQuality' => 0.5,
            'actionGroups' => [
                'default' => [
                    'label' => 'Default',
                    'actions' => [
                        'crop' => [
                            'visible' => true,
                            'priority' => 100,
                            'buttons' => [
                                'first' => [
                                    'label' => 'First button',
                                    'visible' => true,
                                    'priority' => 100,
                                ],
                                'last' => [
                                    'label' => 'Last button',
                                    'visible' => true,
                                    'ratio' => ['x' => 0, 'y' => 2],
                                    'priority' => 0,
                                ],
                            ],
                        ],
                        'rotate' => [
                            'priority' => -100,
                            'buttons' => [],
                            'visible' => true,
                        ],
                    ],
                ],
            ],
        ],
            $this->provider->getConfig()['config']
        );
    }
}

class_alias(ImageEditorConfigProviderTest::class, 'Ibexa\Platform\Tests\ImageEditor\UI\Config\ImageEditorConfigProviderTest');
