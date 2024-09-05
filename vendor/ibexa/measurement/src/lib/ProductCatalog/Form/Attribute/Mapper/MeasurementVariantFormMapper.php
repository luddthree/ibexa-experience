<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\TagifyType;
use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface;
use Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface;
use Ibexa\Contracts\Measurement\Parser\MeasurementParserInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\DataTransformer\MeasurementTransformer;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

final class MeasurementVariantFormMapper implements VariantFormMapperInterface, TranslationContainerInterface
{
    public const FORM_MAPPER_HELP_TRANSLATION_KEY = 'measurement.product_catalog.attribute.definition.variant.form_mapper.help';
    public const TRANSLATION_DOMAIN = 'product_catalog';

    private MeasurementParserInterface $measurementParser;

    private MeasurementValueFormatterInterface $measurementFormatter;

    private MeasurementTypeFactoryInterface $measurementTypeFactory;

    public function __construct(
        MeasurementParserInterface $measurementParser,
        MeasurementValueFormatterInterface $measurementFormatter,
        MeasurementTypeFactoryInterface $measurementTypeFactory
    ) {
        $this->measurementParser = $measurementParser;
        $this->measurementFormatter = $measurementFormatter;
        $this->measurementTypeFactory = $measurementTypeFactory;
    }

    /**
     * @param array<string,mixed> $context
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function createForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): void {
        $attributeDefinition = $assignment->getAttributeDefinition();
        $attributeDefinitionOptions = $attributeDefinition->getOptions();
        $type = $this->getType($attributeDefinitionOptions);
        $unitName = $attributeDefinitionOptions->get('unit');
        if (!is_string($unitName)) {
            throw new RuntimeException('Invalid measurement unit name');
        }

        $form = $builder->create($name, TagifyType::class, [
            'label' => $attributeDefinition->getName(),
            'help' => self::FORM_MAPPER_HELP_TRANSLATION_KEY,
            'help_translation_parameters' => [
                '%unit-symbol%' => $type->getUnit($unitName)->getSymbol(),
            ],
            'translation_domain' => self::TRANSLATION_DOMAIN,
        ]);

        $form->addModelTransformer($this->createDataTransformer($type));

        $builder->add($form);
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                self::FORM_MAPPER_HELP_TRANSLATION_KEY,
                self::TRANSLATION_DOMAIN
            )->setDesc('Provide values as digits and %unit-symbol% unit.'),
        ];
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer<
     *     \Ibexa\Contracts\Measurement\Value\ValueInterface|null
     * >
     */
    private function createDataTransformer(MeasurementInterface $measurementType): StringToArrayTransformer
    {
        $innerTransformer = new MeasurementTransformer(
            $this->measurementParser,
            $this->measurementFormatter,
            $measurementType
        );

        return new StringToArrayTransformer($innerTransformer);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function getType(OptionsBag $attributeDefinitionOptions): MeasurementInterface
    {
        $type = $attributeDefinitionOptions->get('type');
        if (!is_string($type)) {
            throw new RuntimeException('Invalid measurement type');
        }

        return $this->measurementTypeFactory->buildType($type);
    }
}
