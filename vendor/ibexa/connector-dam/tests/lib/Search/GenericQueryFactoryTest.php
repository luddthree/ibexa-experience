<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connector\Dam\Search;

use Ibexa\Connector\Dam\Search\GenericQueryFactory;
use Ibexa\Contracts\Connector\Dam\Search\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class GenericQueryFactoryTest extends TestCase
{
    /** @var \Ibexa\Connector\Dam\Search\GenericQueryFactory */
    private $queryFactory;

    protected function setUp(): void
    {
        $this->queryFactory = new GenericQueryFactory();
    }

    public function testBuildQueryWithPhrase(): void
    {
        $request = new Request(['query' => 'search_phrase']);
        $query = $this->queryFactory->buildFromRequest($request);

        $this->assertEquals(
            new Query('search_phrase'),
            $query
        );
    }

    public function testBuildQueryWithoutPhrase(): void
    {
        $request = new Request();
        $this->expectException(BadRequestHttpException::class);
        $this->queryFactory->buildFromRequest($request);
    }
}

class_alias(GenericQueryFactoryTest::class, 'Ibexa\Platform\Tests\Connector\Dam\Search\GenericQueryFactoryTest');
