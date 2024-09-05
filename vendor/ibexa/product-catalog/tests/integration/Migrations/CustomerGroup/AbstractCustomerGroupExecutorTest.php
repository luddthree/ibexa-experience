<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

abstract class AbstractCustomerGroupExecutorTest extends AbstractStepExecutorTest
{
    public static function assertCustomerGroupsMatchTestState(
        CustomerGroupServiceInterface $customerGroupService
    ): void {
        $testLanguages = [
            'eng-GB',
            'eng-US',
            'ger-DE',
        ];

        $customerGroup = $customerGroupService->getCustomerGroupByIdentifier('FOO');
        self::assertNotNull($customerGroup);
        self::assertFooCustomerGroupShape(
            $customerGroup,
            'FOO US name',
            'FOO US description',
            $testLanguages,
        );

        $customerGroup = $customerGroupService->getCustomerGroupByIdentifier('FOO', ['eng-US']);
        self::assertNotNull($customerGroup);
        self::assertFooCustomerGroupShape(
            $customerGroup,
            'FOO US name',
            'FOO US description',
            $testLanguages,
        );

        $customerGroup = $customerGroupService->getCustomerGroupByIdentifier('FOO', ['eng-GB']);
        self::assertNotNull($customerGroup);
        self::assertFooCustomerGroupShape(
            $customerGroup,
            'FOO GB name',
            'FOO GB description',
            $testLanguages,
        );

        $customerGroup = $customerGroupService->getCustomerGroupByIdentifier('FOO', ['ger-DE']);
        self::assertNotNull($customerGroup);
        self::assertFooCustomerGroupShape(
            $customerGroup,
            'FOO DE name',
            '',
            $testLanguages,
        );
    }

    /**
     * @param string[] $languages
     */
    private static function assertFooCustomerGroupShape(
        CustomerGroupInterface $customerGroup,
        string $name,
        string $description,
        array $languages
    ): void {
        self::assertSame('FOO', $customerGroup->getIdentifier());
        self::assertSame($name, $customerGroup->getName());
        self::assertSame($description, $customerGroup->getDescription());

        self::assertInstanceOf(TranslatableInterface::class, $customerGroup);
        self::assertEqualsCanonicalizing($languages, $customerGroup->getLanguages());
    }
}
