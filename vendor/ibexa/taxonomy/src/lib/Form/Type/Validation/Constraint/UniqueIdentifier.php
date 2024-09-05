<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Validation\Constraint;

use Ibexa\Taxonomy\Form\Type\Validation\UniqueIdentifierValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @internal
 *
 * @Annotation
 *
 * @Target({"PROPERTY", "ANNOTATION"})
 */
final class UniqueIdentifier extends Constraint
{
    public const MESSAGE = 'Taxonomy Entry with identifier "{{ identifier }}" already exists in "{{ taxonomy }}" '
    . 'taxonomy tree. Please use unique identifier.';

    public const NOT_UNIQUE_ERROR = 'c1051bb5-d103-4f74-8988-acbcafc7fdc3';

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public string $message = self::MESSAGE;

    public ?string $taxonomy = null;

    /**
     * @param array<string, mixed>|null $options
     */
    public function __construct(
        array $options = null,
        string $message = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return UniqueIdentifierValidator::class;
    }

    public function getRequiredOptions(): array
    {
        return ['taxonomy'];
    }
}
