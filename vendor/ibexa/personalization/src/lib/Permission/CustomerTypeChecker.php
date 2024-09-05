<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Permission;

use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;

/**
 * @internal
 */
final class CustomerTypeChecker implements CustomerTypeCheckerInterface
{
    private const PUBLISHER_ALLOWED_SOLUTION_TYPES = ['newsl', 'newsh'];
    private const COMMERCE_ALLOWED_SOLUTION_TYPES = ['ebl', 'ebl2', 'ebh'];

    /** @var \Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface */
    private $customerInformationService;

    public function __construct(CustomerInformationServiceInterface $customerInformationService)
    {
        $this->customerInformationService = $customerInformationService;
    }

    public function isCommerce(int $customerId): bool
    {
        $solutionType = $this->getSolutionType($customerId);

        return in_array($solutionType, self::COMMERCE_ALLOWED_SOLUTION_TYPES, true);
    }

    public function isPublisher(int $customerId): bool
    {
        $solutionType = $this->getSolutionType($customerId);

        return null === $solutionType
            || in_array($solutionType, self::PUBLISHER_ALLOWED_SOLUTION_TYPES, true);
    }

    private function getSolutionType(int $customerId): ?string
    {
        return $this->customerInformationService
            ->getBaseInformation($customerId)
            ->getSolutionType();
    }
}

class_alias(CustomerTypeChecker::class, 'Ibexa\Platform\Personalization\Permission\CustomerTypeChecker');
