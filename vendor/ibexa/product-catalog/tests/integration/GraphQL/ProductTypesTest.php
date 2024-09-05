<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\GraphQL;

final class ProductTypesTest extends BaseGraphQLWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/graphql';

    public function testProductTypes(): void
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
                            dress {
                                identifier
                                name
                                attributesDefinitions {
                                    isRequired
                                    isDiscriminator
                                    attributeDefinition {
                                        name
                                        description
                                        identifier
                                        type {
                                            identifier
                                            name
                                        }
                                        group {
                                            identifier
                                            name
                                            position
                                        }
                                        position
                                        options {
                                            key
                                            name
                                        }
                                    }
                                }
                                _contentType {
                                    id
                                    description
                                    status
                                    creationDate {
                                        format(constant: atom)
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
        return 'ProductTypesList';
    }
}
