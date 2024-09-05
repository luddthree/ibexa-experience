<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\AdminUi\Menu\ContentCreateRightSidebarBuilder;
use Ibexa\AdminUi\Menu\ContentEditRightSidebarBuilder;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Workflow;

abstract class AbstractMenuSubscriber implements EventSubscriberInterface
{
    protected const PUBLISH_MENU_ITEMS = [
        ContentCreateRightSidebarBuilder::ITEM__PUBLISH,
        ContentEditRightSidebarBuilder::ITEM__PUBLISH,
    ];

    /** @var \Knp\Menu\FactoryInterface */
    private $menuFactory;

    /** @var \Ibexa\Contracts\Workflow\Registry\WorkflowRegistryInterface */
    private $workflowRegistry;

    /** @var \Ibexa\Workflow\Factory\Registry\WorkflowDefinitionMetadataRegistryFactory */
    private $workflowDefinitionMetadataRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        FactoryInterface $menuFactory,
        WorkflowRegistryInterface $workflowRegistry,
        WorkflowDefinitionMetadataRegistryInterface $workflowDefinitionMetadataRegistry,
        PermissionResolver $permissionResolver
    ) {
        $this->menuFactory = $menuFactory;
        $this->workflowRegistry = $workflowRegistry;
        $this->workflowDefinitionMetadataRegistry = $workflowDefinitionMetadataRegistry;
        $this->permissionResolver = $permissionResolver;
    }

    private function getWorkflows(ValueObject $subject): array
    {
        $workflows = $this->workflowRegistry->getSupportedWorkflows($subject);

        return $this->filterWorkflows($workflows, $subject);
    }

    public function addTransitionButtonsToContentEdit(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();

        $options = $event->getOptions();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];

        $workflows = $this->getWorkflows($content);

        $workflowMenuItems = [];

        foreach ($workflows as $workflow) {
            $workflowMenuItems = array_replace(
                $workflowMenuItems,
                $this->createWorkflowMenuItems($workflow, $workflow->getEnabledTransitions($content), $options)
            );
        }

        $root->setChildren(array_replace($workflowMenuItems, $root->getChildren()));
    }

    public function addTransitionButtonsToContentCreate(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();

        $options = $event->getOptions();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct */
        $contentCreateStruct = $options['content_create_struct'];

        $workflows = $this->getWorkflows($contentCreateStruct);

        $workflowMenuItems = [];

        foreach ($workflows as $workflow) {
            $workflowMenuItems = array_replace(
                $workflowMenuItems,
                $this->createWorkflowMenuItems($workflow, $workflow->getEnabledTransitions($contentCreateStruct), $options)
            );
        }

        $root->setChildren(array_replace($workflowMenuItems, $root->getChildren()));
    }

    private function filterWorkflows($workflows, ValueObject $subject): array
    {
        $workflows = array_filter($workflows, static function (Workflow $workflow) use ($subject) {
            try {
                $workflow->getMarking($subject);

                return true;
            } catch (LogicException $e) {
                return false;
            }
        });

        // quick_review works as fallback so we remove it when there are other workflows
        if (count($workflows) > 1) {
            unset($workflows['quick_review']);
        }

        return $workflows;
    }

    public function removePublishButtonsOnContentEdit(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();

        $options = $event->getOptions();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];

        $canPublish = $this->permissionResolver->canUser('content', 'publish', $content);
        if ($canPublish) {
            return;
        }

        $workflows = $this->workflowRegistry->getSupportedWorkflows($content);
        if (!$this->hasEnabledTransitions($workflows, $content)) {
            return;
        }

        $this->removePublishMenuItems($root);
    }

    public function removePublishButtonsOnContentCreate(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();

        $options = $event->getOptions();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct */
        $contentCreateStruct = $options['content_create_struct'];

        $canPublish = $this->permissionResolver->canUser('content', 'publish', $contentCreateStruct);
        if ($canPublish) {
            return;
        }

        $workflows = $this->workflowRegistry->getSupportedWorkflows($contentCreateStruct);
        if (!$this->hasEnabledTransitions($workflows, $contentCreateStruct)) {
            return;
        }

        $this->removePublishMenuItems($root);
    }

    private function createWorkflowMenuItems(Workflow $workflow, array $transitions, array $options): array
    {
        $workflowMetadataRegistry = $this->workflowDefinitionMetadataRegistry->getWorkflowMetadata($workflow->getName());
        $workflowTransitionsMetadata = $workflowMetadataRegistry->getTransitionsMetadata();

        $menuItems = [];
        $orderNumber = 1;

        foreach ($transitions as $transition) {
            $transitionMetadata = $workflowTransitionsMetadata[$transition->getName()];
            $transitionLabel = $transitionMetadata->getLabel();
            $transitionValidate = $transitionMetadata->getValidate();

            $menuItemKey = sprintf('workflow__apply__%s__%s', $workflow->getName(), $transition->getName());
            $menuItemParameters = [
                'label' => $transitionLabel,
                'attributes' => [
                    'class' => 'ibexa-btn--extra-actions',
                    'data-actions' => $menuItemKey,
                    'data-validate' => $transitionValidate,
                    'data-focus-element' => '.ibexa-workflow-apply-transition__user-input',
                ],
                'extras' => [
                    'translation_domain' => 'ibexa_workflow',
                    'template' => '@ibexadesign/ibexa_workflow/apply_transition_widget.html.twig',
                    'template_parameters' => [
                        'action' => $menuItemKey,
                        'workflow' => $workflow->getName(),
                        'transition' => $transition->getName(),
                        'reviewer' => [
                            'required' => $transitionMetadata->getReviewersMetadata()->isRequired(),
                        ],
                        'context' => [
                            'action' => isset($options['content']) ? 'edit' : 'create',
                            'options' => $options,
                        ],
                    ],
                    'orderNumber' => $orderNumber++,
                ],
            ];

            if ($transitionMetadata->getIcon()) {
                $menuItemParameters['extras']['icon_path'] = $transitionMetadata->getIcon();
            } else {
                $menuItemParameters['extras']['icon'] = 'review';
            }

            $menuItems[$menuItemKey] = $this->menuFactory->createItem(
                $menuItemKey,
                $menuItemParameters
            );
        }

        return $menuItems;
    }

    private function hasEnabledTransitions(array $workflows, $subject): bool
    {
        $enabledTransitions = [];
        foreach ($workflows as $workflow) {
            $enabledTransitions = array_merge($enabledTransitions, $workflow->getEnabledTransitions($subject));
        }

        return !empty($enabledTransitions);
    }

    private function removePublishMenuItems(ItemInterface $root): void
    {
        foreach (self::PUBLISH_MENU_ITEMS as $name) {
            $root->removeChild($name);
        }
    }
}

class_alias(AbstractMenuSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\AbstractMenuSubscriber');
