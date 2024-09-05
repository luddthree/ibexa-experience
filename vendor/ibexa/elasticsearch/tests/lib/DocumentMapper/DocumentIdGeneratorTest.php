<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\DocumentMapper;

use Ibexa\Elasticsearch\DocumentMapper\DocumentIdGenerator;
use PHPUnit\Framework\TestCase;

final class DocumentIdGeneratorTest extends TestCase
{
    private const EXAMPLE_CONTENT_ID = 2;
    private const EXAMPLE_LOCATION_ID = 1;
    private const EXAMPLE_LANGUAGE_CODE = 'eng-GB';

    public function testGenerateContentDocumentId(): void
    {
        $generator = new DocumentIdGenerator();

        $this->assertEquals(
            'content-2-eng-GB',
            $generator->generateContentDocumentId(self::EXAMPLE_CONTENT_ID, self::EXAMPLE_LANGUAGE_CODE)
        );
    }

    public function testGenerateLocationDocumentId(): void
    {
        $generator = new DocumentIdGenerator();

        $this->assertEquals(
            'location-1-eng-GB',
            $generator->generateLocationDocumentId(self::EXAMPLE_LOCATION_ID, self::EXAMPLE_LANGUAGE_CODE)
        );
    }
}

class_alias(DocumentIdGeneratorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\DocumentMapper\DocumentIdGeneratorTest');
