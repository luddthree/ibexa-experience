<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Permission;

use Ibexa\Personalization\Permission\CustomerTypeChecker;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Value\Customer\BaseInformation;
use PHPUnit\Framework\TestCase;

final class CustomerTypeCheckerTest extends TestCase
{
    /** @var \Ibexa\Personalization\Permission\CustomerTypeCheckerInterface */
    private $customerTypeChecker;

    /** @var \Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $customerInformationService;

    /** @var int */
    private $customerId;

    public function setUp(): void
    {
        parent::setUp();

        $this->customerInformationService = $this->createMock(CustomerInformationServiceInterface::class);
        $this->customerTypeChecker = new CustomerTypeChecker(
            $this->customerInformationService
        );
        $this->customerId = 12345;
    }

    public function testCreateInstanceModeChecker(): void
    {
        self::assertInstanceOf(
            CustomerTypeCheckerInterface::class,
            $this->customerTypeChecker
        );
    }

    /**
     * @dataProvider providerValidCommerceSolutionTypesForIsCommerce
     */
    public function testIsCommerceForValidSolutionType(string $solutionType): void
    {
        $this->getBaseInformation($solutionType);

        self::assertTrue(
            $this->customerTypeChecker->isCommerce($this->customerId)
        );
    }

    /**
     * @dataProvider providerInvalidSolutionTypes
     */
    public function testIsCommerceModeForInvalidSolutionType(string $solutionType): void
    {
        $this->getBaseInformation($solutionType);

        self::assertFalse(
            $this->customerTypeChecker->isCommerce($this->customerId)
        );
    }

    /**
     * @dataProvider providerValidPublisherSolutionTypesForIsPublisher
     */
    public function testIsPublisherForValidSolutionType(?string $solutionType = null): void
    {
        $this->getBaseInformation($solutionType);

        self::assertTrue(
            $this->customerTypeChecker->isPublisher($this->customerId)
        );
    }

    /**
     * @dataProvider providerInvalidSolutionTypes
     */
    public function testIsPublisherForInvalidSolutionType(?string $solutionType = null): void
    {
        $this->getBaseInformation($solutionType);

        self::assertFalse(
            $this->customerTypeChecker->isPublisher($this->customerId)
        );
    }

    public function providerValidCommerceSolutionTypesForIsCommerce(): iterable
    {
        yield ['ebl'];
        yield ['ebl2'];
        yield ['ebh'];
    }

    public function providerValidPublisherSolutionTypesForIsPublisher(): iterable
    {
        yield ['newsl'];
        yield ['newsh'];
        yield [];
    }

    public function providerInvalidSolutionTypes(): iterable
    {
        yield ['invalid_solution_type'];
        yield ['invalid_solution_Type_2'];
    }

    private function getBaseInformation(?string $solutionType = null): void
    {
        $this->customerInformationService
            ->method('getBaseInformation')
            ->with($this->customerId)
            ->willReturn(BaseInformation::fromArray(
                ['solutionType' => $solutionType]
            ));
    }
}

class_alias(CustomerTypeCheckerTest::class, 'Ibexa\Platform\Tests\Personalization\Permission\CustomerTypeCheckerTest');
