<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImagePicker\Installer;

use Ibexa\Installer\Provisioner\AbstractMigrationProvisioner;

final class ImagePickerProvisioner extends AbstractMigrationProvisioner
{
    /**
     * @return array<string,string>
     */
    protected function getMigrationFiles(): array
    {
        return [
            '2023_12_06_15_00_image_content_type.yaml' => '2023_12_06_15_00_image_content_type.yaml',
        ];
    }

    protected function getMigrationDirectory(): string
    {
        return '@IbexaImagePickerBundle/Resources/migrations';
    }
}
