<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Form\Type\FieldType;

use Ibexa\Seo\Value\SeoTypeValue;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeoFieldType extends AbstractType implements TranslationContainerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', HiddenType::class, [
            'data' => $options['seo_type'],
        ]);

        $builder->add('fields', SeoFieldsCollectionType::class, [
            'seo_type' => $options['seo_type'],
            'seo_fields' => $options['seo_fields'],
            'seo_fields_placeholders' => $options['seo_fields_placeholders'],
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'seo_type',
            'seo_fields',
            'seo_fields_placeholders',
        ]);

        $resolver->setDefaults([
            'data_class' => SeoTypeValue::class,
            'seo_fields' => [],
            'seo_type' => null,
            'seo_fields_placeholders' => null,
        ]);

        $resolver->addAllowedTypes('seo_fields_placeholders', ['null', SeoTypeValue::class]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('type.meta_tags', 'ibexa_seo_types')->setDesc('Meta tags'),
            Message::create('type.meta_tags.field.title', 'ibexa_seo_types')->setDesc('Title'),
            Message::create('type.meta_tags.field.description', 'ibexa_seo_types')->setDesc('Description'),
            Message::create('type.meta_tags.field.keywords', 'ibexa_seo_types')->setDesc('Keywords'),
            Message::create('type.meta_tags.field.canonical', 'ibexa_seo_types')->setDesc('Canonical'),
            Message::create('type.open_graph', 'ibexa_seo_types')->setDesc('OpenGraph'),
            Message::create('type.open_graph.field.og_title', 'ibexa_seo_types')->setDesc('Title'),
            Message::create('type.open_graph.field.og_description', 'ibexa_seo_types')->setDesc('Description'),
            Message::create('type.twitter', 'ibexa_seo_types')->setDesc('Twitter'),
            Message::create('type.twitter.field.twitter_title', 'ibexa_seo_types')->setDesc('Title'),
            Message::create('type.twitter.field.twitter_description', 'ibexa_seo_types')->setDesc('Description'),
        ];
    }
}
