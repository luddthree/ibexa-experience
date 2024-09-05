<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exporter;

use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Value\Export\Parameters;

/**
 * @internal
 */
interface ExporterInterface
{
    public function export(Parameters $parameters): void;

    public function getPackageList(Parameters $parameters): PackageList;

    public function hasExportItems(Parameters $parameters): bool;
}

class_alias(ExporterInterface::class, 'EzSystems\EzRecommendationClient\Exporter\ExporterInterface');
