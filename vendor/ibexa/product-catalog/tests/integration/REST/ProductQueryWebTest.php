<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

final class ProductQueryWebTest extends BaseRestWebTestCase
{
    private const METHOD = 'POST';
    private const URI = '/api/ibexa/v2/product/catalog/products/view';

    protected function getResourceType(): string
    {
        return 'ProductView';
    }

    /**
     * @dataProvider provideForProductQuery
     * @dataProvider provideForProductQueryWithAggregations
     * @dataProvider provideForProductQueryWithAttributeAggregations
     * @dataProvider provideForProductQueryWithVirtualProducts
     * @dataProvider provideForProductQueryWithVirtualProductsSetToFalse
     */
    public function testProductsQuery(string $requestBody, string $requestType): void
    {
        $this->client->catchExceptions(false);
        $this->assertClientRequest(
            self::METHOD,
            self::URI,
            [
                'CONTENT_TYPE' => "application/vnd.ibexa.api.ProductViewInput+$requestType",
                'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductView+json',
            ],
            $requestBody
        );
    }

    /**
     * @dataProvider provideForProductQuery
     * @dataProvider provideForProductQueryWithAggregations
     * @dataProvider provideForProductQueryWithAttributeAggregations
     * @dataProvider provideForProductQueryWithVirtualProducts
     * @dataProvider provideForProductQueryWithVirtualProductsSetToFalse
     */
    public function testProductQueryWithXMLResponse(string $requestBody, string $requestType): void
    {
        $this->client->catchExceptions(false);
        $this->client->request(self::METHOD, self::URI, [], [], [
            'CONTENT_TYPE' => "application/vnd.ibexa.api.ProductViewInput+$requestType",
            'HTTP_ACCEPT' => 'application/vnd.ibexa.api.ProductView+xml',
        ], $requestBody);
        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<array{non-empty-string, non-empty-string}>
     */
    public function provideForProductQuery(): iterable
    {
        yield 'Queries: XML' => [
            <<<XML
                <ViewInput>
                    <identifier>TitleView</identifier>
                    <ProductQuery>
                        <limit>10</limit>
                        <offset>0</offset>
                        <Filter>
                            <ProductNameCriterion>Dress A</ProductNameCriterion>
                            <ProductCodeCriterion>0001</ProductCodeCriterion>
                            <ProductAvailabilityCriterion>true</ProductAvailabilityCriterion>
                        </Filter>
                        <SortClauses>
                            <ProductName>descending</ProductName>
                        </SortClauses>
                    </ProductQuery>
                </ViewInput>
            XML,
            'xml',
        ];

        yield 'Queries: JSON' => [
            <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "ProductQuery": {
                            "limit": "10",
                            "offset": "0",
                            "Filter": {
                                "ProductNameCriterion": "Dress A",
                                "ProductCodeCriterion": "0001",
                                "ProductAvailabilityCriterion": false
                            },
                            "SortClauses": {
                                "ProductName": "descending"
                            }
                        }
                    }
                }
            JSON,
            'json',
        ];
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function provideForProductQueryWithVirtualProducts(): iterable
    {
        yield 'Criteria: XML - IsVirtualCriterion=true' => [
            $this->getIsVirtualXmlCriterionViewInput('true'),
            'xml',
        ];

        yield 'Criteria: JSON - IsVirtualCriterion=true' => [
            $this->getIsVirtualJsonCriterionViewInput('true'),
            'json',
        ];
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function provideForProductQueryWithVirtualProductsSetToFalse(): iterable
    {
        yield 'Criteria: XML - IsVirtualCriterion=false' => [
            $this->getIsVirtualXmlCriterionViewInput('false', '0001'),
            'xml',
        ];

        yield 'Criteria: JSON - IsVirtualCriterion=false' => [
            $this->getIsVirtualJsonCriterionViewInput('false', '0001'),
            'json',
        ];
    }

    /**
     * @return iterable<array{non-empty-string, non-empty-string}>
     */
    public static function provideForProductQueryWithAggregations(): iterable
    {
        yield 'Aggregations: JSON - ProductPriceRange' => [
            self::getJsonViewInputWithAggregations(<<<JSON
                [
                    {
                        "ProductPriceRange": {
                            "name": "test",
                            "currencyCode": "PLN",
                            "ranges": [
                                {"from": 0, "to": 10000}
                            ]
                        }
                    }
                ]
            JSON),
            'json',
        ];

        yield 'Aggregations: XML - ProductPriceRange' => [
            self::getXmlAggregationTemplate(<<<XML
                <value>
                    <ProductPriceRange>
                        <name>test</name>
                        <currencyCode>PLN</currencyCode>
                        <ranges>
                            <range>
                                <from>0</from>
                                <to>10000</to>
                            </range>
                        </ranges>
                    </ProductPriceRange>
                </value>
            XML),
            'xml',
        ];

        yield 'Aggregations: JSON - ProductAvailability' => [
            self::getJsonViewInputWithAggregations(<<<JSON
                [
                    {
                        "ProductAvailabilityTerm": {
                            "name": "test"
                        }
                    }
                ]
            JSON),
            'json',
        ];

        yield 'Aggregations: XML - ProductAvailability' => [
            self::getXmlAggregationTemplate(<<<XML
                <value>
                    <ProductAvailabilityTerm>
                        <name>test</name>
                    </ProductAvailabilityTerm>
                </value>
            XML),
            'xml',
        ];

        yield 'Aggregations: JSON - ProductTypeTerm' => [
            self::getJsonViewInputWithAggregations(<<<JSON
                [
                    {
                        "ProductTypeTerm": {
                            "name": "test"
                        }
                    }
                ]
            JSON),
            'json',
        ];

        yield 'Aggregations: XML - ProductTypeTerm' => [
            self::getXmlAggregationTemplate(<<<XML
                <value>
                    <ProductTypeTerm>
                        <name>test</name>
                    </ProductTypeTerm>
                </value>
            XML),
            'xml',
        ];
    }

    /**
     * @return iterable<array{non-empty-string, non-empty-string}>
     */
    public static function provideForProductQueryWithAttributeAggregations(): iterable
    {
        $attributes = [
            'AttributeBooleanTerm',
            'AttributeColorTerm',
            'AttributeFloatStats',
            'AttributeIntegerStats',
            'AttributeSelectionTerm',
        ];

        foreach ($attributes as $attribute) {
            yield "Aggregations: JSON - $attribute" => [
                self::getJsonViewInputWithAggregations(<<<JSON
                    [
                        {
                            "$attribute": {
                                "name": "test",
                                "attributeDefinitionIdentifier": "foo"
                            }
                        }
                    ]
                JSON),
                'json',
            ];

            $str = <<<XML
                <value>
                    <__ATTRIBUTE_NAME__>
                        <name>test</name>
                        <attributeDefinitionIdentifier>foo</attributeDefinitionIdentifier>
                    </__ATTRIBUTE_NAME__>
                </value>
            XML;

            $str = str_replace('__ATTRIBUTE_NAME__', $attribute, $str);

            yield "Aggregations: XML - $attribute" => [
                self::getXmlAggregationTemplate($str),
                'xml',
            ];
        }

        $attributes = [
            'AttributeFloatRange',
            'AttributeIntegerRange',
        ];

        foreach ($attributes as $attribute) {
            yield "Aggregations: JSON - $attribute" => [
                self::getJsonViewInputWithAggregations(<<<JSON
                    [
                        {
                            "$attribute": {
                                "name": "test",
                                "attributeDefinitionIdentifier": "foo",
                                "ranges": [
                                    {
                                        "from": 0,
                                        "to": 10000
                                    }
                                ]
                            }
                        }
                    ]
                JSON),
                'json',
            ];

            $str = <<<XML
                <value>
                    <__ATTRIBUTE_NAME__>
                        <name>test</name>
                        <attributeDefinitionIdentifier>foo</attributeDefinitionIdentifier>
                        <ranges>
                            <range>
                                <from>0</from>
                                <to>10000</to>
                            </range>
                        </ranges>
                    </__ATTRIBUTE_NAME__>
                </value>
            XML;

            $str = str_replace('__ATTRIBUTE_NAME__', $attribute, $str);

            yield "Aggregations: XML - $attribute" => [
                self::getXmlAggregationTemplate($str),
                'xml',
            ];
        }
    }

    /**
     * @param non-empty-string $aggregations
     *
     * @return non-empty-string
     */
    private static function getJsonViewInputWithAggregations(string $aggregations): string
    {
        return <<<JSON
            {
                "ViewInput": {
                    "identifier": "TitleView",
                    "ProductQuery": {
                        "limit": "10",
                        "offset": "0",
                        "Filter": {
                            "ProductNameCriterion": "Dress A",
                            "ProductCodeCriterion": "0001",
                            "ProductAvailabilityCriterion": false
                        },
                        "SortClauses": {
                            "ProductName": "descending"
                        },
                        "Aggregations": $aggregations
                    }
                }
            }
        JSON;
    }

    /**
     * @param non-empty-string $aggregations
     *
     * @return non-empty-string
     */
    private static function getXmlAggregationTemplate(string $aggregations): string
    {
        return <<<XML
            <ViewInput>
                <identifier>TitleView</identifier>
                <ProductQuery>
                    <limit>10</limit>
                    <offset>0</offset>
                    <Filter>
                        <ProductNameCriterion>Dress A</ProductNameCriterion>
                        <ProductCodeCriterion>0001</ProductCodeCriterion>
                        <ProductAvailabilityCriterion>true</ProductAvailabilityCriterion>
                    </Filter>
                    <SortClauses>
                        <ProductName>descending</ProductName>
                    </SortClauses>
                    <Aggregations>$aggregations</Aggregations>
                </ProductQuery>
            </ViewInput>
        XML;
    }

    private function getIsVirtualXmlCriterionViewInput(
        string $isVirtual,
        ?string $productCode = null
    ): string {
        $productCodeCriterion = null !== $productCode ?
            "<ProductCodeCriterion>$productCode</ProductCodeCriterion>"
            : null;

        return <<<XML
                <ViewInput>
                    <identifier>TitleView</identifier>
                    <ProductQuery>
                        <limit>10</limit>
                        <offset>0</offset>
                        <Filter>
                            <IsVirtualCriterion>$isVirtual</IsVirtualCriterion>
                            $productCodeCriterion
                        </Filter>
                        <SortClauses>
                            <ProductName>descending</ProductName>
                        </SortClauses>
                    </ProductQuery>
                </ViewInput>
            XML;
    }

    private function getIsVirtualJsonCriterionViewInput(
        string $isVirtual,
        ?string $productCode = null
    ): string {
        $productCodeCriterion = null !== $productCode
            ? "\"ProductCodeCriterion\": \"$productCode\","
            : null;

        return <<<JSON
                {
                    "ViewInput": {
                        "identifier": "TitleView",
                        "ProductQuery": {
                            "limit": "10",
                            "offset": "0",
                            "Filter": {
                                $productCodeCriterion
                                "IsVirtualCriterion": $isVirtual
                            },
                            "SortClauses": {
                                "ProductName": "descending"
                            }
                        }
                    }
                }
            JSON;
    }
}
