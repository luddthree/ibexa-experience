<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Config\Repository;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolver;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Config\Repository\RepositoryConfigResolver
 */
final class RepositoryConfigResolverTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    private RepositoryConfigResolverInterface $repositoryConfigResolver;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->repositoryConfigResolver = new RepositoryConfigResolver($this->configResolver);
    }

    public function testUseRemoteId(): void
    {
        $this->mockConfigResolverHasParameter(true);
        $this->mockConfigResolverGetParameter(true);

        self::assertTrue($this->repositoryConfigResolver->useRemoteId());
    }

    public function testDoNotUseRemoteIdWhenParameterIsNotDefined(): void
    {
        $this->mockConfigResolverHasParameter(false);

        self::assertFalse($this->repositoryConfigResolver->useRemoteId());
    }

    public function testDoNotUseRemoteIdWhenParameterValueIsFalse(): void
    {
        $this->mockConfigResolverHasParameter(true);
        $this->mockConfigResolverGetParameter(false);

        self::assertFalse($this->repositoryConfigResolver->useRemoteId());
    }

    private function mockConfigResolverHasParameter(bool $hasParameter): void
    {
        $this->configResolver
            ->expects(self::once())
            ->method('hasParameter')
            ->with('personalization.repository.content.use_remote_id')
            ->willReturn($hasParameter);
    }

    private function mockConfigResolverGetParameter(bool $useRemoteId): void
    {
        $this->configResolver
            ->expects(self::once())
            ->method('getParameter')
            ->with('personalization.repository.content.use_remote_id')
            ->willReturn($useRemoteId);
    }
}
