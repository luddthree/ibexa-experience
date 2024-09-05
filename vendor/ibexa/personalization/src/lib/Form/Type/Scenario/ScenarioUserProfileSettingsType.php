<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Event\Subscriber\Form\ScenarioUserSettingsSubscriber;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioUserProfileSettingsData;
use Ibexa\Personalization\Form\Type\OptionalIntegerType;
use Ibexa\Personalization\Form\Type\OptionalTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ScenarioUserProfileSettingsType extends AbstractType
{
    private const HTML_CLASS_BOOST_ITEM = 'boost-item';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('exclude_context_items', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans(/** @Desc("Do not recommend the item currently viewed") */
                    'scenario.user_profile_settings.do_not_recommend_the_item_currently_viewed',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->add('exclude_already_consumed', CheckboxType::class, [
                'required' => false,
                'label' => $this->translator->trans(/** @Desc("Do not recommend items the user already consumed") */
                    'scenario.user_profile_settings.do_not_recommend_items_the_user_already_consumed',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->add('exclude_repeated_recommendations', OptionalIntegerType::class, [
                'required' => false,
                'value_label' => $this->translator->trans(/** @Desc("Max. repeated shows of identical recommendations per session.") */
                    'scenario.user_profile_settings.max_repeated_shows_of_identical_recommendation_per_session',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->add('boost_item', ScenarioBoostItemType::class, [
                'required' => false,
                'data-related-id' => self::HTML_CLASS_BOOST_ITEM,
            ])
            ->add('user_attribute_name', OptionalTextType::class, [
                'required' => false,
                'data-related' => self::HTML_CLASS_BOOST_ITEM,
                'value_label' => $this->translator->trans(/** @Desc("User attribute has different name") */
                    'scenario.user_profile_settings.user_attribute_has_different_name',
                    [],
                    'ibexa_personalization'
                ),
            ])
            ->addEventSubscriber(new ScenarioUserSettingsSubscriber())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioUserProfileSettingsData::class,
        ]);
    }
}

class_alias(ScenarioUserProfileSettingsType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioUserProfileSettingsType');
