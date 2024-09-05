<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ActivityLog\ControllerArgumentResolver;

use Ibexa\Bundle\ActivityLog\ControllerArgumentResolver\ActivityLogGetQueryArgumentResolver;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ActivityLogGetQueryArgumentResolverTest extends TestCase
{
    private ActivityLogGetQueryArgumentResolver $resolver;

    /** @var \Ibexa\Contracts\Rest\Input\ParsingDispatcher&\PHPUnit\Framework\MockObject\MockObject */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = $this->createMock(ParsingDispatcher::class);
        $this->resolver = new ActivityLogGetQueryArgumentResolver(
            $this->dispatcher,
            $this->createMock(ConfigResolverInterface::class),
        );
    }

    public function testResolve(): void
    {
        $this->dispatcher
            ->expects(self::exactly(2))
            ->method('parse')
            ->with(
                self::logicalOr(
                    self::identicalTo([
                        [
                            'type' => 'action',
                            'value' => 'create',
                        ],
                        [
                            'type' => 'object_class',
                            'class' => 'Ibexa\Contracts\Core\Repository\Values\Content\Content',
                        ],
                    ]),
                    self::identicalTo([
                        ['type' => 'logged_at', 'direction' => 'DESC'],
                    ]),
                ),
                self::logicalOr(
                    self::identicalTo('application/vnd.ibexa.api.internal.activity_log.sort_clauses'),
                    self::identicalTo('application/vnd.ibexa.api.internal.activity_log.criteria'),
                )
            )
            ->willReturn([]);

        $uri = '?limit=2&offset=1'
            . '&filter%5B0%5D%5Btype%5D=action&filter%5B0%5D%5Bvalue%5D=create'
            . '&sort%5B0%5D%5Btype%5D=logged_at&sort%5B0%5D%5Bdirection%5D=DESC'
            . '&filter%5B1%5D%5Btype%5D=object_class&filter%5B1%5D%5Bclass%5D=Ibexa%5CContracts%5CCore%5CRepository%5CValues%5CContent%5CContent';
        $request = Request::create($uri);
        $argument = new ArgumentMetadata('query', Query::class, false, false, null);

        $arguments = $this->resolver->resolve($request, $argument);
        $arguments = iterator_to_array($arguments);

        self::assertCount(1, $arguments);
        [$query] = $arguments;
        self::assertInstanceOf(Query::class, $query);
        self::assertSame(1, $query->offset);
        self::assertSame(2, $query->limit);
        self::assertSame([], $query->criteria);
        self::assertSame([], $query->sortClauses);
    }

    public function testSupports(): void
    {
        $uri = '?limit=2&offset=1'
            . '&filter%5B0%5D%5Btype%5D=action&filter%5B0%5D%5Bvalue%5D=create'
            . '&sort%5B0%5D%5Btype%5D=logged_at&sort%5B0%5D%5Bdirection%5D=DESC'
            . '&filter%5B1%5D%5Btype%5D=object_class&filter%5B1%5D%5Bclass%5D=Ibexa%5CContracts%5CCore%5CRepository%5CValues%5CContent%5CContent';
        $request = Request::create($uri);
        $argument = new ArgumentMetadata('query', Query::class, false, false, null);

        self::assertTrue($this->resolver->supports($request, $argument));
    }
}
