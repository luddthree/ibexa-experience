<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\UserGroupAggregationKeyMapper;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class UserGroupAggregationKeyMapperTest extends TestCase
{
    private const EXAMPLE_USER_GROUP_IDS = ['1', '2', '3'];

    /** @var \Ibexa\Contracts\Core\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private $userService;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
    }

    public function testMap(): void
    {
        $mapper = new UserGroupAggregationKeyMapper($this->userService);

        $expectedResult = $this->configureUserServiceMock(
            self::EXAMPLE_USER_GROUP_IDS
        );

        $this->assertEquals(
            $expectedResult,
            $mapper->map(
                $this->createMock(Aggregation::class),
                MockUtils::createEmptyLanguageFilter(),
                self::EXAMPLE_USER_GROUP_IDS
            )
        );
    }

    private function configureUserServiceMock(iterable $ids): array
    {
        $users = [];
        foreach ($ids as $i => $id) {
            $user = $this->createMock(UserGroup::class);

            $this->userService
                ->expects($this->at($i))
                ->method('loadUserGroup')
                ->with((int)$id, [])
                ->willReturn($user);

            $users[$id] = $user;
        }

        return $users;
    }
}

class_alias(UserGroupAggregationKeyMapperTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\UserGroupAggregationKeyMapperTest');
