<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\Generator\Reference\ContentGenerator;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\Generator\Reference\ContentGenerator
 */
final class ContentGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $generator = new ContentGenerator();

        $content = $this->createMock(Content::class);
        $contentType = new ContentType([
            'identifier' => 'foo',
        ]);
        $content->method('getContentType')
            ->willReturn($contentType);

        $references = $generator->generate($content);

        self::assertEquals([
            new ReferenceDefinition('ref__content__foo__', 'content_id'),
            new ReferenceDefinition('ref_location__foo__', 'location_id'),
            new ReferenceDefinition('ref_path__foo__', 'path'),
        ], $references);
    }
}

class_alias(ContentGeneratorTest::class, 'Ibexa\Platform\Tests\Migration\Generator\Reference\ContentGeneratorTest');
