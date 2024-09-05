<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument;
use Ibexa\Elasticsearch\ElasticSearch\Index\Group\LanguageGroupResolver;
use PHPUnit\Framework\TestCase;

final class LanguageGroupResolverTest extends TestCase
{
    private const EXAMPLE_LANGUAGE_CODE = 'eng-GB';

    public function testResolveDocumentGroup(): void
    {
        $document = new ContentDocument();
        $document->languageCode = self::EXAMPLE_LANGUAGE_CODE;

        $resolver = new LanguageGroupResolver();

        self::assertSame(
            'eng_gb',
            $resolver->resolveDocumentGroup($document)
        );
    }
}
