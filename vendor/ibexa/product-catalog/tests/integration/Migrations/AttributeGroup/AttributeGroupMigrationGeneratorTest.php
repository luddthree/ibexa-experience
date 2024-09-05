<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStep;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStep;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupMigrationGenerator;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStep;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;
use Traversable;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupMigrationGenerator
 */
final class AttributeGroupMigrationGeneratorTest extends IbexaKernelTestCase
{
    private AttributeGroupMigrationGenerator $attributeGroupMigrationGenerator;

    private AttributeGroupServiceInterface $attributeGroupService;

    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();

        $this->attributeGroupService = self::getAttributeGroupService();
        $this->attributeGroupMigrationGenerator = self::getServiceByClassName(AttributeGroupMigrationGenerator::class);
    }

    /**
     * @dataProvider providerForGenerate
     *
     * @param class-string<\Ibexa\Migration\ValueObject\Step\StepInterface> $attributeGroupStepClassName
     * @param array<mixed> $value
     */
    public function testGenerate(
        string $mode,
        string $attributeGroupStepClassName,
        string $matchProperty = null,
        array $value = ['foo']
    ): void {
        if ($matchProperty === null) {
            $attributeGroupCount = $this->attributeGroupService->findAttributeGroups()->getTotalCount();
        } else {
            $attributeGroupCount = 1;
        }

        $iterator = $this->attributeGroupMigrationGenerator->generate(new Mode($mode), [
            'value' => $value,
            'match-property' => $matchProperty,
        ]);

        self::assertInstanceOf(Traversable::class, $iterator);
        $results = iterator_to_array($iterator);
        self::assertCount($attributeGroupCount, $results);
        self::assertContainsOnlyInstancesOf($attributeGroupStepClassName, $results);
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
            AttributeGroupCreateStep::class,
        ];

        yield 'update (no criteria)' => [
            'update',
            AttributeGroupUpdateStep::class,
        ];

        yield 'delete (no criteria)' => [
            'delete',
            AttributeGroupDeleteStep::class,
        ];

        yield 'create (identifier: dimensions)' => [
            'create',
            AttributeGroupCreateStep::class,
            'identifier',
            ['dimensions'],
        ];

        yield 'update (identifier: dimensions)' => [
            'update',
            AttributeGroupUpdateStep::class,
            'identifier',
            ['dimensions'],
        ];

        yield 'delete (identifier: dimensions)' => [
            'delete',
            AttributeGroupDeleteStep::class,
            'identifier',
            ['dimensions'],
        ];
    }

    /**
     * @dataProvider providerForNonIdentifierMatchingIsNotAllowed
     *
     * @param class-string<\Ibexa\Migration\ValueObject\Step\StepInterface> $attributeGroupStepClassName
     * @param array<mixed> $value
     */
    public function testNonIdentifierMatchingIsNotAllowed(
        string $mode,
        string $attributeGroupStepClassName,
        string $matchProperty = null,
        array $value = ['foo']
    ): void {
        $iterator = $this->attributeGroupMigrationGenerator->generate(new Mode($mode), [
            'value' => $value,
            'match-property' => $matchProperty,
        ]);

        self::assertInstanceOf(Traversable::class, $iterator);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Unable to match by "name" property. Only matching by "identifier" identifier is supported.');
        iterator_to_array($iterator);
    }

    /**
     * @return iterable<string, array{
     *     string,
     *     class-string<\Ibexa\Migration\ValueObject\Step\StepInterface>,
     *     string,
     *     array<mixed>,
     * }>
     */
    public function providerForNonIdentifierMatchingIsNotAllowed(): iterable
    {
        yield 'create (name: foo)' => [
            'create',
            AttributeGroupCreateStep::class,
            'name',
            ['foo'],
        ];

        yield 'update (name: foo)' => [
            'update',
            AttributeGroupUpdateStep::class,
            'name',
            ['foo'],
        ];

        yield 'delete (name: foo)' => [
            'delete',
            AttributeGroupDeleteStep::class,
            'name',
            ['foo'],
        ];
    }

    public function testGetSupportedType(): void
    {
        self::assertSame('attribute_group', $this->attributeGroupMigrationGenerator->getSupportedType());
    }

    public function testGetSupportedModes(): void
    {
        self::assertEqualsCanonicalizing([
            'create',
            'delete',
            'update',
        ], $this->attributeGroupMigrationGenerator->getSupportedModes());
    }

    /**
     * @dataProvider dataProviderForSupports
     */
    public function testSupports(string $type, string $mode, bool $supports): void
    {
        self::assertSame(
            $supports,
            $this->attributeGroupMigrationGenerator->supports($type, new Mode($mode)),
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

        yield 'attribute_group create (true)' => [
            'attribute_group',
            'create',
            true,
        ];

        yield 'attribute_group update (true)' => [
            'attribute_group',
            'update',
            true,
        ];

        yield 'attribute_group delete (true)' => [
            'attribute_group',
            'delete',
            true,
        ];
    }
}
