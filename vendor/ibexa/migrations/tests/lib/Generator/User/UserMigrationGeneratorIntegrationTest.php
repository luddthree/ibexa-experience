<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\User;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\User\UserMigrationGenerator;
use Ibexa\Migration\ValueObject\Step\UserCreateStep;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use PHPUnit\Framework\Assert;
use Traversable;

class UserMigrationGeneratorIntegrationTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    public function testGenerateReturnsContentCreateStepsForAllContentObjects(): void
    {
        $generator = self::getServiceByClassName(
            UserMigrationGenerator::class,
            'ibexa.migrations.generator.user'
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
        Assert::assertContainsOnlyInstancesOf(UserCreateStep::class, $results);
    }

    private function getExpectedTotalCount(): int
    {
        $filter = new Filter();
        $filter->withCriterion(new Criterion\ContentTypeIdentifier('user'));

        return self::getContentService()->find($filter)->getTotalCount();
    }
}
