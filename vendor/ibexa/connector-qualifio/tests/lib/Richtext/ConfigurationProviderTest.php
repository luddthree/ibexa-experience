<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ConnectorQualifio\Richtext;

use Ibexa\ConnectorQualifio\Richtext\ConfigurationProvider;
use Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface;
use PHPUnit\Framework\TestCase;

final class ConfigurationProviderTest extends TestCase
{
    public function testRemovesQualifioEntryWhenDisabled(): void
    {
        $qualifioService = $this->createMock(QualifioServiceInterface::class);
        $qualifioService
            ->expects(self::once())
            ->method('isConfigured')
            ->willReturn(false);

        $inputConfiguration = [
            'qualifio' => [
                'foo' => 'bar',
            ],
            'foo' => ['bar' => 42],
        ];
        $expectedConfiguration = [
            'foo' => ['bar' => 42],
        ];

        $provider = new ConfigurationProvider($qualifioService);
        $changedConfiguration = $provider->getConfiguration($inputConfiguration);
        self::assertSame($expectedConfiguration, $changedConfiguration);
    }

    public function testDoesntChangeConfigurationWhenQualifioIsNotInIt(): void
    {
        $qualifioService = $this->createMock(QualifioServiceInterface::class);
        $qualifioService
            ->expects(self::never())
            ->method('isConfigured');

        $inputConfiguration = [
            'foo' => ['bar' => 42],
        ];

        $provider = new ConfigurationProvider($qualifioService);
        $changedConfiguration = $provider->getConfiguration($inputConfiguration);
        self::assertSame($inputConfiguration, $changedConfiguration);
    }

    public function testRetainsQualifioEntryWhenEnabled(): void
    {
        $qualifioService = $this->createMock(QualifioServiceInterface::class);
        $qualifioService
            ->expects(self::once())
            ->method('isConfigured')
            ->willReturn(true);

        $inputConfiguration = [
            'qualifio' => [
                'foo' => 'bar',
            ],
            'foo' => ['bar' => 42],
        ];

        $provider = new ConfigurationProvider($qualifioService);
        $changedConfiguration = $provider->getConfiguration($inputConfiguration);
        self::assertSame($inputConfiguration, $changedConfiguration);
    }
}
