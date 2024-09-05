<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Workflow\Matcher;

use Ibexa\Workflow\Exception\NotFoundException;
use Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface;
use Ibexa\Workflow\Registry\MatcherValueMapperRegistry;
use PHPUnit\Framework\TestCase;

class MatcherValueMapperRegistryTest extends TestCase
{
    private const FOO_MATCHER = 'foo-matcher';
    private const BAR_MATCHER = 'bar-matcher';

    public function testGetMapper(): void
    {
        $fooMatcher = $this->craeteMatcherValueMapper(self::FOO_MATCHER);

        $registry = new MatcherValueMapperRegistry([$fooMatcher]);

        $this->assertEquals($fooMatcher, $registry->get(self::FOO_MATCHER));
    }

    /**
     * @depends testGetMapper
     */
    public function testGetNonExistingMapper(): void
    {
        $fooMatcher = $this->craeteMatcherValueMapper(self::FOO_MATCHER);

        $registry = new MatcherValueMapperRegistry([$fooMatcher]);

        $this->expectException(NotFoundException::class);

        $registry->get(self::BAR_MATCHER);
    }

    public function testHasMapper(): void
    {
        $fooMatcher = $this->craeteMatcherValueMapper(self::FOO_MATCHER);

        $registry = new MatcherValueMapperRegistry([$fooMatcher]);

        $this->assertTrue($registry->has(self::FOO_MATCHER));
        $this->assertFalse($registry->has(self::BAR_MATCHER));
    }

    /**
     * @depends testHasMapper
     */
    public function testSetMapper(): void
    {
        $fooMatcher = $this->craeteMatcherValueMapper(self::FOO_MATCHER);

        $registry = new MatcherValueMapperRegistry();
        $registry->set(self::FOO_MATCHER, $fooMatcher);

        $this->assertTrue($registry->has(self::FOO_MATCHER));
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Workflow\Mapper\Matcher\MatcherValueMapperInterface
     */
    private function craeteMatcherValueMapper(string $identifier): MatcherValueMapperInterface
    {
        $matcherValueMapper = $this->createMock(MatcherValueMapperInterface::class);
        $matcherValueMapper
            ->method('getIdentifier')
            ->willReturn($identifier);

        return $matcherValueMapper;
    }
}

class_alias(MatcherValueMapperRegistryTest::class, 'EzSystems\EzPlatformWorkflow\Tests\Matcher\MatcherValueMapperRegistryTest');
