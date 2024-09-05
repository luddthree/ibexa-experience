<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Type\Block;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Service\BlockServiceInterface;
use Ibexa\PageBuilder\Data\Block\BlockConfiguration;
use Ibexa\PageBuilder\Form\Type\RevealDateTimeType;
use Ibexa\PageBuilder\Form\Type\TillDateTimeType;
use Ibexa\PageBuilder\Form\Validator\ContainsScss;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlockConfigurationType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('type', HiddenType::class)
            ->add('name', TextType::class, [
                'label' => /** @Desc("Name") */
                    'page_builder.block_configuration.name',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => BlockServiceInterface::BLOCK_NAME_MAX_LENGTH]),
                ],
            ])
            ->add('class', TextType::class, [
                'required' => false,
                'label' => /** @Desc("Class") */
                    'page_builder.block_configuration.class',
            ])
            ->add('style', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new ContainsScss(),
                ],
                'label' => /** @Desc("Style") */
                    'page_builder.block_configuration.style',
            ])
            ->add('since', RevealDateTimeType::class, [
                'required' => false,
                'label' => /** @Desc("Reveal") */
                    'page_builder.block_configuration.reveal',
            ])
            ->add('till', TillDateTimeType::class, [
                'required' => false,
                'label' => /** @Desc("Hide") */
                    'page_builder.block_configuration.hide',
            ])
            ->add('view', ChoiceType::class, [
                'choices' => $this->getViewChoices($options['block_type']),
                'multiple' => false,
                'expanded' => false,
                'label' => /** @Desc("View") */
                    'page_builder.block_configuration.view',
                'attr' => [
                    'class' => 'ibexa-block-configuration-view-select',
                ],
            ])
            ->add('attributes', AttributesType::class, [
                'label' => false,
                'block_type' => $options['block_type'],
                'language_code' => $options['language_code'],
            ])
            ->add('configure', SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['language_code'] === null) {
            @trigger_error('Using ' . self::class . ' without language option is deprecated since ezplatform-page-builder 1.2 and will be not possible in 2.0.', E_USER_DEPRECATED);
        }

        $view->vars += [
            'language_code' => $options['language_code'],
        ];
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     *
     * @return array
     */
    private function getViewChoices(BlockDefinition $blockDefinition)
    {
        $views = [];
        foreach ($blockDefinition->getViews() as $key => $view) {
            $views[$view['name']] = $key;
        }

        return $views;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => BlockConfiguration::class,
                'translation_domain' => 'ibexa_page_builder_block_config',
                'language_code' => null,
            ])
            ->setRequired(['block_type'])
            ->setAllowedTypes('block_type', BlockDefinition::class)
        ;
    }
}

class_alias(BlockConfigurationType::class, 'EzSystems\EzPlatformPageBuilder\Form\Type\Block\BlockConfigurationType');
