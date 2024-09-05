<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Config;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Personalization\Config\PersonalizationClientCredentialsResolver;
use Ibexa\Personalization\Value\Config\PersonalizationClientCredentials;
use PHPUnit\Framework\TestCase;

class PersonalizationClientCredentialsResolverTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    protected function setUp(): void
    {
        $this->configResolver = $this->getMockBuilder(ConfigResolverInterface::class)->getMock();

        parent::setUp();
    }

    public function testCreatePersonalizationClientCredentialsResolverInstance()
    {
        $this->assertInstanceOf(PersonalizationClientCredentialsResolver::class, new PersonalizationClientCredentialsResolver(
            $this->configResolver,
        ));
    }

    /**
     * Test for getCredentials() method.
     */
    public function testReturnGetPersonalizationClientCredentials()
    {
        $this->configResolver
            ->expects($this->at(0))
            ->method('getParameter')
            ->with('personalization.authentication.customer_id')
            ->willReturn(12345);

        $this->configResolver
            ->expects($this->at(1))
            ->method('getParameter')
            ->with('personalization.authentication.license_key')
            ->willReturn('12345-12345-12345-12345');

        $credentialsResolver = new PersonalizationClientCredentialsResolver(
            $this->configResolver,
        );

        $this->assertInstanceOf(PersonalizationClientCredentials::class, $credentialsResolver->getCredentials());
    }

    /**
     * Test for getCredentials() method.
     */
    public function testReturnNullWhenCredentialsAreNotSet()
    {
        $credentialsResolver = new PersonalizationClientCredentialsResolver(
            $this->configResolver,
        );

        $this->assertNull($credentialsResolver->getCredentials());
    }
}

class_alias(PersonalizationClientCredentialsResolverTest::class, 'EzSystems\EzRecommendationClient\Tests\Config\PersonalizationClientCredentialsResolverTest');
