<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\TagifyType;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ColorVariantFormMapper implements VariantFormMapperInterface, TranslationContainerInterface
{
    private const FORM_MAPPER_HELP_TRANSLATION_KEY = 'attribute.definition.variant.form_mapper.help';
    private const TRANSLATION_DOMAIN = 'ibexa_product_catalog';

    public function createForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): void {
        $form = $builder->create($name, TagifyType::class, [
            'label' => $assignment->getAttributeDefinition()->getName(),
            'help' => self::FORM_MAPPER_HELP_TRANSLATION_KEY,
            'translation_domain' => self::TRANSLATION_DOMAIN,
        ]);
        $form->addModelTransformer(new StringToArrayTransformer());

        $builder->add($form);
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                self::FORM_MAPPER_HELP_TRANSLATION_KEY,
                self::TRANSLATION_DOMAIN
            )->setDesc('Provide color values as hex codes. For example, #FF0000.'),
        ];
    }
}
