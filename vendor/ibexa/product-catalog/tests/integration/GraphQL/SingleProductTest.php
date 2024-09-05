<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\GraphQL;

final class SingleProductTest extends BaseGraphQLWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/graphql';

    public function testSingleProduct(): void
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
                        single(code: "0001") {
                            code
                            name
                            productType {
                                identifier
                                name
                            }
                            thumbnail {
                                uri
                                width
                                height
                                alternativeText
                                mimeType
                            }
                            createdAt {
                                format(constant: atom)
                            }
                            updatedAt {
                                format(constant: atom)
                            }
                            fields {
                                _all {
                                    fieldDefIdentifier
                                    value
                                }
                            }
                            _content {
                                id
                                contentTypeId
                                name
                                section {
                                    id
                                    identifier
                                    name
                                }
                                currentVersionNo
                                currentVersion {
                                    name
                                }
                                ownerId
                                locations {
                                    id
                                    contentId
                                }
                                relations {
                                    sourceFieldDefinitionIdentifier
                                    type
                                }
                                states {
                                    identifier
                                    priority
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
        return 'SingleProduct';
    }
}
