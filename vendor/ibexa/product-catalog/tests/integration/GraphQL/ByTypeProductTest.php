<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\GraphQL;

final class ByTypeProductTest extends BaseGraphQLWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/graphql';

    public function testProductsByType(): void
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
                        byType {
                            dresses (sortBy: [code, _desc]) {
                                edges {
                                    node {
                                        code
                                        name
                                        productType {
                                            identifier
                                            name
                                            attributesDefinitions {
                                                isRequired
                                                isDiscriminator
                                                attributeDefinition {
                                                    name
                                                    description
                                                    identifier
                                                }
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
                    }
                }',
            ]
        );
    }

    protected function getResourceType(): ?string
    {
        return 'ByTypeProducts';
    }
}
