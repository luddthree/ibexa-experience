<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\HtmlDiff;

use Ibexa\VersionComparison\HtmlDiff\Lexer\Tokenizer;
use Ibexa\VersionComparison\HtmlDiff\Token\TokenFactory;
use PHPUnit\Framework\TestCase;

class TokenizerTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Lexer\TokenizerInterface */
    private $tokenizer;

    public function setUp(): void
    {
        $this->tokenizer = new Tokenizer(
            new TokenFactory()
        );
    }

    /**
     * @dataProvider htmlAndListOfWordsDataProvider
     *
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $listOfTokens
     *
     * @throws \Exception
     */
    public function testConvertHtmlToListOfWords(string $inputHtml, array $listOfTokens)
    {
        $result = $this->tokenizer->convertHtmlToTokens($inputHtml);

        $this->assertEquals(
            $listOfTokens,
            $result
        );
    }

    public function htmlAndListOfWordsDataProvider()
    {
        $tokenFactory = new TokenFactory();

        return [
            [
                'No tags',
                [
                    $tokenFactory->createToken('No'),
                    $tokenFactory->createToken(' '),
                    $tokenFactory->createToken('tags'),
                ],
            ],
            [
                '<span><a href="localhost"><img src="image.png"></a></span>',
                [
                    $tokenFactory->createToken('<span>'),
                    $tokenFactory->createToken('<a href="localhost">'),
                    $tokenFactory->createToken('<img src="image.png">'),
                    $tokenFactory->createToken('</a>'),
                    $tokenFactory->createToken('</span>'),
                ],
            ],
        ];
    }

    public function testHashOfImgTag()
    {
        $old = $this->tokenizer->convertHtmlToTokens('<img src="image.png">');
        $newClass = $this->tokenizer->convertHtmlToTokens('<img class="class" src="image.png">');
        $newImage = $this->tokenizer->convertHtmlToTokens('<img class="class" src="new_image.png">');

        $this->assertEquals(
            $old[0]->getHash(),
            $newClass[0]->getHash()
        );

        $this->assertNotEquals(
            $old[0]->getHash(),
            $newImage[0]->getHash()
        );
    }
}

class_alias(TokenizerTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\HtmlDiff\TokenizerTest');
