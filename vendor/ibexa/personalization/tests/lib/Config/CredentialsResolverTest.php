<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Config;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Personalization\Config\CredentialsResolver;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CredentialsResolverTest extends TestCase
{
    /** @var \Ibexa\Personalization\Config\CredentialsResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $credentialsResolverMock;

    /** @var \Psr\Log\NullLogger|\PHPUnit\Framework\MockObject\MockObject */
    private $loggerMock;

    /** @var array */
    private $credentials;

    /** @var array */
    private $invalidCredentials;

    protected function setUp(): void
    {
        $this->credentialsResolverMock = $this->getMockForAbstractClass(
            CredentialsResolver::class,
            [
                $this->getMockBuilder(ConfigResolverInterface::class)->getMock(),
                new NullLogger(),
            ]
        );
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
        $this->credentials = [
            'firstCredential' => 'firstCredential',
            'secondCredential' => 'secondCredential',
        ];
        $this->invalidCredentials = [
            'firstCredential' => '',
            'secondCredential' => '',
        ];
    }

    public function testCreateCredentialsResolverInstance()
    {
        $this->assertInstanceOf(CredentialsResolver::class, $this->credentialsResolverMock);
    }

    /**
     * Test for hasCredentials() method.
     */
    public function testShouldReturnTrueWhenRequiredCredentialsAreSet()
    {
        $this->credentialsResolverMock
            ->expects($this->atLeastOnce())
            ->method('getRequiredCredentials')
            ->willReturn($this->credentials);

        $this->assertTrue($this->credentialsResolverMock->hasCredentials());
    }

    /**
     * Test for hasCredentials() method.
     */
    public function testReturnFalseWhenOneOfRequiredCredentialsAreMissing()
    {
        $this->credentialsResolverMock
            ->expects($this->atLeastOnce())
            ->method('getRequiredCredentials')
            ->willReturn($this->invalidCredentials);

        $this->assertFalse($this->credentialsResolverMock->hasCredentials());
    }
}

class_alias(CredentialsResolverTest::class, 'EzSystems\EzRecommendationClient\Tests\Config\CredentialsResolverTest');
