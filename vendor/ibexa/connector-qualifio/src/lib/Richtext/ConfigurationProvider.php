<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Richtext;

use Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface;
use Ibexa\Contracts\FieldTypeRichText\Configuration\ProviderConfiguratorInterface;

final class ConfigurationProvider implements ProviderConfiguratorInterface
{
    private QualifioServiceInterface $qualifioService;

    public function __construct(
        QualifioServiceInterface $qualifioService
    ) {
        $this->qualifioService = $qualifioService;
    }

    public function getConfiguration(array $configuration): array
    {
        if (!isset($configuration['qualifio'])) {
            return $configuration;
        }

        if (!$this->qualifioService->isConfigured()) {
            unset($configuration['qualifio']);
        }

        return $configuration;
    }
}
