<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverter;

use Ibexa\Contracts\Core\Limitation\Type;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitation;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;

interface LimitationConverterInterface
{
    public function convertToMigration(APILimitation $limitation): Limitation;

    public function convertToApi(Type $type, Limitation $limitation): APILimitation;

    public function supportsConversionToMigration(APILimitation $limitation): bool;

    public function supportsConversionToApi(Type $type, Limitation $limitation): bool;
}

class_alias(LimitationConverterInterface::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\LimitationConverter\LimitationConverterInterface');
