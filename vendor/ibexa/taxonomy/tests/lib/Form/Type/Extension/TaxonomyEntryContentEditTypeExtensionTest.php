<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\Type\Extension;

use Ibexa\ContentForms\Data\Content\ContentCreateData;
use Ibexa\ContentForms\Form\Type\Content\ContentEditType;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Form\Type\Extension\EventSubscriber\TaxonomyParentSubscriber;
use Ibexa\Taxonomy\Form\Type\Extension\TaxonomyEntryContentEditTypeExtension;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;

final class TaxonomyEntryContentEditTypeExtensionTest extends TestCase
{
    private TaxonomyEntryContentEditTypeExtension $extension;

    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    /** @var \Symfony\Component\HttpFoundation\RequestStack|\PHPUnit\Framework\MockObject\MockObject */
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->extension = new TaxonomyEntryContentEditTypeExtension(
            $this->taxonomyConfiguration,
            $this->taxonomyService,
            $this->requestStack
        );
    }

    public function testGetExtendedTypes(): void
    {
        self::assertEquals(
            [ContentEditType::class],
            TaxonomyEntryContentEditTypeExtension::getExtendedTypes()
        );
    }

    public function testBuildFormWithNoContentType(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('getData')->willReturn(null);

        $builder->expects(self::never())->method('addEventSubscriber');

        $this->extension->buildForm($builder, []);
    }

    public function testBuildFormWithNonTaxonomyContentType(): void
    {
        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturn(false)
        ;

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('getData')->willReturn(
            new ContentCreateData(['contentType' => new ContentType()])
        );

        $builder->expects(self::never())->method('addEventSubscriber');

        $this->extension->buildForm($builder, []);
    }

    public function testBuildFormWithTaxonomyContentType(): void
    {
        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturn(true)
        ;

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('getData')->willReturn(
            new ContentCreateData(['contentType' => new ContentType()])
        );

        $builder
            ->expects(self::once())
            ->method('addEventSubscriber')
            ->with(
                new TaxonomyParentSubscriber(
                    $this->taxonomyConfiguration,
                    $this->taxonomyService,
                    $this->requestStack,
                )
            )
        ;

        $this->extension->buildForm($builder, []);
    }

    public function testFinishViewWithNoContentType(): void
    {
        $view = $this->createMock(FormView::class);
        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn(null);

        $view->expects(self::never())->method('offsetGet');

        $this->extension->finishView($view, $form, []);
    }

    public function testFinishViewWithNonTaxonomyContentType(): void
    {
        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturn(false)
        ;

        $view = $this->createMock(FormView::class);
        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn(
            new ContentCreateData(['contentType' => new ContentType()])
        );

        $view->expects(self::never())->method('offsetGet');

        $this->extension->finishView($view, $form, []);
    }

    public function testFinishViewWithTaxonomyContentType(): void
    {
        $contentType = new ContentType();

        $this->taxonomyConfiguration
            ->method('isContentTypeAssociatedWithTaxonomy')
            ->willReturn(true)
        ;

        $this->taxonomyConfiguration
            ->method('getTaxonomyForContentType')
            ->with($contentType)
            ->willReturn('tags')
        ;

        $this->taxonomyConfiguration
            ->method('getFieldMappings')
            ->with('tags')
            ->willReturn([
                'identifier' => 'field_identifier',
                'name' => 'field_name',
                'parent' => 'field_parent',
            ])
        ;

        $view = $this->createContentFormView();
        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn(
            new ContentCreateData(['contentType' => $contentType])
        );

        $this->extension->finishView($view, $form, []);

        self::assertArrayHasKey(
            'data-autogenerate-identifier-target-selector',
            $view['fieldsData']['field_name']['value']->vars['attr'] /** @phpstan-ignore-line */
        );
        self::assertEquals(
            '#foobar',
            $view['fieldsData']['field_name']['value']->vars['attr']['data-autogenerate-identifier-target-selector'] /** @phpstan-ignore-line */
        );
    }

    protected function createContentFormView(): FormView
    {
        $identifierFormView = new FormView();
        $identifierFormView->vars = [
            'id' => 'foobar',
        ];

        $fieldsDataFormView = $this->createFormViewWithChildren([
            'field_identifier' => $this->createFormViewWithChildren(['value' => $identifierFormView]),
            'field_name' => $this->createFormViewWithChildren(['value' => new FormView()]),
        ]);

        return $this->createFormViewWithChildren(['fieldsData' => $fieldsDataFormView]);
    }

    /**
     * @param array<string, \Symfony\Component\Form\FormView> $children
     */
    private function createFormViewWithChildren(array $children): FormView
    {
        $formView = new FormView();
        $formView->children = $children;

        return $formView;
    }
}
