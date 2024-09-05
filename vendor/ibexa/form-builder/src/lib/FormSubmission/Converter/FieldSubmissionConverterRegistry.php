<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

class FieldSubmissionConverterRegistry
{
    /** @var \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface[] */
    private $converters;

    /** @var \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface */
    private $fallback;

    /**
     * @param iterable $converters
     * @param \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface $fallback
     */
    public function __construct(
        iterable $converters,
        FieldSubmissionConverterInterface $fallback
    ) {
        /** @var \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface $converter */
        foreach ($converters as $converter) {
            $this->addConverter($converter->getTypeIdentifier(), $converter);
        }

        $this->fallback = $fallback;
    }

    /**
     * @return \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface[]
     */
    public function getConverters(): array
    {
        return $this->converters;
    }

    /**
     * @param \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface[] $converters
     */
    public function setConverters(array $converters): void
    {
        $this->converters = $converters;
    }

    /**
     * @param string $type
     * @param \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface $converter
     */
    public function addConverter(string $type, FieldSubmissionConverterInterface $converter): void
    {
        $this->converters[$type] = $converter;
    }

    /**
     * @param string $type
     *
     * @return \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface
     */
    public function getConverter(string $type): FieldSubmissionConverterInterface
    {
        if ($this->hasConverter($type)) {
            return $this->converters[$type];
        }

        return $this->fallback;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasConverter(string $type): bool
    {
        return isset($this->converters[$type]);
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     *
     * @return \Ibexa\FormBuilder\FormSubmission\Converter\FieldSubmissionConverterInterface
     */
    public function getConverterForField(Field $field): FieldSubmissionConverterInterface
    {
        return $this->getConverter($field->getIdentifier());
    }
}

class_alias(FieldSubmissionConverterRegistry::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\FieldSubmissionConverterRegistry');
