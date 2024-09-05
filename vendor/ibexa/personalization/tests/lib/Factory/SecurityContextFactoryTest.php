<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Factory;

use Ibexa\Personalization\Factory\PersonalizationSecurityContextFactory;
use Ibexa\Personalization\Factory\SecurityContextFactory;
use Ibexa\Personalization\Value\Security\PersonalizationSecurityContext;
use PHPUnit\Framework\TestCase;

final class SecurityContextFactoryTest extends TestCase
{
    /** @var \Ibexa\Personalization\Factory\SecurityContextFactory|\PHPUnit\Framework\MockObject\MockObject */
    private $securityContextFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->securityContextFactory = $this->createMock(SecurityContextFactory::class);
    }

    public function testCreateInstanceSecurityContextFactory(): void
    {
        $this->assertInstanceOf(SecurityContextFactory::class, new PersonalizationSecurityContextFactory());
    }

    public function testCreateSecurityContextObject(): void
    {
        $this->securityContextFactory
            ->method('createSecurityContextObject')
            ->willReturn(
                new PersonalizationSecurityContext([
                    'customerId' => 1234,
                ])
            );

        $securityContextObject = $this->securityContextFactory->createSecurityContextObject(1234);

        $this->assertInstanceOf(PersonalizationSecurityContext::class, $securityContextObject);
        $this->assertEquals(
            new PersonalizationSecurityContext([
                'customerId' => 1234,
            ]),
            $securityContextObject
        );
    }
}

class_alias(SecurityContextFactoryTest::class, 'Ibexa\Platform\Tests\Personalization\Factory\SecurityContextFactoryTest');
