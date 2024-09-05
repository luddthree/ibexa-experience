<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Form\Type;

use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Workflow\Form\ChoiceLoader\WorkflowStageChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkflowStageChoiceType extends AbstractType
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     */
    public function __construct(WorkflowRegistryInterface $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'required' => false,
            'choice_loader' => new WorkflowStageChoiceLoader($this->workflowRegistry),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}

class_alias(WorkflowStageChoiceType::class, 'EzSystems\EzPlatformWorkflow\Form\Type\WorkflowStageChoiceType');
