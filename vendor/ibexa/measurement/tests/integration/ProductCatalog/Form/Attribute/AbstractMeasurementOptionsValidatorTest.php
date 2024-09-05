<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\AbstractMeasurementOptionsValidator;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;

/**
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementOptionsValidator
 *
 * @phpstan-type TOptions array<string, scalar|array<scalar|null>|null>
 * @phpstan-type TError array{target: string, message: string}
 */
abstract class AbstractMeasurementOptionsValidatorTest extends IbexaKernelTestCase
{
    protected const EXISTING_TYPE = 'length';

    protected const EXISTING_UNIT = 'meter';

    abstract protected function getValidator(): AbstractMeasurementOptionsValidator;

    /**
     * @phpstan-param TOptions $options
     *
     * @dataProvider provideForValidOptions
     */
    public function testValidateForValidOptions(array $options): void
    {
        $errors = $this->getValidator()->validateOptions(new AttributeDefinitionOptions($options));
        self::assertEmpty($errors, sprintf(
            "Expected valid options. Found %d errors:\n%s",
            count($errors),
            implode("\n", array_map([self::class, 'stringifyError'], $errors)),
        ));
    }

    /**
     * @phpstan-param TOptions $options
     * @phpstan-param array<TError> $expectedErrors
     *
     * @dataProvider provideForInvalidOptions
     */
    public function testValidateForInvalidOptions(array $options, array $expectedErrors): void
    {
        $errors = $this->getValidator()->validateOptions(new AttributeDefinitionOptions($options));

        self::assertFalse(count($errors) === 0, sprintf(
            "No errors found. Expected %d errors. Errors expected:\n%s",
            count($expectedErrors),
            implode("\n", array_map(
                static fn (array $expectedError): string => ' * ' . $expectedError['target'] . ': ' . $expectedError['message'],
                $expectedErrors,
            ))
        ));
        foreach ($expectedErrors as $expectedError) {
            self::assertErrorExists($errors, $expectedError['target'], $expectedError['message']);
        }
        self::assertCount(
            count($expectedErrors),
            $errors,
            sprintf(
                "Too many errors found. Expected %d errors.\nFound errors:\n%s\nExpected errors:\n%s",
                count($expectedErrors),
                implode("\n", array_map([self::class, 'stringifyError'], $errors)),
                implode("\n", array_map(
                    static fn (array $expectedError): string => ' * ' . $expectedError['target'] . ': ' . $expectedError['message'],
                    $expectedErrors
                )),
            ),
        );
    }

    /**
     * @return iterable<
     *     string,
     *     array{
     *         TOptions,
     *     }
     * >
     */
    abstract public function provideForValidOptions(): iterable;

    /**
     * @return iterable<
     *     string,
     *     array{
     *         TOptions,
     *         array<TError>,
     *     },
     * >
     */
    abstract public function provideForInvalidOptions(): iterable;

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError> $errors
     */
    final protected static function assertErrorExists(array $errors, string $target, string $message = null): void
    {
        $typeErrors = self::findErrorsForTarget($errors, $target);

        self::assertNotEmpty($typeErrors, sprintf(
            "Error for target \"%s\" not found. \nErrors found: \n%s",
            $target,
            implode("\n", array_map([self::class, 'stringifyError'], $errors)),
        ));

        if ($message !== null) {
            $found = null;
            foreach ($typeErrors as $error) {
                if ($error->getMessage() === $message) {
                    $found = $error;
                    break;
                }
            }

            self::assertNotNull($found, sprintf(
                "Error for target \"%s\" with message \"%s\" not found.\nErrors found:\n%s",
                $target,
                $message,
                implode("\n", array_map([self::class, 'stringifyError'], $errors)),
            ));
        }
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError> $errors
     *
     * @return array<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError>
     */
    final protected static function findErrorsForTarget(array $errors, string $target): array
    {
        return array_values(
            array_filter(
                $errors,
                static fn (OptionsValidatorError $error) => $error->getTarget() === $target,
            ),
        );
    }

    final protected static function stringifyError(OptionsValidatorError $error): string
    {
        return sprintf(' * %s: %s', $error->getTarget(), $error->getMessage());
    }
}
