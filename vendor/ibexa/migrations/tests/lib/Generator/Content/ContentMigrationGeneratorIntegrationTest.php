<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Repository\Values\User\UserReference;
use Ibexa\Migration\Generator\Content\ContentMigrationGenerator;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use PHPUnit\Framework\Assert;
use Traversable;

class ContentMigrationGeneratorIntegrationTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(14));
    }

    public function testGenerateReturnsContentCreateStepsForAllContentObjects(): void
    {
        $generator = self::getServiceByClassName(
            ContentMigrationGenerator::class,
            'ibexa.migrations.generator.content'
        );

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

        $totalCount = $this->getExpectedTotalCount();
        Assert::assertNotNull($totalCount);
        Assert::assertCount($totalCount, $results);
        Assert::assertContainsOnlyInstancesOf(ContentCreateStep::class, $results);
    }

    private function getExpectedTotalCount(): ?int
    {
        $query = new Query();
        $query->limit = 100;
        $query->query = new Criterion\LogicalAnd(
            [
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            ]
        );

        return self::getSearchService()->findContent($query)->totalCount;
    }
}

class_alias(ContentMigrationGeneratorIntegrationTest::class, 'Ibexa\Platform\Tests\Migration\Generator\Content\ContentMigrationGeneratorIntegrationTest');
