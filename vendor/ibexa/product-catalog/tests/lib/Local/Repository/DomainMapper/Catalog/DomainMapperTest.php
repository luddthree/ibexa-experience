<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\DomainMapper\Catalog;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\CatalogQuerySerializer;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog\DomainMapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\Catalog as SPICatalog;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog;
use PHPUnit\Framework\TestCase;

final class DomainMapperTest extends TestCase
{
    private const LANGUAGE_CODE_EN = 'eng-GB';
    private const LANGUAGE_CODE_EN_NAME = 'name_en';
    private const LANGUAGE_CODE_EN_DESC = 'desc_en';
    private const LANGUAGE_CODE_PL = 'pol-PL';
    private const LANGUAGE_CODE_PL_NAME = 'name_pl';
    private const LANGUAGE_CODE_PL_DESC = 'desc_pl';
    private const LANGUAGE_ID_EN = 2;
    private const LANGUAGE_ID_PL = 4;

    private const CATALOG_ID = 1;
    private const CATALOG_IDENTIFIER = 'foo';
    private const CATALOG_CREATOR_ID = 14;
    private const CATALOG_CREATED = 1647865210;
    private const CATALOG_MODIFIED = 1648654100;
    private const CATALOG_STATUS = 'new';
    private const CATALOG_QUERY = '';

    private DomainMapper $domainMapper;

    protected function setUp(): void
    {
        $languageResolver = $this->createMock(LanguageResolver::class);
        $languageResolver
            ->method('getPrioritizedLanguages')
            ->willReturn([self::LANGUAGE_CODE_EN, self::LANGUAGE_CODE_PL]);

        $languageHandler = $this->createMock(LanguageHandlerInterface::class);
        $languageHandler
            ->method('loadList')
            ->willReturnCallback(static function (array $ids): array {
                $map = [
                    self::LANGUAGE_ID_PL => new Language([
                        'id' => self::LANGUAGE_ID_EN,
                        'languageCode' => self::LANGUAGE_CODE_EN,
                    ]),
                    self::LANGUAGE_ID_EN => new Language([
                        'id' => self::LANGUAGE_ID_PL,
                        'languageCode' => self::LANGUAGE_CODE_PL,
                    ]),
                ];

                return array_map(static fn (int $id): Language => $map[$id], $ids);
            });
        $languageHandler
            ->method('loadListByLanguageCodes')
            ->with([self::LANGUAGE_CODE_EN, self::LANGUAGE_CODE_PL])
            ->willReturn([
                new Language(['id' => self::LANGUAGE_ID_EN, 'languageCode' => self::LANGUAGE_CODE_EN]),
                new Language(['id' => self::LANGUAGE_ID_PL, 'languageCode' => self::LANGUAGE_CODE_PL]),
            ]);

        $this->domainMapper = new DomainMapper(
            $languageHandler,
            $languageResolver,
            new CatalogQuerySerializer()
        );
    }

    /**
     * @dataProvider providerForCreateCatalog
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language[]|null $prioritizedLanguages
     */
    public function testCreateFromSpi(
        SPICatalog $spiCatalog,
        ?iterable $prioritizedLanguages,
        Catalog $expectedCatalog
    ): void {
        self::assertEquals(
            $expectedCatalog,
            $this->domainMapper->createFromSpi(
                $spiCatalog,
                $this->createMock(User::class),
                $prioritizedLanguages
            )
        );
    }

    /**
     * @phpstan-return iterable<string,array{
     *     \Ibexa\ProductCatalog\Local\Persistence\Values\Catalog,
     *     \Ibexa\Contracts\Core\Persistence\Content\Language[]|null,
     *     \Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog
     * }>
     */
    public function providerForCreateCatalog(): iterable
    {
        yield 'attribute group' => [
            $this->getSpiCatalog(),
            [
                new Language(
                    [
                        'id' => self::LANGUAGE_ID_EN,
                        'languageCode' => self::LANGUAGE_CODE_EN,
                    ]
                ),
            ],
            $this->getCatalog(),
        ];

        yield 'missing language in prioritizedLanguages' => [
            $this->getSpiCatalog(),
            [new Language(['id' => 8, 'languageCode' => 'ger-DE'])],
            $this->getCatalog('', ''),
        ];

        yield 'null as prioritizedLanguages' => [
            $this->getSpiCatalog(),
            null,
            $this->getCatalog(),
        ];
    }

    private function getSpiCatalog(): SPICatalog
    {
        $spiCatalog = new SPICatalog();
        $spiCatalog->id = self::CATALOG_ID;
        $spiCatalog->identifier = self::CATALOG_IDENTIFIER;
        $spiCatalog->creatorId = self::CATALOG_CREATOR_ID;
        $spiCatalog->created = self::CATALOG_CREATED;
        $spiCatalog->modified = self::CATALOG_MODIFIED;
        $spiCatalog->status = self::CATALOG_STATUS;
        $spiCatalog->query = self::CATALOG_QUERY;
        $names = [
            self::LANGUAGE_ID_EN => self::LANGUAGE_CODE_EN_NAME,
            self::LANGUAGE_ID_PL => self::LANGUAGE_CODE_PL_NAME,
        ];
        $descriptions = [
            self::LANGUAGE_ID_EN => self::LANGUAGE_CODE_EN_DESC,
            self::LANGUAGE_ID_PL => self::LANGUAGE_CODE_PL_DESC,
        ];

        foreach ($names as $languageId => $name) {
            $spiCatalog->setName($languageId, $name);
            $spiCatalog->setDescription($languageId, $descriptions[$languageId]);
        }

        return $spiCatalog;
    }

    private function getCatalog(
        string $name = self::LANGUAGE_CODE_EN_NAME,
        string $description = self::LANGUAGE_CODE_EN_DESC
    ): Catalog {
        return new Catalog(
            self::CATALOG_ID,
            self::CATALOG_IDENTIFIER,
            $name,
            [self::LANGUAGE_CODE_PL, self::LANGUAGE_CODE_EN],
            $this->createMock(User::class),
            self::CATALOG_CREATED,
            self::CATALOG_MODIFIED,
            self::CATALOG_STATUS,
            null,
            $description
        );
    }
}
