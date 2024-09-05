<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Generator\Reference\UserGroupGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\Generator\Reference\UserGroupGenerator
 */
final class UserGroupGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $generator = new UserGroupGenerator();

        $content = $this->createMock(UserGroup::class);
        $contentType = new ContentType([
            'identifier' => 'foo',
        ]);
        $content->method('getContentType')
            ->willReturn($contentType);

        $references = $generator->generate($content);

        self::assertEquals([
            new ReferenceDefinition('ref__user_group__foo__', 'user_group_id'),
        ], $references);
    }
}

class_alias(UserGroupGeneratorTest::class, 'Ibexa\Platform\Tests\Migration\Generator\Reference\UserGroupGeneratorTest');
