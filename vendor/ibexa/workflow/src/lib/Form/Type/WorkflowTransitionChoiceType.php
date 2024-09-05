<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Form\Type;

use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Workflow\Form\ChoiceLoader\WorkflowTransitionChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkflowTransitionChoiceType extends AbstractType
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface */
    private $workflowDefinitionMetadataRegistry;

    public function __construct(
        WorkflowRegistryInterface $workflowRegistry,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'required' => false,
            'choice_loader' => new WorkflowTransitionChoiceLoader($this->workflowRegistry, $this->workflowDefinitionMetadataRegistry),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

class_alias(WorkflowTransitionChoiceType::class, 'EzSystems\EzPlatformWorkflow\Form\Type\WorkflowTransitionChoiceType');
