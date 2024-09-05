<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator;

use function array_filter;
use function array_reduce;
use function count;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Migration\Generator\ContentTypeMigrationGenerator;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use function in_array;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Traversable;

final class ContentTypeMigrationGeneratorIntegrationTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testGenerateReturnsContentTypeCreateStepsForAllContentTypeObjects(): void
    {
        $generator = self::getServiceByClassName(ContentTypeMigrationGenerator::class);

        $results = $generator->generate(
            new Mode('create'),
            [
                'value' => ['*'],
                'match-property' => null,
            ]
        );

        if ($results instanceof Traversable) {
            $results = iterator_to_array($results);
        }

        Assert::assertCount($this->getExpectedContentTypeTotalCount(), $results);
        Assert::assertContainsOnlyInstancesOf(ContentTypeCreateStep::class, $results);
    }

    public function testGenerateReturnsContentTypeCreateStepsForGivenContentTypeObjects(): void
    {
        $generator = self::getServiceByClassName(ContentTypeMigrationGenerator::class);
        $contentTypes = [
            'article',
            'landing_page',
            'blog_post',
        ];

        $results = $generator->generate(
            new Mode('create'),
            [
                'value' => $contentTypes,
                'match-property' => 'content_type_identifier',
            ]
        );

        if ($results instanceof Traversable) {
            $results = iterator_to_array($results);
        }

        $expectedTotalCount = $this->getExpectedContentTypeTotalCount($contentTypes);
        Assert::assertCount($expectedTotalCount, $results);
        Assert::assertContainsOnlyInstancesOf(ContentTypeCreateStep::class, $results);
    }

    /**
     * @param string[] $typesToFilter
     */
    private function getExpectedContentTypeTotalCount(array $typesToFilter = []): int
    {
        $contentTypeService = self::getServiceByClassName(ContentTypeService::class);
        $contentTypeGroups = $contentTypeService->loadContentTypeGroups();
        if ($contentTypeGroups instanceof Traversable) {
            $contentTypeGroups = iterator_to_array($contentTypeGroups);
        }

        return array_reduce(
            $contentTypeGroups,
            static function (int $groupTotalCount, ContentTypeGroup $contentGroup) use ($contentTypeService, $typesToFilter): int {
                $contentTypes = $contentTypeService->loadContentTypes($contentGroup);
                if ($contentTypes instanceof Traversable) {
                    $contentTypes = iterator_to_array($contentTypes);
                }

                if (!empty($typesToFilter)) {
                    $contentTypes = array_filter($contentTypes, static function (ContentType $contentType) use ($typesToFilter) {
                        return in_array($contentType->identifier, $typesToFilter);
                    });
                }

                $groupTotalCount += count($contentTypes);

                return $groupTotalCount;
            },
            $groupTotalCount = 0
        );
    }

    public function testGenerateThrowsExceptionWhenMatchPropertyIsUnknown(): void
    {
        $generator = self::getServiceByClassName(ContentTypeMigrationGenerator::class);
        $contentTypes = [
            'article',
            'landing_page',
            'blog_post',
        ];

        $this->expectException(InvalidArgumentException::class);
        $generator->generate(
            new Mode('create'),
            [
                'value' => $contentTypes,
                'match-property' => '__UNKNOWN_PROPERTY__',
            ]
        );
    }
}

class_alias(ContentTypeMigrationGeneratorIntegrationTest::class, 'Ibexa\Platform\Tests\Migration\Generator\ContentTypeMigrationGeneratorIntegrationTest');
