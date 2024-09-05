<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\Type\Extension;

use Ibexa\AdminUi\Form\Type\ContentType\FieldDefinitionsCollectionType;
use Ibexa\Taxonomy\Form\Type\Extension\EventSubscriber\DisableFieldDefinitionFieldsSubscriber;
use Ibexa\Taxonomy\Form\Type\Extension\FieldDefinitionsCollectionTypeExtension;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

final class FieldDefinitionsCollectionTypeExtensionTest extends TestCase
{
    private FieldDefinitionsCollectionTypeExtension $extension;

    private TaxonomyConfiguration $taxonomyConfiguration;

    private DisableFieldDefinitionFieldsSubscriber $eventSubscriber;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);
        $this->eventSubscriber = new DisableFieldDefinitionFieldsSubscriber($this->taxonomyConfiguration);
        $this->extension = new FieldDefinitionsCollectionTypeExtension($this->taxonomyConfiguration);
    }

    public function testGetExtendedTypes(): void
    {
        self::assertEquals(
            [FieldDefinitionsCollectionType::class],
            FieldDefinitionsCollectionTypeExtension::getExtendedTypes()
        );
    }

    public function testBuildForm(): void
    {
        $children = [
            $this->createChildForm(),
            $this->createChildForm(),
            $this->createChildForm(),
        ];

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('all')->willReturn($children);

        $this->extension->buildForm($builder, []);
    }

    /**
     * @return \Symfony\Component\Form\FormBuilderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createChildForm(): FormBuilderInterface
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->expects(self::atLeastOnce())
            ->method('addEventSubscriber')
            ->with($this->eventSubscriber)
        ;

        return $formBuilder;
    }
}
