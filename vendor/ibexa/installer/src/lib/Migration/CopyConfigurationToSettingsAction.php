<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Migration;

use Ibexa\Migration\ValueObject\Step\Action;

final class CopyConfigurationToSettingsAction implements Action
{
    public const ACTION_NAME = 'copy_configuration_to_settings';

    /** @var string */
    private $fieldDefIdentifier;

    public function __construct(string $fieldDefIdentifier)
    {
        $this->fieldDefIdentifier = $fieldDefIdentifier;
    }

    public function getValue()
    {
        return $this->fieldDefIdentifier;
    }

    public function getSupportedType(): string
    {
        return self::ACTION_NAME;
    }
}

class_alias(CopyConfigurationToSettingsAction::class, 'Ibexa\Platform\Installer\Migration\CopyConfigurationToSettingsAction');
