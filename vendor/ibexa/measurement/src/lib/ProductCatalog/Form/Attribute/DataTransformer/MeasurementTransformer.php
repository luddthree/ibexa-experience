<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\DataTransformer;

use Exception;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface;
use Ibexa\Contracts\Measurement\Parser\MeasurementParserInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\Formatter\Strategy\InputFormattingStrategy;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @template-implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Ibexa\Contracts\Measurement\Value\ValueInterface|null,
 *     string|null
 * >
 */
final class MeasurementTransformer implements DataTransformerInterface
{
    private MeasurementParserInterface $measurementParser;

    private MeasurementValueFormatterInterface $measurementFormatter;

    private MeasurementInterface $measurement;

    public function __construct(
        MeasurementParserInterface $measurementParser,
        MeasurementValueFormatterInterface $measurementFormatter,
        MeasurementInterface $measurement
    ) {
        $this->measurementParser = $measurementParser;
        $this->measurementFormatter = $measurementFormatter;
        $this->measurement = $measurement;
    }

    public function transform($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof ValueInterface) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    ValueInterface::class,
                    get_debug_type($value)
                )
            );
        }

        try {
            return $this->measurementFormatter->format($value, InputFormattingStrategy::NAME);
        } catch (InvalidArgumentException $e) {
            throw new TransformationFailedException($e->getMessage(), 0, $e);
        }
    }

    public function reverseTransform($value): ?ValueInterface
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data, expected a string value, received %s.',
                    get_debug_type($value)
                )
            );
        }

        try {
            return $this->measurementParser->parse($this->measurement, $value);
        } catch (Exception $e) {
            throw new TransformationFailedException($e->getMessage(), 0, $e);
        }
    }
}
