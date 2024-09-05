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
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Ibexa\Core\Limitation\LimitationIdentifierToLabelConverter;
use Ibexa\Workflow\Form\Type\WorkflowTransitionChoiceType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Workflow\Transition;

class WorkflowTransitionLimitationMapper implements LimitationValueMapperInterface, LimitationFormMapperInterface, TranslationContainerInterface
{
    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /** @var string */
    private $template;

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
    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
        $form->add(
            'limitationValues',
            WorkflowTransitionChoiceType::class,
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
            list($workflowName, $transitionName) = explode(':', $limitationValue);

            $workflow = $this->workflowRegistry->getWorkflow($workflowName);

            $matchingTransitions = array_filter(
                $workflow->getDefinition()->getTransitions(),
                static function (Transition $transition) use ($transitionName): bool {
                    return $transition->getName() === $transitionName;
                }
            );

            if (empty($matchingTransitions)) {
                continue;
            }

            $values[] = [
                'workflow' => $workflow,
                'transition' => reset($matchingTransitions),
            ];
        }

        return $values;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                LimitationIdentifierToLabelConverter::convert('workflowtransition'),
                'ibexa_content_forms_policies'
            )->setDesc('Workflow Transition'),
        ];
    }
}

class_alias(WorkflowTransitionLimitationMapper::class, 'EzSystems\EzPlatformWorkflow\Security\Limitation\Mapper\WorkflowTransitionLimitationMapper');
