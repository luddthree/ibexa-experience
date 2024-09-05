<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Connect;

use Ibexa\FormBuilder\FormSubmission\Converter\GenericFieldSubmissionConverter;

final class TestFieldSubmissionConverter extends GenericFieldSubmissionConverter
{
    /**
     * @throws \JsonException
     */
    public function toDisplayValue($fieldValue): string
    {
        return json_encode($fieldValue, JSON_THROW_ON_ERROR);
    }
}
