<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type;

use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyType extends AbstractType
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(TaxonomyConfiguration $taxonomyConfiguration)
    {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => false,
            'multiple' => false,
            'choice_loader' => ChoiceList::lazy($this, function () {
                return $this->getChoices();
            }),
            'choice_label' => static function (string $choice, string $key, string $value): string {
                return sprintf('taxonomy.%s', $value);
            },
            'choice_translation_domain' => 'ibexa_taxonomy',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function getChoices(): array
    {
        $taxonomies = $this->taxonomyConfiguration->getTaxonomies();

        return array_combine($taxonomies, $taxonomies);
    }
}
