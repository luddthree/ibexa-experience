<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Provisioner;

final class CommonProvisioner extends AbstractMigrationProvisioner
{
    protected function getMigrationFiles(): array
    {
        return [
            '2023_12_07_20_23_editor_content_type.yaml' => '2023_12_07_20_23_editor_content_type.yaml',
            '2024_01_09_22_23_editor_permissions.yaml' => '2024_01_09_22_23_editor_permissions.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaInstallerBundle/Resources/install/migrations';
    }
}
