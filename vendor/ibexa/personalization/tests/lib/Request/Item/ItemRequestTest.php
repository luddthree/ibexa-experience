<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Request\Item;

use Ibexa\Personalization\Request\Item\ItemRequest;
use Ibexa\Tests\Personalization\Creator\PackageListCreator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Request\Item\ItemRequest
 */
final class ItemRequestTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testCreateItemRequest(): void
    {
        $accessToken = '1qaz2wsx3edc4rfvZFAW145ZCwadad';

        $itemRequest = new ItemRequest(
            PackageListCreator::createUriPackageList(),
            $accessToken,
            ItemRequest::DEFAULT_HEADERS
        );

        $expectedJson = <<<JSON
            {
                "packages": [
                    {
                        "contentTypeId": 1,
                        "contentTypeName": "foo",
                        "lang": "eng-GB",
                        "uri": "link.invalid\/content\/id\/1"
                    },
                    {
                        "contentTypeId": 2,
                        "contentTypeName": "bar",
                        "lang": "eng-GB",
                        "uri": "link.invalid\/content\/id\/2"
                    }
                ],
                "accessToken": "1qaz2wsx3edc4rfvZFAW145ZCwadad",
                "importHeaders": {
                    "Content-Type": "application\/json",
                    "Accept": "application\/vnd.ibexa.api.contentList+json"
                }
            }
            JSON;

        self::assertEquals(
            $expectedJson,
            json_encode($itemRequest, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
        );
    }
}
