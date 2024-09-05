<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

/**
 * @covers \Ibexa\Measurement\MeasurementService
 */
final class MeasurementServiceTest extends IbexaKernelTestCase
{
    private MeasurementServiceInterface $measurementService;

    public function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();

        $this->measurementService = self::getServiceByClassName(MeasurementServiceInterface::class);
    }

    /**
     * @dataProvider provideBuiltInTypes
     */
    public function testBuildingBuiltInRangeValue(string $typeName, string $unitIdentifier): void
    {
        $value = $this->measurementService->buildRangeValue(
            $typeName,
            PHP_FLOAT_EPSILON,
            100_000_000_000_000,
            $unitIdentifier
        );

        self::assertEqualsWithDelta(PHP_FLOAT_EPSILON, $value->getMinValue(), PHP_FLOAT_EPSILON);
        self::assertEqualsWithDelta(100_000_000_000_000, $value->getMaxValue(), PHP_FLOAT_EPSILON);

        self::assertSame($unitIdentifier, $value->getUnit()->getIdentifier());
        self::assertSame($typeName, $value->getMeasurement()->getName());
    }

    /**
     * @dataProvider provideBuiltInTypes
     */
    public function testBuildingBuiltInSimpleValue(string $typeName, string $unitIdentifier): void
    {
        $value = $this->measurementService->buildSimpleValue(
            $typeName,
            PHP_FLOAT_EPSILON,
            $unitIdentifier
        );

        self::assertEqualsWithDelta(PHP_FLOAT_EPSILON, $value->getValue(), PHP_FLOAT_EPSILON);

        self::assertSame($unitIdentifier, $value->getUnit()->getIdentifier());
        self::assertSame($typeName, $value->getMeasurement()->getName());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function provideBuiltInTypes(): iterable
    {
        $units = Yaml::parseFile(__DIR__ . '/../../src/bundle/Resources/config/builtin_units.yaml');
        Assert::isArray($units);
        Assert::keyExists($units, 'types');
        Assert::notEmpty($units['types']);

        foreach ($units['types'] as $typeName => $unitData) {
            foreach ($unitData as $unitIdentifier => $unitDatum) {
                yield "$typeName - $unitIdentifier" => [
                    $typeName,
                    $unitIdentifier,
                ];
            }
        }
    }

    public function testBuildingIncompatibleSimpleValue(): void
    {
        $typeName = 'length';
        $unitIdentifier = 'acre';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Argument \'$unit\' is invalid: %s for %s does not exist',
            $unitIdentifier,
            $typeName,
        ));

        $this->measurementService->buildSimpleValue(
            $typeName,
            0,
            $unitIdentifier
        );
    }

    public function testBuildingIncompatibleRangeValue(): void
    {
        $typeName = 'length';
        $unitIdentifier = 'acre';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Argument \'$unit\' is invalid: %s for %s does not exist',
            $unitIdentifier,
            $typeName,
        ));

        $this->measurementService->buildRangeValue(
            $typeName,
            PHP_FLOAT_EPSILON,
            100_000_000_000_000,
            $unitIdentifier
        );
    }

    public function testBuildingNotExistingTypeSimpleValue(): void
    {
        $typeName = 'foo';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Argument \'$type\' is invalid: %s does not exist',
            $typeName,
        ));

        $this->measurementService->buildSimpleValue(
            $typeName,
            0,
            ''
        );
    }

    public function testBuildingNotExistingTypeRangeValue(): void
    {
        $typeName = 'foo';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Argument \'$type\' is invalid: %s does not exist',
            $typeName,
        ));

        $this->measurementService->buildRangeValue(
            $typeName,
            PHP_FLOAT_EPSILON,
            100_000_000_000_000,
            ''
        );
    }
}
