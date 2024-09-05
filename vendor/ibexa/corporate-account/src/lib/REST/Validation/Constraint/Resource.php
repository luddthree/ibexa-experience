<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Constraint;

use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Constraint;
use Throwable;

/**
 * @internal
 *
 * REST resource URI Constraint
 */
final class Resource extends Constraint
{
    public const IS_MALFORMED_RESOURCE_ERROR = '2a18ae10-0c7b-4f17-9204-a752ae1eada3';

    public string $message = 'This value should match REST route.';

    public ?Throwable $parserException = null;

    public RequestParser $requestParser;

    public function __construct(
        RequestParser $requestParser,
        $options = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->requestParser = $requestParser;
    }

    public function validatedBy(): string
    {
        return ResourceValidator::class;
    }
}
