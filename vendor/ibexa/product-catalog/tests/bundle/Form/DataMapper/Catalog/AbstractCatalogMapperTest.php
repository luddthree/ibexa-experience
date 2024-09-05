<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper\Catalog;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use PHPUnit\Framework\TestCase;

abstract class AbstractCatalogMapperTest extends TestCase
{
    protected const CATALOG_ID = 42;
    protected const CATALOG_IDENTIFIER = 'foo';
    protected const CATALOG_NAME = 'Foo';
    protected const CATALOG_DESCRIPTION = 'Description';

    protected const LANGUAGE_ID = 2;
    protected const LANGUAGE_CODE = 'eng-US';

    protected function getTestLanguage(): Language
    {
        return new Language([
            'id' => self::LANGUAGE_ID,
            'languageCode' => self::LANGUAGE_CODE,
        ]);
    }
}
