<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\Subtask;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\TranslationSubtask;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Tests\Integration\ProductCatalog\Completeness\Task\BaseTaskTest;

final class TranslationSubtaskTest extends BaseTaskTest
{
    /**
     * @dataProvider provideForTestGetEntry
     */
    public function testGetEntry(
        string $productCode,
        float $completenessPercentage,
        bool $isComplete,
        string $languageCode,
        string $languageName
    ): void {
        $product = self::getProductService()->getProduct($productCode);
        $translationSubtask = $this->getTranslationSubtask($product, $languageCode, $languageName);

        if ($translationSubtask === null) {
            return;
        }

        $entry = $translationSubtask->getEntry($product);
        $this->assertCompleteness($entry, $isComplete, $completenessPercentage);
    }

    /**
     * @phpstan-return iterable<array{string, float, boolean, string, string}>
     */
    public function provideForTestGetEntry(): iterable
    {
        yield [
            'JEANS_1',
            100,
            true,
            'eng-GB',
            'English (United Kingdom)',
        ];

        yield [
            'JEANS_1',
            0,
            false,
            'foo-FOO',
            'foo',
        ];

        yield [
            'BLOUSE_1',
            100,
            true,
            'eng-GB',
            'English (United Kingdom)',
        ];

        yield [
            'BLOUSE_1',
            0,
            false,
            'ger-DE',
            'German',
        ];
    }

    /**
     * @dataProvider provideForTestGetSubtaskGroups
     *
     * @phpstan-param mixed $expectedTasks
     */
    public function testGetSubtaskGroups(string $productCode, $expectedTasks): void
    {
        $product = self::getProductService()->getProduct($productCode);
        $translationSubtask = $this->getTranslationSubtask(
            $product,
            'eng-GB',
            'English (United Kingdom)'
        );

        if ($translationSubtask === null) {
            return;
        }

        self::assertEquals(
            $expectedTasks,
            $translationSubtask->getSubtaskGroups($product)
        );
    }

    /**
     * @phpstan-return iterable<array{string,null}>
     */
    public function provideForTestGetSubtaskGroups(): iterable
    {
        yield ['JEANS_1', null];
    }

    private function getTranslationSubtask(
        ProductInterface $product,
        string $languageCode,
        string $languageName
    ): ?TaskInterface {
        if (!$product instanceof ContentAwareProductInterface) {
            return null;
        }

        $language = new Language([
            'languageCode' => $languageCode,
            'name' => $languageName,
        ]);

        $translations = (array)self::getLanguageService()->loadLanguageListByCode(
            $product->getContent()->versionInfo->languageCodes
        );

        return new TranslationSubtask($language, $translations);
    }
}
