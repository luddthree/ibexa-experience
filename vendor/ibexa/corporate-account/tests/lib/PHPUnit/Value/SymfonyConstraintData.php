<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\PHPUnit\Value;

/**
 * @internal
 *
 * DTO for custom PHPUnit Constraint
 * {@see \Ibexa\Tests\CorporateAccount\PHPUnit\Constraint\ViolationListMatchesExpectedViolations}.
 */
final class SymfonyConstraintData
{
    public string $message;

    public string $propertyPath;

    /** @var array<string, string> */
    public array $parameters;

    /**
     * @param array<string, string> $parameters
     */
    public function __construct(string $message, string $propertyPath, array $parameters)
    {
        $this->message = $message;
        $this->propertyPath = $propertyPath;
        $this->parameters = $parameters;
    }
}
