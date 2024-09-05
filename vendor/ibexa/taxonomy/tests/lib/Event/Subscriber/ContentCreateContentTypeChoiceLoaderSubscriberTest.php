<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Event\Subscriber;

use Ibexa\AdminUi\Form\Type\Event\ContentCreateContentTypeChoiceLoaderEvent;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Event\Subscriber\ContentCreateContentTypeChoiceLoaderSubscriber;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Taxonomy\Event\Subscriber\ContentCreateContentTypeChoiceLoaderSubscriber
 */
final class ContentCreateContentTypeChoiceLoaderSubscriberTest extends TestCase
{
    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration&\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    private ContentCreateContentTypeChoiceLoaderSubscriber $choiceLoaderSubscriber;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->choiceLoaderSubscriber = new ContentCreateContentTypeChoiceLoaderSubscriber(
            $this->taxonomyConfiguration,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                ContentCreateContentTypeChoiceLoaderEvent::RESOLVE_CONTENT_TYPES => 'removeTaxonomyContentTypes',
            ],
            ContentCreateContentTypeChoiceLoaderSubscriber::getSubscribedEvents()
        );
    }

    public function testRemoveTaxonomyContentTypes(): void
    {
        $contentTypeGroups = ['Content' => [new ContentType(['id' => 1]), new ContentType(['id' => 2])]];
        $event = new ContentCreateContentTypeChoiceLoaderEvent($contentTypeGroups, null);

        $this->taxonomyConfiguration->expects(self::exactly(2))
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturnCallback(static function (ContentType $contentType): bool {
                return $contentType->id === 1;
            });
        $this->choiceLoaderSubscriber->removeTaxonomyContentTypes($event);

        self::assertCount(1, $event->getContentTypeGroups()['Content']);
    }
}
