<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Creator;

use Ibexa\Personalization\Request\Item\ItemIdsPackage;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Request\Item\UriPackage;

final class PackageListCreator
{
    public static function createUriPackageList(): PackageList
    {
        return new PackageList(
            [
                new UriPackage(
                    1,
                    'foo',
                    'eng-GB',
                    'link.invalid/content/id/1'
                ),
                new UriPackage(
                    2,
                    'bar',
                    'eng-GB',
                    'link.invalid/content/id/2'
                ),
            ]
        );
    }

    public static function createItemIdsPackageList(): PackageList
    {
        return new PackageList(
            [
                new ItemIdsPackage(
                    1,
                    'foo',
                    'eng-GB',
                    ['1', '2', '3']
                ),
                new ItemIdsPackage(
                    2,
                    'bar',
                    'eng-GB',
                    ['4']
                ),
            ]
        );
    }
}
