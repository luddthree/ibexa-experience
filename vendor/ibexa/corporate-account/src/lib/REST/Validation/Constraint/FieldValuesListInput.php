<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Constraint;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Symfony\Component\Validator\Constraint;

/**
 * @internal
 */
final class FieldValuesListInput extends Constraint
{
    private ContentType $contentType;

    public function __construct(ContentType $contentType, $options = null, array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);
        $this->contentType = $contentType;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function validatedBy(): string
    {
        return FieldValuesListInputValidator::class;
    }
}
