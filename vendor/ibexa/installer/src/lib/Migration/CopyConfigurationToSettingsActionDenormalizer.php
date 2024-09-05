<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Migration;

use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Webmozart\Assert\Assert;

final class CopyConfigurationToSettingsActionDenormalizer extends AbstractActionDenormalizer
{
    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === CopyConfigurationToSettingsAction::ACTION_NAME;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): CopyConfigurationToSettingsAction
    {
        Assert::stringNotEmpty($data['value'] ?? null);

        return new CopyConfigurationToSettingsAction($data['value']);
    }
}

class_alias(CopyConfigurationToSettingsActionDenormalizer::class, 'Ibexa\Platform\Installer\Migration\CopyConfigurationToSettingsActionDenormalizer');
