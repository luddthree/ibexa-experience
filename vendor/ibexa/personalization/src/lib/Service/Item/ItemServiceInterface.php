<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Item;

use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Value\Authentication\Parameters as AuthenticationParameters;
use Ibexa\Personalization\Value\Export\Parameters as ExportParameters;

/**
 * @internal
 */
interface ItemServiceInterface
{
    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function export(ExportParameters $parameters, PackageList $packageList): void;

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function update(AuthenticationParameters $parameters, PackageList $packageList): void;

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    public function delete(AuthenticationParameters $parameters, PackageList $packageList): void;
}
