<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\Generator\Mode;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStep;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStep;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeMigrationGenerator;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStep;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;
use Traversable;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeMigrationGenerator
 */
final class AttributeMigrationGeneratorTest extends IbexaKernelTestCase
{
    private AttributeMigrationGenerator $attributeMigrationGenerator;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();

        $this->attributeDefinitionService = $this->getAttributeDefinitionService();
        $this->attributeMigrationGenerator = self::getServiceByClassName(AttributeMigrationGenerator::class);
    }

    /**
     * @dataProvider providerForGenerate
     *
     * @param class-string<\Ibexa\Migration\ValueObject\Step\StepInterface> $attributeStepClassName
     * @param array<mixed> $value
     */
    public function testGenerate(
        string $mode,
        string $attributeStepClassName,
        string $matchProperty = null,
        array $value = ['foo']
    ): void {
        $query = null;
        if ($matchProperty !== null) {
            $query = new AttributeDefinitionQuery(
                new FieldValueCriterion($matchProperty, $value),
                [],
                null,
            );
        }

        $attributeCount = $this->attributeDefinitionService->findAttributesDefinitions($query)->getTotalCount();

        $iterator = $this->attributeMigrationGenerator->generate(new Mode($mode), [
            'value' => $value,
            'match-property' => $matchProperty,
        ]);

        self::assertInstanceOf(Traversable::class, $iterator);
        $results = iterator_to_array($iterator);
        self::assertCount($attributeCount, $results);
        self::assertContainsOnlyInstancesOf($attributeStepClassName, $results);
    }

    /**
     * @return iterable<string, array{
     *     string,
     *     class-string<\Ibexa\Migration\ValueObject\Step\StepInterface>,
     *     2?: string,
     *     3?: array<mixed>,
     * }>
     */
    public function providerForGenerate(): iterable
    {
        yield 'create (no criteria)' => [
            'create',
            AttributeCreateStep::class,
        ];

        yield 'update (no criteria)' => [
            'update',
            AttributeUpdateStep::class,
        ];

        yield 'delete (no criteria)' => [
            'delete',
            AttributeDeleteStep::class,
        ];

        yield 'create (identifier: foo)' => [
            'create',
            AttributeCreateStep::class,
            'identifier',
            ['foo'],
        ];

        yield 'update (identifier: foo)' => [
            'update',
            AttributeUpdateStep::class,
            'identifier',
            ['foo'],
        ];

        yield 'delete (identifier: foo)' => [
            'delete',
            AttributeDeleteStep::class,
            'identifier',
            ['foo'],
        ];

        yield 'create (attribute_group.identifier: foo)' => [
            'create',
            AttributeCreateStep::class,
            'attribute_group.identifier',
            ['foo'],
        ];

        yield 'update (attribute_group.identifier: foo)' => [
            'update',
            AttributeUpdateStep::class,
            'attribute_group.identifier',
            ['foo'],
        ];

        yield 'delete (attribute_group.identifier: foo)' => [
            'delete',
            AttributeDeleteStep::class,
            'attribute_group.identifier',
            ['foo'],
        ];
    }

    public function testGetSupportedType(): void
    {
        self::assertSame('attribute', $this->attributeMigrationGenerator->getSupportedType());
    }

    public function testGetSupportedModes(): void
    {
        self::assertEqualsCanonicalizing([
            'create',
            'delete',
            'update',
        ], $this->attributeMigrationGenerator->getSupportedModes());
    }

    /**
     * @dataProvider dataProviderForSupports
     */
    public function testSupports(string $type, string $mode, bool $supports): void
    {
        self::assertSame(
            $supports,
            $this->attributeMigrationGenerator->supports($type, new Mode($mode)),
        );
    }

    /**
     * @return iterable<string, array{string, string, boolean}>
     */
    public function dataProviderForSupports(): iterable
    {
        yield 'content create (false)' => [
            'content',
            'create',
            false,
        ];

        yield 'attribute create (true)' => [
            'attribute',
            'create',
            true,
        ];

        yield 'attribute update (true)' => [
            'attribute',
            'update',
            true,
        ];

        yield 'attribute delete (true)' => [
            'attribute',
            'delete',
            true,
        ];
    }
}
