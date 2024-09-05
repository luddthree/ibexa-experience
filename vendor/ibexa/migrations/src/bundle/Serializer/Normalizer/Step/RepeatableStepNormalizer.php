<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Normalizer\Step;

use Generator;
use Ibexa\Bundle\Migration\Serializer\Normalizer\ExpressionNormalizer;
use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\RepeatableStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Webmozart\Assert\Assert;

/**
 * @extends \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer<
 *     \Ibexa\Migration\ValueObject\Step\RepeatableStep
 * >
 */
final class RepeatableStepNormalizer extends AbstractStepNormalizer
{
    public function getType(): string
    {
        return 'repeatable';
    }

    public function getMode(): string
    {
        return 'create';
    }

    public function getHandledClassType(): string
    {
        return RepeatableStep::class;
    }

    protected function normalizeStep(StepInterface $object, string $format = null, array $context = []): array
    {
        throw new NotNormalizableValueException(sprintf(
            '%s is not normalizable. Migration of type "%s" and mode "%s" has to be created manually.',
            RepeatableStep::class,
            $this->getType(),
            $this->getMode(),
        ));
    }

    protected function denormalizeStep($data, string $type, string $format, array $context = []): StepInterface
    {
        Assert::keyExists($data, 'iterations');
        $iterations = $data['iterations'];
        Assert::positiveInteger($iterations, '"iterations" has to be an integer');

        $iterationCounterStartingValue = $data['iteration_counter_starting_value'] ?? 0;
        Assert::integer($iterationCounterStartingValue, '"iteration_counter_starting_value" has to be an integer');

        Assert::keyExists($data, 'steps');
        Assert::isArray($data['steps']);

        $context[ExpressionNormalizer::PERFORM_REPLACE_KEY] = true;

        $iterationCounterName = $data['iteration_counter_name'] ?? 'i';
        Assert::stringNotEmpty($iterationCounterName, '"iteration_counter_name" has to be a string');

        $steps = $this->createSteps(
            $iterationCounterStartingValue,
            $iterations,
            $context,
            $iterationCounterName,
            $data['steps'],
            $format
        );

        return new RepeatableStep($steps);
    }

    /**
     * @param array<string, mixed> $context
     * @param array<array<mixed>> $template
     *
     * @return \Generator<\Ibexa\Migration\ValueObject\Step\StepInterface>
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function createSteps(
        int $iterationCounterStartingValue,
        int $iterations,
        array $context,
        string $iterationCounterName,
        array $template,
        string $format
    ): Generator {
        for ($i = $iterationCounterStartingValue; $i < $iterations + $iterationCounterStartingValue; ++$i) {
            $templateContext = array_merge($context[ExpressionNormalizer::TEMPLATE_CONTEXT_KEY] ?? [], [
                $iterationCounterName => $i,
            ]);

            $internalContext = array_merge($context, [
                ExpressionNormalizer::TEMPLATE_CONTEXT_KEY => $templateContext,
            ]);

            yield from $this->denormalizer->denormalize(
                $template,
                StepInterface::class . '[]',
                $format,
                $internalContext,
            );
        }
    }
}
