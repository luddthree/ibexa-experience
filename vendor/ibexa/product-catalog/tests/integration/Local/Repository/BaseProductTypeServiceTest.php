<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseProductTypeServiceTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    protected static function assertCountProductTypesInDatabaseTable(int $expected): void
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();
        $countExpr = $connection->getDatabasePlatform()->getCountExpression('DISTINCT(ezcontentclass_attribute.id)');
        $result = $qb->select($countExpr)
            ->from('ezcontentclass_attribute')
            ->where(
                $qb->expr()->eq(
                    'data_type_string',
                    $qb->createNamedParameter('ibexa_product_specification')
                )
            )
            ->execute()
            ->fetchOne();

        self::assertNotFalse($result);
        self::assertSame($expected, (int)$result);
    }

    /**
     * @param string[] $exceptedProductTypeIdentifiers
     */
    protected function assertProductTypeListItems(
        array $exceptedProductTypeIdentifiers,
        ProductTypeListInterface $actualProductTypeList
    ): void {
        $actualProductTypeIdentifiers = [];
        foreach ($actualProductTypeList->getProductTypes() as $productType) {
            $actualProductTypeIdentifiers[] = $productType->getIdentifier();
        }

        self::assertEquals($exceptedProductTypeIdentifiers, $actualProductTypeIdentifiers);
    }

    /**
     * @param string[] $expectedIdentifiers
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface[] $assignments
     */
    protected function assertHasAttributeDefinitions(array $expectedIdentifiers, iterable $assignments): void
    {
        $actualIdentifiers = [];
        foreach ($assignments as $assignment) {
            $actualIdentifiers[] = $assignment->getAttributeDefinition()->getIdentifier();
        }

        self::assertEquals($expectedIdentifiers, $actualIdentifiers);
    }

    /**
     * @param string[] $expectedNames
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface[] $assignments
     */
    protected function assertAttributeDefinitionsNames(array $expectedNames, iterable $assignments): void
    {
        $actualNames = [];
        foreach ($assignments as $assignment) {
            $actualNames[] = $assignment->getAttributeDefinition()->getName();
        }

        self::assertEquals($expectedNames, $actualNames);
    }

    /**
     * @param string[] $expectedIdentifiers
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface[] $assignments
     */
    protected function assertHasRequiredAttributeDefinitions(array $expectedIdentifiers, iterable $assignments): void
    {
        $actualIdentifiers = [];
        foreach ($assignments as $assignment) {
            if ($assignment->isRequired()) {
                $actualIdentifiers[] = $assignment->getAttributeDefinition()->getIdentifier();
            }
        }

        self::assertEquals($expectedIdentifiers, $actualIdentifiers);
    }

    /**
     * @param string[] $expectedIdentifiers
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface[] $assignments
     */
    protected function assertHasDiscriminatorAttributeDefinitions(array $expectedIdentifiers, iterable $assignments): void
    {
        $actualIdentifiers = [];
        foreach ($assignments as $assignment) {
            if ($assignment->isDiscriminator()) {
                $actualIdentifiers[] = $assignment->getAttributeDefinition()->getIdentifier();
            }
        }

        self::assertEquals($expectedIdentifiers, $actualIdentifiers);
    }

    /**
     * @param array<int, array<string, bool|string>> $expectedAttributeDefinitions
     */
    protected static function assertAttributeDefinitions(
        array $expectedAttributeDefinitions,
        ProductTypeInterface $productType
    ): void {
        $actualAttributeDefinitions = [];
        foreach ($productType->getAttributesDefinitions() as $attributesDefinition) {
            $actualAttributeDefinitions[] = [
                'attributeDefinition' => $attributesDefinition->getAttributeDefinition()->getIdentifier(),
                'discriminator' => $attributesDefinition->isDiscriminator(),
                'required' => $attributesDefinition->isRequired(),
            ];
        }

        self::assertEquals($expectedAttributeDefinitions, $actualAttributeDefinitions);
    }

    protected function assertIsVirtual(
        bool $expectedIsVirtual,
        ProductTypeInterface $productType
    ): void {
        self::assertSame(
            $expectedIsVirtual,
            $productType->isVirtual()
        );
    }
}
