<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field as APIField;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\StepExecutor\ContentCreateStepExecutor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\Location;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\ContentCreateStepExecutor
 */
final class ContentCreateStepExecutorTest extends AbstractContentStepExecutorTest
{
    /**
     * @dataProvider provideSteps
     */
    public function testHandle(ContentCreateStep $step): void
    {
        self::assertSame(1, self::findContentByIdentifier(self::KNOWN_CONTENT_TYPE_IDENTIFIER)->getTotalCount());
        $found = true;
        try {
            self::getContentService()->loadContentByRemoteId('foo');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);

        $executor = new ContentCreateStepExecutor(
            self::getContentService(),
            self::getContentTypeService(),
            self::getFieldTypeService(),
            self::getLocationService(),
            self::getSectionService(),
            self::getContentActionExecutor(),
        );

        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('content'),
        ]);

        $executor->handle($step);

        self::assertSame(2, self::findContentByIdentifier(self::KNOWN_CONTENT_TYPE_IDENTIFIER)->getTotalCount());
        $content = self::getContentService()->loadContentByRemoteId('foo');
        self::assertInstanceOf(Content::class, $content);
        self::assertSame('Landing Page Name Here', $content->getName());
        self::assertSame(self::KNOWN_CONTENT_TYPE_IDENTIFIER, $content->getContentType()->identifier);
        self::assertTrue($content->contentInfo->alwaysAvailable);
        self::assertSame('eng-GB', $content->contentInfo->mainLanguageCode);
        self::assertTrue($content->contentInfo->published);
        self::assertSame(2, $content->contentInfo->sectionId);
        $mainLocation = $content->contentInfo->getMainLocation();
        self::assertNotNull($mainLocation);
        self::assertSame(9, $mainLocation->sortField);
        self::assertSame(0, $mainLocation->sortOrder);

        // Note: publicationDate is ignored by API
        self::assertNotNull($step->metadata->publicationDate);
        self::assertNotSame(
            $step->metadata->publicationDate->format('U'),
            $content->contentInfo->publishedDate->format('U')
        );
        // Note: modificationDate is ignored by API
        self::assertNotNull($step->metadata->modificationDate);
        self::assertNotSame(
            $step->metadata->modificationDate->format('U'),
            $content->contentInfo->modificationDate->format('U')
        );
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\ContentCreateStep}>
     */
    public function provideSteps(): iterable
    {
        $content = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'remoteId' => 'foo',
                    'mainLanguage' => new Language([
                        'languageCode' => 'eng-GB',
                    ]),
                    'alwaysAvailable' => true,
                    'sectionId' => 2,
                    'section' => new Section([
                        'id' => 2,
                        'identifier' => 'foo_section_identifier',
                    ]),
                    'publishedDate' => new \DateTime('2020-10-30T11:40:09+00:00'),
                    'modificationDate' => new \DateTime('2020-10-30T11:40:09+00:00'),
                    'mainLocation' => new APILocation([
                        'parentLocationId' => 1,
                        'hidden' => true,
                        'sortField' => 9,
                        'sortOrder' => 0,
                        'priority' => 1,
                    ]),
                ]),
            ]),
            'contentType' => new ContentType([
                'identifier' => 'landing_page',
            ]),
            'internalFields' => [
                new APIField([
                    'fieldDefIdentifier' => 'name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'languageCode' => 'eng-GB',
                    'value' => 'Landing Page Name Here',
                ]),
            ],
        ]);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = [];
        foreach ($content->getFields() as $key => $field) {
            $fields[$key] = Field::createFromValueObject($field, 'Landing Page Name Here');
        }

        $mainLocation = $content->contentInfo->getMainLocation();
        self::assertNotNull($mainLocation);
        $location = Location::createFromValueObject($mainLocation);

        yield 'Default ContentCreateStep' => [
            new ContentCreateStep(
                CreateMetadata::createFromContent($content),
                $location,
                $fields,
                [],
                []
            ),
        ];

        $location->hidden = null;
        $location->priority = null;

        yield 'ContentCreateStep with priority & hidden = null' => [
            new ContentCreateStep(
                CreateMetadata::createFromContent($content),
                $location,
                $fields,
                [],
                []
            ),
        ];
    }
}

class_alias(ContentCreateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ContentCreateStepExecutorTest');
