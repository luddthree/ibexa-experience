<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\GraphQL;

final class AllProductsTest extends BaseGraphQLWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/graphql';

    public function testAllProducts(): void
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
                        all {
                            edges {
                                node {
                                    code
                                    name
                                    fields {
                                        _all {
                                          fieldDefIdentifier
                                          value
                                        }
                                        ... on DressContentFields {
                                            name
                                        }
                                    }
                                    attributes {
                                        _all {
                                            identifier
                                            ... on IntegerAttribute {
                                                name
                                                integerValue: value
                                            }
                                            ... on CheckboxAttribute {
                                                name
                                                checkboxValue: value
                                            }
                                        }
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
        return 'AllProducts';
    }
}
