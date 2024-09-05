<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\DomainMapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup as SpiCustomerGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\DomainMapper
 */
final class DomainMapperTest extends TestCase
{
    private const MOCK_CUSTOMER_GROUP_ID = 42;
    private const MOCK_CUSTOMER_GROUP_IDENTIFIER = 'foo_identifier';
    private const MOCK_CUSTOMER_GROUP_PRICE_RATE = '-42.0';
    private const MOCK_CUSTOMER_GROUP_LANG_FOO = 'lang-foo';
    private const MOCK_CUSTOMER_GROUP_LANG_BAR = 'lang-bar';

    private const MOCK_LANG_FOO_ID = 1;
    private const MOCK_LANG_BAR_ID = 2;

    private LanguageHandler $languageHandler;

    private LanguageResolver $languageResolver;

    private DomainMapper $domainMapper;

    protected function setUp(): void
    {
        $this->languageHandler = $this->createMock(LanguageHandler::class);
        $this->languageHandler
            ->method('loadListByLanguageCodes')
            ->willReturn([
                $this->createLanguageMock(self::MOCK_LANG_FOO_ID, self::MOCK_CUSTOMER_GROUP_LANG_FOO),
                $this->createLanguageMock(self::MOCK_LANG_BAR_ID, self::MOCK_CUSTOMER_GROUP_LANG_BAR),
            ]);

        $this->languageHandler
            ->method('loadList')
            ->with(
                self::identicalTo([self::MOCK_LANG_FOO_ID, self::MOCK_LANG_BAR_ID]),
            )
            ->willReturn([
                $this->createLanguageMock(self::MOCK_LANG_FOO_ID, self::MOCK_CUSTOMER_GROUP_LANG_FOO),
                $this->createLanguageMock(self::MOCK_LANG_BAR_ID, self::MOCK_CUSTOMER_GROUP_LANG_BAR),
            ]);

        $this->languageResolver = $this->createMock(LanguageResolver::class);

        $this->domainMapper = new DomainMapper($this->languageHandler, $this->languageResolver);
    }

    public function testCreateFromSpi(): void
    {
        $spiCustomerGroup = new SpiCustomerGroup();

        $spiCustomerGroup->id = self::MOCK_CUSTOMER_GROUP_ID;
        $spiCustomerGroup->identifier = self::MOCK_CUSTOMER_GROUP_IDENTIFIER;
        $spiCustomerGroup->globalPriceRate = self::MOCK_CUSTOMER_GROUP_PRICE_RATE;

        $spiCustomerGroup->setName(self::MOCK_LANG_FOO_ID, 'translation_1_name');

        $spiCustomerGroup->setName(self::MOCK_LANG_BAR_ID, 'translation_2_name');
        $spiCustomerGroup->setDescription(self::MOCK_LANG_BAR_ID, 'translation_2_description');

        $result = $this->domainMapper->createFromSpi($spiCustomerGroup, [
            $this->createLanguageMock(self::MOCK_LANG_BAR_ID, self::MOCK_CUSTOMER_GROUP_LANG_BAR),
        ]);
        self::assertTranslationSame('translation_2_name', 'translation_2_description', $result);

        $result = $this->domainMapper->createFromSpi($spiCustomerGroup, [
            $this->createLanguageMock(self::MOCK_LANG_FOO_ID, self::MOCK_CUSTOMER_GROUP_LANG_FOO),
        ]);
        self::assertTranslationSame('translation_1_name', '', $result);

        $result = $this->domainMapper->createFromSpi($spiCustomerGroup, [
            $this->createLanguageMock(self::MOCK_LANG_FOO_ID, self::MOCK_CUSTOMER_GROUP_LANG_FOO),
            $this->createLanguageMock(self::MOCK_LANG_BAR_ID, self::MOCK_CUSTOMER_GROUP_LANG_BAR),
        ]);
        self::assertTranslationSame('translation_1_name', '', $result);

        $result = $this->domainMapper->createFromSpi($spiCustomerGroup, [
            $this->createLanguageMock(self::MOCK_LANG_BAR_ID, self::MOCK_CUSTOMER_GROUP_LANG_BAR),
            $this->createLanguageMock(self::MOCK_LANG_FOO_ID, self::MOCK_CUSTOMER_GROUP_LANG_FOO),
        ]);
        self::assertTranslationSame('translation_2_name', 'translation_2_description', $result);

        $result = $this->domainMapper->createFromSpi($spiCustomerGroup);
        self::assertTranslationSame('translation_1_name', '', $result);
    }

    private static function assertTranslationSame(string $name, string $description, CustomerGroup $result): void
    {
        self::assertSame(self::MOCK_CUSTOMER_GROUP_ID, $result->getId());
        self::assertSame(self::MOCK_CUSTOMER_GROUP_IDENTIFIER, $result->getIdentifier());
        self::assertSame(self::MOCK_CUSTOMER_GROUP_PRICE_RATE, $result->getGlobalPriceRate());
        self::assertSame($name, $result->getName());
        self::assertSame($description, $result->getDescription());
        self::assertSame([
            self::MOCK_CUSTOMER_GROUP_LANG_FOO,
            self::MOCK_CUSTOMER_GROUP_LANG_BAR,
        ], $result->getLanguages());
    }

    private function createLanguageMock(int $id, string $code): Language
    {
        $language = $this->createMock(Language::class);
        $language->id = $id;
        $language->languageCode = $code;

        return $language;
    }
}
