<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Extension;

use Ibexa\Taxonomy\Form\Type\TaxonomyEntryFieldType;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ParentTaxonomyEntryFieldTypeExtension extends AbstractTypeExtension
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(TaxonomyConfiguration $taxonomyConfiguration)
    {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getExtendedTypes(): iterable
    {
        return [TaxonomyEntryFieldType::class];
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!isset($options['field_definition'])) {
            return;
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        $fieldDefinition = $options['field_definition'];

        $taxonomy = $options['taxonomy'];
        $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);

        if ($fieldMappings['parent'] !== $fieldDefinition->identifier) {
            return;
        }

        $view->vars['attr']['data-is-parent'] = true;
    }
}
