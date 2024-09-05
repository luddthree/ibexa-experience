<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Content;

use Ibexa\Personalization\Content\DataResolverInterface;
use Ibexa\Tests\Integration\Personalization\BaseIntegrationTestCase;

/**
 * @covers \Ibexa\Personalization\Content\DataResolver
 */
final class DataResolverTest extends BaseIntegrationTestCase
{
    private DataResolverInterface $dataResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataResolver = self::getServiceByClassName(DataResolverInterface::class);
    }

    /**
     * @dataProvider provideDataForTestResolve
     *
     * @param array{
     *     title: string,
     *     uri: array<string>,
     *     short_title: string,
     *     image: string|null,
     *     publish_date: null|string,
     *     tags: array<string>,
     *     location: string|null,
     * } $expectedData
     */
    public function testResolve(
        string $remoteId,
        string $languageCode,
        array $expectedData
    ): void {
        $content = $this->getContentService()->loadContentByRemoteId($remoteId);
        $contentInfo = $content->getVersionInfo()->getContentInfo();
        $mainLocation = $contentInfo->getMainLocation();
        $categoryPath = '';
        if (null !== $mainLocation) {
            $categoryPath = $mainLocation->getPathString();
        }

        self::assertEquals(
            array_merge(
                $expectedData,
                [
                    'categoryPath' => [$categoryPath],
                    'mainLocation' => [
                        'href' => '/api/ibexa/v2/content/locations' . $categoryPath,
                    ],
                    'locations' => [
                        'href' => '/api/ibexa/v2/content/objects/' . $contentInfo->getId() . '/locations',
                    ],
                    'publishedDate' => $contentInfo->publishedDate->format('c'),
                ]
            ),
            $this->dataResolver->resolve(
                $content,
                $languageCode
            )
        );
    }

    /**
     * @return iterable<array{
     *     string,
     *     string,
     *     array{
     *         title: string,
     *         uri: array<string>,
     *         short_title: string,
     *         image: string|null,
     *         publish_date: null|string,
     *         tags: array<string>,
     *         location: string|null,
     *     },
     * }>
     */
    public function provideDataForTestResolve(): iterable
    {
        yield 'Resolves content in English language' => [
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            self::LANGUAGE_CODE_ENG,
            [
                'title' => 'Unleash the power of Ibexa Personalization',
                'uri' => [
                    '/Unleash-the-power-of-Ibexa-Personalization',
                ],
                'short_title' => '',
                'author' => [],
                'image' => null,
                'publish_date' => null,
                'tags' => ['personalization', 'recommendations'],
                'location' => null,
            ],
        ];

        yield 'Resolves content in Polish language' => [
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            self::LANGUAGE_CODE_POL,
            [
                'title' => 'Uwolnij potencjał Ibexa Personalization',
                'uri' => [
                    '/Uwolnij-potencjal-Ibexa-Personalization',
                ],
                'short_title' => '',
                'author' => [],
                'image' => null,
                'publish_date' => null,
                'tags' => ['personalizacja', 'rekomendacje'],
                'location' => null,
            ],
        ];
    }
}
