<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Cache;

use Ibexa\Personalization\Cache\CachedCustomerInformationService;
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Value\Customer\BaseInformation;

final class CachedCustomerInformationServiceTest extends AbstractCacheTestCase
{
    /** @var \Ibexa\Personalization\Cache\CachedCustomerInformationService */
    private $cachedCustomerInformationService;

    /** @var \Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $innerCustomerInformationService;

    /** @var int */
    private $customerId;

    public function setUp(): void
    {
        parent::setUp();

        $this->innerCustomerInformationService = $this->createMock(CustomerInformationServiceInterface::class);
        $this->cachedCustomerInformationService = new CachedCustomerInformationService(
            $this->cache,
            $this->persistenceHandler,
            $this->persistenceLogger,
            $this->cacheIdentifierGenerator,
            $this->cacheIdentifierSanitizer,
            $this->locationPathConverter,
            $this->innerCustomerInformationService
        );
        $this->customerId = 1234;
    }

    public function testCreateInstanceCustomerInformationServiceDecorator(): void
    {
        self::assertInstanceOf(
            CustomerInformationServiceInterface::class,
            $this->cachedCustomerInformationService
        );
    }

    /**
     * @dataProvider providerForTestGetBaseInformation
     */
    public function testGetBaseInformation(BaseInformation $baseInformation): void
    {
        $key = 'ibexa-base-customer-information-1234';

        $this->cache
            ->method('getItem')
            ->with($key)
            ->willReturn(
                $this->getCacheItem($key)
            );

        $this->innerCustomerInformationService
            ->method('getBaseInformation')
            ->with($this->customerId)
            ->willReturn($baseInformation);

        $fromCache = $this->cachedCustomerInformationService->getBaseInformation($this->customerId);

        self::assertInstanceOf(
            BaseInformation::class,
            $fromCache
        );
        self::assertEquals(
            $baseInformation,
            $fromCache
        );
    }

    public function providerForTestGetBaseInformation(): iterable
    {
        yield [
            BaseInformation::fromArray([
                'id' => '12345',
                'version' => 'EXTENDED',
                'website' => 'Test version',
                'alphanumericItems' => 'NUMERIC',
                'solutionType' => 'ebh',
                'enabled' => 'true',
            ]),
        ];
        yield [
            BaseInformation::fromArray([
                'id' => '',
                'version' => '',
                'website' => '',
                'alphanumericItems' => '',
                'solutionType' => '',
                'enabled' => '',
            ]),
        ];
    }
}

class_alias(CachedCustomerInformationServiceTest::class, 'Ibexa\Platform\Tests\Personalization\Cache\CachedCustomerInformationServiceTest');
