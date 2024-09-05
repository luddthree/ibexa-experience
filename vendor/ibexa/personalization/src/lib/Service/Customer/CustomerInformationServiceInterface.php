<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Customer;

use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Customer\BaseInformation;
use Ibexa\Personalization\Value\Customer\Features;

interface CustomerInformationServiceInterface
{
    public function getBaseInformation(int $customerId): BaseInformation;

    public function getItemTypes(int $customerId): ItemTypeList;

    public function getFeatures(int $customerId): Features;
}

class_alias(CustomerInformationServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Customer\CustomerInformationServiceInterface');
