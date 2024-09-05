<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Extension;

use Ibexa\AdminUi\Form\Type\ContentType\FieldDefinitionsCollectionType;
use Ibexa\Taxonomy\Form\Type\Extension\EventSubscriber\DisableFieldDefinitionFieldsSubscriber;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extension for Field Definition list in content type editing form.
 *
 * Hooks up event subscriber used to enforce taxonomy fields safety.
 */
class FieldDefinitionsCollectionTypeExtension extends AbstractTypeExtension
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $subscriber = new DisableFieldDefinitionFieldsSubscriber($this->taxonomyConfiguration);
        foreach ($builder->all() as $fieldTypeGroup) {
            $fieldTypeGroup->addEventSubscriber($subscriber);
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [FieldDefinitionsCollectionType::class];
    }
}
