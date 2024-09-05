<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class AttributeCreateWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/attributes';

    public function testAttributeCreate(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => 'application/vnd.ibexa.api.AttributeCreate+json',
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.AttributeCreate+json',
            ],
            <<<JSON
                {
                    "AttributeCreate": {
                        "identifier": "test_attr",
                        "type": "checkbox",
                        "group": "dimensions",
                        "names": {
                            "eng-GB": "Attribute"
                        },
                        "descriptions": {
                            "eng-GB": "description"
                        },
                        "position": 0,
                        "options": {
                            "foo": "bar"
                        }
                    }
                }
            JSON
        );
    }

    protected function getResourceType(): string
    {
        return 'Attribute';
    }
}
