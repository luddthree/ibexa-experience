<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Ibexa\Personalization\Value\Model\ModelList;
use Symfony\Component\Validator\Constraint;

final class SupportedModelDataType extends Constraint
{
    public string $message = 'Data type {{ data_type }} is not supported for model {{ model }}';

    public ?string $referenceCode;

    public ModelList $modelList;
}
