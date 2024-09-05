<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Gateway;

/**
 * Gateway containing queries needed by
 * {@see \Ibexa\Bundle\Installer\Command\IbexaUpgradeCommand}.
 *
 * @internal
 */
interface SiteDataGateway
{
    public function getLegacySchemaVersion(): ?string;

    public function getDXPSchemaVersion(): ?string;

    public function getSiteDataValue(string $key): ?string;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function setDXPSchemaVersion(string $newVersion): void;
}

class_alias(SiteDataGateway::class, 'Ibexa\Platform\Installer\Gateway\SiteDataGateway');
