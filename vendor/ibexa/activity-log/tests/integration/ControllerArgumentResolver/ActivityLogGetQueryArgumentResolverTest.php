<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\ControllerArgumentResolver;

use Ibexa\Bundle\ActivityLog\ControllerArgumentResolver\ActivityLogGetQueryArgumentResolver;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ActionCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Traversable;

final class ActivityLogGetQueryArgumentResolverTest extends IbexaKernelTestCase
{
    private ArgumentValueResolverInterface $resolver;

    protected function setUp(): void
    {
        $core = $this->getIbexaTestCore();
        $this->resolver = $core->getServiceByClassName(ActivityLogGetQueryArgumentResolver::class);
    }

    /**
     * @dataProvider provideUriForTest
     */
    public function testResolve(string $uri, Query $query): void
    {
        $request = Request::create($uri);
        $argument = new ArgumentMetadata('query', Query::class, false, false, null);

        $arguments = $this->resolver->resolve($request, $argument);
        if ($arguments instanceof Traversable) {
            $arguments = iterator_to_array($arguments);
        }

        self::assertCount(1, $arguments);
        [$argument] = $arguments;

        self::assertEquals(
            $query,
            $argument,
        );
    }

    /**
     * @dataProvider provideUriForTest
     */
    public function testSupports(string $uri): void
    {
        $request = Request::create($uri);
        $argument = new ArgumentMetadata('query', Query::class, false, false, null);

        self::assertTrue($this->resolver->supports($request, $argument));
    }

    /**
     * @return iterable<array{string, \Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query}>
     */
    public static function provideUriForTest(): iterable
    {
        yield [
            '?' . http_build_query([
                'limit' => 2,
                'offset' => 1,
                'filter' => [
                    0 => [
                        'type' => 'action',
                        'value' => 'create',
                    ],
                    1 => [
                        'type' => 'object_class',
                        'class' => 'Ibexa\Contracts\Core\Repository\Values\Content\Content',
                    ],
                ],
                'sort' => [
                    0 => [
                        'type' => 'logged_at',
                        'direction' => 'DESC',
                    ],
                ],
            ]),
            new Query(
                [
                    new ActionCriterion(['create']),
                    new ObjectCriterion('Ibexa\Contracts\Core\Repository\Values\Content\Content'),
                ],
                [
                    new LoggedAtSortClause('DESC'),
                ],
                1,
                2,
            ),
        ];

        yield [
            '?limit=2&offset=1'
            . '&filter%5B0%5D%5Btype%5D=action&filter%5B0%5D%5Bvalue%5D=create'
            . '&sort%5B0%5D%5Btype%5D=logged_at&sort%5B0%5D%5Bdirection%5D=DESC'
            . '&filter%5B1%5D%5Btype%5D=object_class&filter%5B1%5D%5Bclass%5D=Ibexa%5CContracts%5CCore%5CRepository%5CValues%5CContent%5CContent',
            new Query(
                [
                    new ActionCriterion(['create']),
                    new ObjectCriterion('Ibexa\Contracts\Core\Repository\Values\Content\Content'),
                ],
                [
                    new LoggedAtSortClause('DESC'),
                ],
                1,
                2,
            ),
        ];

        yield [
            '',
            new Query([], [], 0, 25),
        ];

        yield [
            '?limit=2&offset=1',
            new Query([], [], 1, 2),
        ];

        yield [
            '?offset=1',
            new Query([], [], 1, 25),
        ];
    }
}
