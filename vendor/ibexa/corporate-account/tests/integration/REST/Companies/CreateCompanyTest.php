<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\REST\Companies;

use Ibexa\Contracts\Test\Rest\Input\PayloadLoader;
use Ibexa\Contracts\Test\Rest\Request\Value\EndpointRequestDefinition;
use Ibexa\Tests\Integration\CorporateAccount\REST\BaseCorporateAccountRestWebTestCase;

final class CreateCompanyTest extends BaseCorporateAccountRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/corporate/companies';

    public static function getEndpointsToTest(): iterable
    {
        $payloadLoader = new PayloadLoader(dirname(__DIR__, 2) . '/Resources/REST/input_payloads');

        yield new EndpointRequestDefinition(
            self::METHOD,
            self::URI,
            'Company',
            self::generateMediaTypeString('Company', 'xml'),
            [],
            $payloadLoader->loadPayload('CompanyCreate', 'xml')
        );

        yield new EndpointRequestDefinition(
            self::METHOD,
            self::URI,
            'Company',
            self::generateMediaTypeString('Company', 'json'),
            [],
            $payloadLoader->loadPayload('CompanyCreate', 'json')
        );
    }
}
