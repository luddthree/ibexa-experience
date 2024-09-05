<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @template T
 *
 * @template-implements \Symfony\Component\Form\DataTransformerInterface<T[],string>
 */
final class StringToArrayTransformer implements DataTransformerInterface
{
    private const DEFAULT_SEPARATOR = ',';

    /** @var \Symfony\Component\Form\DataTransformerInterface<T,string|int>|null */
    private ?DataTransformerInterface $innerTransformer;

    /** @var non-empty-string */
    private string $separator;

    /**
     * @param \Symfony\Component\Form\DataTransformerInterface<T,string|int>|null $innerTransformer
     * @param non-empty-string $separator
     */
    public function __construct(
        ?DataTransformerInterface $innerTransformer = null,
        string $separator = self::DEFAULT_SEPARATOR
    ) {
        $this->innerTransformer = $innerTransformer;
        $this->separator = $separator;
    }

    /**
     * @param array<T|string>|null $value
     */
    public function transform($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Invalid data, expected a array or null value');
        }

        if ($this->innerTransformer !== null) {
            /** @var callable(string|T): ?string $callable */
            $callable = [$this->innerTransformer, 'transform'];

            $value = array_map($callable, $value);
        }

        return implode($this->separator, $value);
    }

    /**
     * @param string|null $value
     *
     * @return array<string|T|null>|null
     */
    public function reverseTransform($value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, expected a string or null value');
        }

        if (trim($value) === '') {
            return null;
        }

        $value = array_values(array_filter(array_map('trim', explode($this->separator, $value))));
        if ($this->innerTransformer !== null) {
            $value = array_map([$this->innerTransformer, 'reverseTransform'], $value);
        }

        return $value;
    }
}
