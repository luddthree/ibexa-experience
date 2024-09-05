<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\REST\RootResource;

use Ibexa\Contracts\Test\Rest\Request\Value\EndpointRequestDefinition;
use Ibexa\Tests\Integration\CorporateAccount\REST\BaseCorporateAccountRestWebTestCase;

final class GetRootResourceTest extends BaseCorporateAccountRestWebTestCase
{
    private const METHOD = 'GET';
    private const URI = '/api/ibexa/v2/corporate';

    public static function getEndpointsToTest(): iterable
    {
        yield new EndpointRequestDefinition(
            self::METHOD,
            self::URI,
            'CorporateAccountRoot',
            self::generateMediaTypeString('CorporateAccountRoot', 'xml')
        );
    }
}
