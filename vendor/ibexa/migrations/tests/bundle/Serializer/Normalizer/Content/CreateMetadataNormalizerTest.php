<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Serializer\Normalizer\Content;

use DateTime;
use Ibexa\Bundle\Migration\Serializer\Normalizer\Content\CreateMetadataNormalizer;
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Metadata\Section;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Content\CreateMetadataNormalizer
 */
final class CreateMetadataNormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Serializer\Normalizer\Content\CreateMetadataNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\Normalizer\NormalizerInterface */
    private $innerNormalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $innerDenormalizer;

    protected function setUp(): void
    {
        $this->normalizer = new CreateMetadataNormalizer();

        $this->innerNormalizer = $this->createMock(NormalizerInterface::class);
        $this->normalizer->setNormalizer($this->innerNormalizer);

        $this->innerDenormalizer = $this->createMock(DenormalizerInterface::class);
        $this->normalizer->setDenormalizer($this->innerDenormalizer);
    }

    public function testDenormalize(): void
    {
        $expectedModificationDate = new DateTime('1970-01-01T00:00:00+00:00');
        $expectedPublicationDate = new DateTime('1970-01-01T00:00:00+00:00');
        $expectedSection = new Section(1, 'foo');

        $this->innerDenormalizer
            ->expects(self::exactly(3))
            ->method('denormalize')
            ->willReturnCallback(static function ($data) use ($expectedModificationDate, $expectedPublicationDate, $expectedSection): ?object {
                if ($data === null) {
                    return null;
                }

                if ($data === 'foo_modification_date') {
                    return $expectedModificationDate;
                }

                if ($data === 'foo_publication_date') {
                    return $expectedPublicationDate;
                }

                if ($data === 'foo_section') {
                    return $expectedSection;
                }

                self::fail(sprintf(
                    'Unexpected call to inner denormalizer. Expected one of "%s", received %s',
                    implode('", "', ['foo_modification_date', 'foo_publication_date', 'foo_section']),
                    is_object($data) ? get_class($data) : (string) $data,
                ));
            })
        ;

        $metadata = $this->normalizer->denormalize([
            'contentType' => 'foo_content_type',
            'mainTranslation' => 'foo_main_translation',
        ], CreateMetadata::class);

        self::assertSame('foo_content_type', $metadata->contentType);
        self::assertSame('foo_main_translation', $metadata->mainTranslation);
        self::assertNull($metadata->creatorId);
        self::assertNull($metadata->modificationDate);
        self::assertNull($metadata->publicationDate);
        self::assertNull($metadata->remoteId);
        self::assertNull($metadata->alwaysAvailable);
        self::assertNull($metadata->section);

        $metadata = $this->normalizer->denormalize([
            'contentType' => 'foo_content_type',
            'mainTranslation' => 'foo_main_translation',
            'creatorId' => 1,
            'modificationDate' => 'foo_modification_date',
            'publicationDate' => 'foo_publication_date',
            'remoteId' => 'foo_remote_id',
            'alwaysAvailable' => true,
            'section' => 'foo_section',
        ], CreateMetadata::class);

        self::assertSame('foo_content_type', $metadata->contentType);
        self::assertSame('foo_main_translation', $metadata->mainTranslation);
        self::assertSame(1, $metadata->creatorId);
        self::assertSame($expectedModificationDate, $metadata->modificationDate);
        self::assertSame($expectedPublicationDate, $metadata->publicationDate);
        self::assertSame('foo_remote_id', $metadata->remoteId);
        self::assertTrue($metadata->alwaysAvailable);
        self::assertSame($expectedSection, $metadata->section);
    }

    public function testNormalize(): void
    {
        $modificationDate = new DateTime();
        $publicationDate = new DateTime();
        $section = new Section(1, 'foo_section');

        $this->innerNormalizer
            ->expects(self::exactly(6))
            ->method('normalize')
            ->willReturnCallback(static function ($data) use ($modificationDate, $publicationDate, $section): ?string {
                if ($data === null) {
                    return null;
                }

                if ($data === $modificationDate) {
                    return 'foo_modification_date';
                }

                if ($data === $publicationDate) {
                    return 'foo_publication_date';
                }

                if ($data === $section) {
                    return 'foo_section';
                }

                self::fail('Unexpected call to inner normalizer.');
            });

        $metadata = new CreateMetadata('foo_content_type', 'foo_main_translation');
        $result = $this->normalizer->normalize($metadata);
        self::assertSame([
            'contentType' => 'foo_content_type',
            'mainTranslation' => 'foo_main_translation',
            'creatorId' => null,
            'modificationDate' => null,
            'publicationDate' => null,
            'remoteId' => null,
            'alwaysAvailable' => null,
            'section' => null,
        ], $result);

        $metadata = new CreateMetadata(
            'foo_content_type',
            'foo_main_translation',
            1,
            $modificationDate,
            $publicationDate,
            'foo_remote_id',
            true,
            $section,
        );

        $result = $this->normalizer->normalize($metadata);
        self::assertSame([
            'contentType' => 'foo_content_type',
            'mainTranslation' => 'foo_main_translation',
            'creatorId' => 1,
            'modificationDate' => 'foo_modification_date',
            'publicationDate' => 'foo_publication_date',
            'remoteId' => 'foo_remote_id',
            'alwaysAvailable' => true,
            'section' => 'foo_section',
        ], $result);
    }

    public function testSupportsDenormalization(): void
    {
        self::assertTrue($this->normalizer->supportsDenormalization(null, CreateMetadata::class));
        self::assertFalse($this->normalizer->supportsDenormalization(null, stdClass::class));
    }

    public function testSupportsNormalization(): void
    {
        self::assertTrue($this->normalizer->supportsNormalization(
            new CreateMetadata('foo_content_type', 'foo_main_translation')
        ));
        self::assertFalse($this->normalizer->supportsNormalization(new stdClass()));
    }
}
