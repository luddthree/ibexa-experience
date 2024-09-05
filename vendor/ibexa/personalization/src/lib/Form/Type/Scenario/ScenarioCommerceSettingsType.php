<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Event\Subscriber\Form\ScenarioCommerceSettingsSubscriber;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioCommerceSettingsData;
use Ibexa\Personalization\Form\Type\OptionalIntegerType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ScenarioCommerceSettingsType extends AbstractType
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('exclude_top_selling_results', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans(/** @Desc("No top-selling items") */
                    'scenario.commerce_settings.no_top_selling_items',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->add('exclude_cheaper_items', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans(/** @Desc("Item price should equal or higher than the price of the context item") */
                    'scenario.commerce_settings.item_price_should_equal_or_higher_than_the_price_of_the_context_item',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->add('exclude_minimal_item_price', OptionalIntegerType::class, [
                'required' => false,
                'value_label' => $this->translator->trans(/** @Desc("Minimum price of the recommended product") */
                    'scenario.commerce_settings.minimum_price_of_the_recommended_product',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->add('exclude_items_without_price', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans(/** @Desc("Do not recommend if price unknown") */
                    'scenario.commerce_settings.do_not_recommend_if_price_unknown',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->add('exclude_already_purchased', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans(/** @Desc("Do not recommend items the user already purchased") */
                    'scenario.commerce_settings.do_not_recommend_items_the_user_already_purchased',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->addEventSubscriber(new ScenarioCommerceSettingsSubscriber())
        ;

        if ($builder->getOption('is_variant_supported')) {
            $builder->add('exclude_product_variants', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans(/** @Desc("Do not recommend product variants (only base products will be recommended)") */
                    'scenario.commerce_settings.do_not_recommend_product_variants',
                    [],
                    'ibexa_personalization'
                ),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => ScenarioCommerceSettingsData::class,
                'is_variant_supported' => null,
            ])
            ->setAllowedTypes('is_variant_supported', 'bool');
    }
}

class_alias(ScenarioCommerceSettingsType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioCommerceSettingsType');
