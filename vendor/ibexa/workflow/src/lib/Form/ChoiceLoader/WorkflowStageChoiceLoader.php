<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Form\ChoiceLoader;

use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class WorkflowStageChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /**
     * The loaded choice list.
     *
     * @var \Symfony\Component\Form\ChoiceList\ArrayChoiceList
     */
    private $choiceList;

    /**
     * @param \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface $workflowRegistry
     */
    public function __construct(WorkflowRegistryInterface $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if (null !== $this->choiceList) {
            return $this->choiceList;
        }

        $choices = [];
        foreach ($this->workflowRegistry->getWorkflows() as $workflow) {
            foreach ($workflow->getDefinition()->getPlaces() as $place) {
                $workflowName = $workflow->getName();
                if (!array_key_exists($workflowName, $choices)) {
                    $choices[$workflowName] = [];
                }

                $label = $this->humanizeName($workflowName) . ':' . $this->humanizeName($place);
                $choices[$workflowName][$label] = "{$workflowName}:{$place}";
            }
        }

        $this->choiceList = new ArrayChoiceList($choices);

        return $this->choiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }

    private function humanizeName(string $name): string
    {
        return ucwords(str_replace('_', ' ', $name));
    }
}

class_alias(WorkflowStageChoiceLoader::class, 'EzSystems\EzPlatformWorkflow\Form\ChoiceLoader\WorkflowStageChoiceLoader');
