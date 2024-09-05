<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\Type\Extension\EventSubscriber;

use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;
use Ibexa\Taxonomy\Form\Type\Extension\EventSubscriber\TaxonomyParentSubscriber;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class TaxonomyParentSubscriberTest extends TestCase
{
    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    /** @var \Symfony\Component\HttpFoundation\RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    private RequestStack $requestStack;

    private TaxonomyParentSubscriber $eventSubscriber;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->eventSubscriber = new TaxonomyParentSubscriber(
            $this->taxonomyConfiguration,
            $this->taxonomyService,
            $this->requestStack
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            ['form.pre_set_data' => 'onPreSetData'],
            TaxonomyParentSubscriber::getSubscribedEvents()
        );
    }

    public function testOnPreSetData(): void
    {
        $parentTaxonomyEntry = $this->createTaxonomyEntry(1, 'foo', 'Foo');

        $value = $this->createMock(Value::class);
        $value
            ->expects(self::atLeastOnce())
            ->method('setTaxonomyEntry')
            ->with($parentTaxonomyEntry);

        $contentType = new ContentType();
        $contentCreateData = new ContentCreateData([
            'contentType' => $contentType,
            'fieldsData' => [
                'field_parent' => new FieldData(['value' => $value]),
            ],
        ]);

        $this->mockTaxonomyConfigurationMethods($contentType);

        $request = new Request(['taxonomyParent' => 5]);

        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        $this->taxonomyService
            ->method('loadEntryById')
            ->with(5)
            ->willReturn($parentTaxonomyEntry);

        $formEvent = $this->createFormEventMock($contentCreateData);

        $this->eventSubscriber->onPreSetData($formEvent);
    }

    /**
     * @param \Ibexa\ContentForms\Data\Content\ContentUpdateData|\Ibexa\ContentForms\Data\Content\ContentCreateData $data
     *
     * @return \Symfony\Component\Form\FormEvent|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createFormEventMock($data): FormEvent
    {
        $formEvent = $this->createMock(FormEvent::class);
        $formEvent->method('getData')->willReturn($data);

        return $formEvent;
    }

    private function createTaxonomyEntry(int $id, string $identifier, string $name): TaxonomyEntry
    {
        return new TaxonomyEntry(
            $id,
            $identifier,
            $name,
            'eng-GB',
            [
                'eng-GB' => $name,
            ],
            null,
            new Content(),
            'tags'
        );
    }

    private function mockTaxonomyConfigurationMethods(ContentType $contentType): void
    {
        $this->taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->with($contentType)
            ->willReturn('tags');

        $this->taxonomyConfiguration
            ->method('getFieldMappings')
            ->with('tags')
            ->willReturn([
                'identifier' => 'field_identifier',
                'name' => 'field_name',
                'parent' => 'field_parent',
            ]);
    }
}
