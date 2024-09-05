<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\GraphQL;

use Ibexa\Contracts\Core\Test\IbexaKernelTestTrait;

final class ProductTypesWithMatrixFieldTypeTest extends BaseGraphQLWebTestCase
{
    use IbexaKernelTestTrait;

    private const METHOD = 'POST';
    private const URI = '/graphql';

    public function testProductTypesWithMatrixFieldType(): void
    {
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [],
            '',
            [
                'query' => '
                {
                    products {
                        _types {
                            trousers {
                                _contentType {
                                    fieldDefinitions {
                                        fieldTypeIdentifier
                                    }
                                }
                            }
                        }
                    }
                }',
            ]
        );
    }

    protected function getResourceType(): ?string
    {
        return 'ProductTypesWithMatrixFieldTypeList';
    }
}
