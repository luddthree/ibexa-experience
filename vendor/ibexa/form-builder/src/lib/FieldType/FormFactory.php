<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Form as FormDefinition;
use Ibexa\FormBuilder\Exception\FieldMapperNotFoundException;
use Ibexa\FormBuilder\FieldType\Field\FieldMapperRegistry;
use Ibexa\FormBuilder\Form\Data\Submission\SubmissionRemoveData;
use Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperRegistry;
use Ibexa\FormBuilder\Form\Type\Submission\SubmissionRemoveType;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;

class FormFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Ibexa\FormBuilder\FieldType\Field\FieldMapperRegistry */
    private $fieldMapperRegistry;

    /** @var \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperRegistry */
    private $validatorConstraintMapperRegistry;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Ibexa\FormBuilder\FieldType\Field\FieldMapperRegistry $fieldMapperRegistry
     * @param \Ibexa\FormBuilder\Form\Mapper\ValidatorConstraintMapperRegistry $validatorConstraintMapperRegistry
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FieldMapperRegistry $fieldMapperRegistry,
        ValidatorConstraintMapperRegistry $validatorConstraintMapperRegistry,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->fieldMapperRegistry = $fieldMapperRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->validatorConstraintMapperRegistry = $validatorConstraintMapperRegistry;
        $this->logger = new NullLogger();
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $definition
     * @param mixed|null $data
     * @param string|null $namePrefix
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     */
    public function createForm(FormDefinition $definition, $data = null, string $namePrefix = null): FormInterface
    {
        $defaultData = [
            'languageCode' => $definition->getLanguageCode(),
            'contentId' => $definition->getContentId(),
            'contentFieldId' => $definition->getContentFieldId(),
        ];

        $data = array_merge($defaultData, $data ?? []);
        $name = implode('-', [$namePrefix ?? 'form'] + $defaultData);

        $builder = $this->formFactory->createNamedBuilder($name, FormType::class, $data, [
            'compound' => true,
            'csrf_protection' => false,
        ]);

        $formFields = $builder->create('fields', FormType::class, [
            'compound' => true,
        ]);

        foreach ($definition->getFields() as $field) {
            $constraints = [];

            /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Validator $validator */
            foreach ($field->getValidators() as $validator) {
                $constraintMapper = $this->validatorConstraintMapperRegistry->getMapperForValidator($validator);

                if (!empty($validator->getValue())) {
                    $constraints[] = $constraintMapper->map($validator);
                }
            }

            try {
                $mapper = $this->fieldMapperRegistry->getMapper($field->getIdentifier());
                $mapper->mapField($formFields, $field, $constraints);
            } catch (FieldMapperNotFoundException $e) {
                $this->logger->error($e->getMessage(), [
                    'exception' => $e,
                ]);
            }
        }

        $builder->add($formFields);

        return $builder->getForm();
    }

    /**
     * @param \Ibexa\FormBuilder\Form\Data\Submission\SubmissionRemoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function removeSubmission(
        SubmissionRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SubmissionRemoveType::class);

        return $this->formFactory->createNamed($name, SubmissionRemoveType::class, $data);
    }
}

class_alias(FormFactory::class, 'EzSystems\EzPlatformFormBuilder\FieldType\FormFactory');
