<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Security\Limitation\Mapper;

use Ibexa\AdminUi\Limitation\LimitationFormMapperInterface;
use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use Ibexa\Workflow\Form\Type\WorkflowStageChoiceType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\FormInterface;

class WorkflowStageLimitationMapper implements LimitationValueMapperInterface, LimitationFormMapperInterface, TranslationContainerInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    /** @var string */
    private $template;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
     */
    public function __construct(
        WorkflowRegistryInterface $workflowRegistry,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
        $form->add(
            'limitationValues',
            WorkflowStageChoiceType::class,
            ['label' => LimitationIdentifierToLabelConverter::convert($data->getIdentifier())]
        );
    }

    /**
     * @param string $template
     */
    public function setFormTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormTemplate(): string
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLimitationValues(Limitation $limitation)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        $values = [];
        foreach ($limitation->limitationValues as $limitationValue) {
            list($workflowName, $stageName) = explode(':', $limitationValue);

            $workflow = $this->workflowRegistry->getWorkflow($workflowName);
            $definitionMetadata = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata($workflowName);

            $values[] = [
                'workflow' => $workflow,
                'stage' => [
                    'name' => $stageName,
                    'color' => $definitionMetadata->getStageMetadata($stageName)->getColor(),
                ],
            ];
        }

        return $values;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(LimitationIdentifierToLabelConverter::convert('workflowstage'), 'ibexa_content_forms_policies'))->setDesc('Workflow Stage'),
        ];
    }
}

class_alias(WorkflowStageLimitationMapper::class, 'EzSystems\EzPlatformWorkflow\Security\Limitation\Mapper\WorkflowStageLimitationMapper');
