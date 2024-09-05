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
final class FieldDefinitionIdentifier extends Constraint
{
    public const IS_INVALID_FIELD_DEFINITION_IDENTIFIER_ERROR = 'c6ee41ea-34d4-4421-b141-04fb9574762d';

    public ContentType $contentType;

    public function __construct(
        ContentType $contentType,
        $options = null,
        array $groups = null,
        $payload = null
    ) {
        $this->contentType = $contentType;

        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return FieldDefinitionIdentifierValidator::class;
    }
}
