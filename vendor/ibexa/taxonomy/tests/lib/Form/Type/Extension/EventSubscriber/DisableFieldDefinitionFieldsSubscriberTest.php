<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\Type\Extension\EventSubscriber;

use Ibexa\AdminUi\Form\Data\ContentTypeData;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\AdminUi\Form\Type\FieldDefinition\FieldDefinitionType;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Taxonomy\Form\Type\Extension\EventSubscriber\DisableFieldDefinitionFieldsSubscriber;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

final class DisableFieldDefinitionFieldsSubscriberTest extends TestCase
{
    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    private DisableFieldDefinitionFieldsSubscriber $eventSubscriber;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->eventSubscriber = new DisableFieldDefinitionFieldsSubscriber($this->taxonomyConfiguration);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            ['form.pre_set_data' => ['onPreSetData']],
            DisableFieldDefinitionFieldsSubscriber::getSubscribedEvents()
        );
    }

    /**
     * @dataProvider dataProviderForOnPreSetData
     *
     * @param array<string, mixed> $expectedOptions
     */
    public function testOnPreSetData(string $fieldDefinitionIdentifier, array $expectedOptions): void
    {
        $contentTypeDraft = new ContentTypeDraft([
            'innerContentType' => new ContentType([
                'identifier' => 'tag',
            ]),
        ]);
        $fieldDefinitionData = $this->createFieldDefinitionData($contentTypeDraft);

        $this->mockTaxonomyConfigurationMethods($contentTypeDraft);

        $data = [
            $fieldDefinitionIdentifier => $fieldDefinitionData,
        ];

        $childConfig = $this->createChildConfigMock();
        $childForm = $this->createChildFormMock($childConfig, $fieldDefinitionIdentifier);
        $form = $this->createFormMock($childForm, $fieldDefinitionIdentifier, $expectedOptions);
        $formEvent = $this->createFormEventMock($data, $form);

        $this->eventSubscriber->onPreSetData($formEvent);
    }

    /**
     * @return iterable<string, array{0: string, 1: array<string, mixed>}>
     */
    public function dataProviderForOnPreSetData(): iterable
    {
        yield 'mapped field: identifier' => [
            'field_identifier',
            [
                'some_option' => 'foo',
                'disable_identifier_field' => true,
                'disable_required_field' => true,
                'disable_translatable_field' => true,
                'disable_remove' => true,
            ],
        ];
        yield 'mapped field: parent' => [
            'field_parent',
            [
                'some_option' => 'foo',
                'disable_identifier_field' => true,
                'disable_required_field' => true,
                'disable_translatable_field' => true,
                'disable_remove' => true,
            ],
        ];
        yield 'mapped field: name' => [
            'field_name',
            [
                'some_option' => 'foo',
                'disable_identifier_field' => true,
                'disable_required_field' => true,
                'disable_translatable_field' => false,
                'disable_remove' => true,
            ],
        ];
        yield 'non mapped field' => [
            'foo',
            [
                'some_option' => 'foo',
            ],
        ];
    }

    private function createFieldDefinitionData(
        ContentTypeDraft $contentTypeDraft
    ): FieldDefinitionData {
        return new FieldDefinitionData([
            'contentTypeData' => new ContentTypeData([
                'contentTypeDraft' => $contentTypeDraft,
            ]),
        ]);
    }

    private function mockTaxonomyConfigurationMethods(ContentTypeDraft $contentTypeDraft): void
    {
        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->with($contentTypeDraft)
            ->willReturn(true);
        $this->taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->with($contentTypeDraft)
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

    /**
     * @return \Symfony\Component\Form\FormConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createChildConfigMock(): FormConfigInterface
    {
        $childConfig = $this->createMock(FormConfigInterface::class);
        $childConfig->method('getOptions')->willReturn(['some_option' => 'foo']);

        return $childConfig;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createChildFormMock(
        FormConfigInterface $childConfig,
        string $fieldDefinitionIdentifier
    ): FormInterface {
        $childForm = $this->createMock(FormInterface::class);
        $childForm->method('getName')->willReturn($fieldDefinitionIdentifier);
        $childForm->method('getConfig')->willReturn($childConfig);

        return $childForm;
    }

    /**
     * @param array<string, mixed> $expectedOptions
     *
     * @return \Symfony\Component\Form\FormInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createFormMock(
        FormInterface $childForm,
        string $fieldDefinitionIdentifier,
        array $expectedOptions
    ): FormInterface {
        $form = $this->createMock(FormInterface::class);
        $form
            ->method('get')
            ->with($fieldDefinitionIdentifier)
            ->willReturn($childForm);
        $form
            ->expects(self::atLeastOnce())
            ->method('remove')
            ->with($fieldDefinitionIdentifier);
        $form
            ->expects(self::atLeastOnce())
            ->method('add')
            ->with(
                $fieldDefinitionIdentifier,
                FieldDefinitionType::class,
                $expectedOptions
            );

        return $form;
    }

    /**
     * @param array<string|int, \Ibexa\AdminUi\Form\Data\FieldDefinitionData> $data
     *
     * @return \Symfony\Component\Form\FormEvent|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createFormEventMock(array $data, FormInterface $form): FormEvent
    {
        $formEvent = $this->createMock(FormEvent::class);
        $formEvent->method('getData')->willReturn($data);
        $formEvent->method('getForm')->willReturn($form);

        return $formEvent;
    }
}
