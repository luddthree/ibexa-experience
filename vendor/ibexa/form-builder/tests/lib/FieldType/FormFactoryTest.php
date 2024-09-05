<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form as FormDefinition;
use Ibexa\FormBuilder\Exception\FieldMapperNotFoundException;
use Ibexa\FormBuilder\FieldType\Field\FieldMapperRegistry;
use Ibexa\FormBuilder\FieldType\FormFactory;
use Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperRegistry;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormFactoryTest extends TestCase
{
    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var \Symfony\Component\Form\FormFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $formFactory;

    /** @var \Ibexa\FormBuilder\FieldType\Field\FieldMapperRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $fieldMapperRegistry;

    /** @var \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $validatorConstraintMapperRegistry;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcherInterface;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->fieldMapperRegistry = $this->createMock(FieldMapperRegistry::class);
        $this->validatorConstraintMapperRegistry = $this->createMock(ValidatorConstraintMapperRegistry::class);
        $this->eventDispatcherInterface = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @dataProvider dataProviderForCreateForm
     *
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $definition
     * @param $data
     * @param string $namePrefix
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     */
    public function testCreateForm(FormDefinition $definition, $data, string $namePrefix): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $expectedForm = $this->createMock(FormInterface::class);
        $fieldBuilder = $this->createMock(FormBuilderInterface::class);

        $this->formFactory
            ->expects($this->once())
            ->method('createNamedBuilder')
            ->with('test_form---', FormType::class, $data, [
                'compound' => true,
                'csrf_protection' => false,
            ])
            ->willReturn($formBuilder);

        $formBuilder
            ->expects($this->once())
            ->method('create')
            ->with('fields', FormType::class, [
                'compound' => true,
            ])
            ->willReturn($fieldBuilder);

        foreach ($definition->getFields() as $i => $field) {
            $fieldMapper = $this->createMock(FieldMapperInterface::class);

            $this->fieldMapperRegistry
                ->expects($this->at($i))
                ->method('getMapper')
                ->with($field->getIdentifier())
                ->willReturn($fieldMapper);

            $fieldMapper
                ->expects($this->once())
                ->method('mapField')
                ->with($fieldBuilder, $field);

            $formBuilder
                ->expects($this->once())
                ->method('add')
                ->with($formBuilder);
        }

        $formBuilder
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($expectedForm);

        $factory = new FormFactory(
            $this->formFactory,
            $this->fieldMapperRegistry,
            $this->validatorConstraintMapperRegistry,
            $this->eventDispatcherInterface
        );
        $factory->setLogger($this->logger);

        $this->assertEquals($expectedForm, $factory->createForm($definition, $data, $namePrefix));
    }

    /**
     * @dataProvider dataProviderForCreateForm
     *
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $definition
     * @param $data
     * @param string $namePrefix
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     */
    public function testCreateFormLogMissingFieldMapper(FormDefinition $definition, $data, string $namePrefix): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $expectedForm = $this->createMock(FormInterface::class);

        $this->formFactory
            ->expects($this->once())
            ->method('createNamedBuilder')
            ->with('test_form---', FormType::class, $data, [
                'compound' => true,
                'csrf_protection' => false,
            ])
            ->willReturn($formBuilder);

        $formBuilder
            ->expects($this->once())
            ->method('create')
            ->with('fields', FormType::class, [
                'compound' => true,
            ]);

        foreach ($definition->getFields() as $i => $field) {
            $exception = new FieldMapperNotFoundException($field->getIdentifier());

            $this->fieldMapperRegistry
                ->expects($this->at($i))
                ->method('getMapper')
                ->with($field->getIdentifier())
                ->willThrowException($exception);

            $this->logger
                ->expects($this->at($i))
                ->method('error')
                ->with($exception->getMessage());
        }

        $formBuilder
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($expectedForm);

        $factory = new FormFactory(
            $this->formFactory,
            $this->fieldMapperRegistry,
            $this->validatorConstraintMapperRegistry,
            $this->eventDispatcherInterface
        );
        $factory->setLogger($this->logger);

        $this->assertEquals($expectedForm, $factory->createForm($definition, $data, $namePrefix));
    }

    /**
     * @return array
     */
    public function dataProviderForCreateForm(): array
    {
        return [
            [
                new FormDefinition(null, null, null, [
                    new Field('1', 'single_line'),
                    new Field('2', 'single_line'),
                    new Field('3', 'multi_line'),
                ]),
                [
                    'languageCode' => null,
                    'contentId' => null,
                    'contentFieldId' => null,
                ],
                'test_form',
            ],
        ];
    }
}

class_alias(FormFactoryTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\FormFactoryTest');
