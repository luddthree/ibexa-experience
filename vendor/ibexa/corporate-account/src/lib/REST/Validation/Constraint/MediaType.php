<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @internal
 *
 * REST MediaType Constraint
 */
class MediaType extends Constraint
{
    public const IS_MALFORMED_MEDIA_TYPE_ERROR = '4cf3d937-d4eb-4333-a2ef-70b9bd9e2343';

    public string $message = 'This value should be a proper REST Media-Type';

    public string $expectedResourceName;

    public function __construct(
        string $expectedResourceName,
        $options = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->expectedResourceName = $expectedResourceName;
    }
}
