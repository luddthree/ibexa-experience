<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper;

use Ibexa\Bundle\ProductCatalog\Form\Type\TagifyType;
use Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface;
use Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface;
use Ibexa\Contracts\Measurement\Parser\MeasurementParserInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Measurement\ProductCatalog\AttributeType;
use Ibexa\Measurement\Value\Definition\GenericUnit;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionAssignment;
use JMS\TranslationBundle\Model\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MeasurementVariantFormMapperTest extends TestCase
{
    private const LANGUAGE_CODE = 'eng-GB';

    private MeasurementVariantFormMapper $mapper;

    /** @var \Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private MeasurementTypeFactoryInterface $measurementTypeFactoryMock;

    protected function setUp(): void
    {
        $this->measurementTypeFactoryMock = $this->createMock(MeasurementTypeFactoryInterface::class);
        $this->mapper = new MeasurementVariantFormMapper(
            $this->createMock(MeasurementParserInterface::class),
            $this->createMock(MeasurementValueFormatterInterface::class),
            $this->measurementTypeFactoryMock
        );
    }

    /**
     * @return iterable<
     *     string,
     *     array{
     *          \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface,
     *          \Ibexa\Contracts\Measurement\Value\Definition\UnitInterface
     *     }
     * >
     */
    public function getDataForTestCreateForm(): iterable
    {
        $attributeIdentifier = 'weight';
        yield 'from sample attribute definition' => [
            new AttributeDefinition(
                1,
                $attributeIdentifier,
                new AttributeType($this->createMock(TranslatorInterface::class), 'weight'),
                $this->createMock(AttributeGroupInterface::class),
                ucfirst($attributeIdentifier),
                0,
                [self::LANGUAGE_CODE],
                '',
                [self::LANGUAGE_CODE => ucfirst($attributeIdentifier)],
                [self::LANGUAGE_CODE => ''],
                [
                    'sign' => 'none',
                    'type' => 'mass',
                    'unit' => 'kilogram',
                ]
            ),
            new GenericUnit('kilogram', 'kg'),
        ];
    }

    /**
     * @dataProvider getDataForTestCreateForm
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function testCreateForm(AttributeDefinition $attributeDefinition, UnitInterface $unit): void
    {
        $measurementTypeMock = $this->createMock(MeasurementInterface::class);
        $optionsBag = $attributeDefinition->getOptions();
        $measurementTypeMock->method('getUnit')->with($optionsBag->get('unit'))->willReturn($unit);
        $this->measurementTypeFactoryMock
            ->method('buildType')
            ->with($optionsBag->get('type'))
            ->willReturn($measurementTypeMock)
        ;

        $formName = 'attribute_definition_form';
        $formBuilderMock = $this->createMock(FormBuilderInterface::class);
        $formBuilderMock
            ->expects(self::once())
            ->method('create')
            ->with(
                $formName,
                TagifyType::class,
                [
                    'label' => $attributeDefinition->getName(),
                    'help' => MeasurementVariantFormMapper::FORM_MAPPER_HELP_TRANSLATION_KEY,
                    'help_translation_parameters' => [
                        '%unit-symbol%' => $unit->getSymbol(),
                    ],
                    'translation_domain' => MeasurementVariantFormMapper::TRANSLATION_DOMAIN,
                ],
            )
            ->willReturn($formBuilderMock)
        ;
        $formBuilderMock->expects(self::once())->method('add')->with($formBuilderMock);

        $this->mapper->createForm(
            $formName,
            $formBuilderMock,
            new AttributeDefinitionAssignment(
                $attributeDefinition,
                false,
                true,
            )
        );
    }

    public function testGetTranslationMessages(): void
    {
        $expectedMessage = new Message(
            'measurement.product_catalog.attribute.definition.variant.form_mapper.help',
            'product_catalog'
        );
        $expectedMessage->setDesc('Provide values as digits and %unit-symbol% unit.');
        self::assertEquals(
            [
                $expectedMessage,
            ],
            MeasurementVariantFormMapper::getTranslationMessages()
        );
    }
}
