<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\CompletenessInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @phpstan-template T of TaskInterface
 */
final class RenderProductCompletenessRuntime implements RuntimeExtensionInterface
{
    private const COMPLETENESS_TASK_NAME = '%s_completeness_task';

    private ConfigResolverInterface $configResolver;

    private Environment $environment;

    public function __construct(
        ConfigResolverInterface $configResolver,
        Environment $environment
    ) {
        $this->configResolver = $configResolver;
        $this->environment = $environment;
    }

    /**
     * @param array<mixed> $parameters
     *
     * @throws \Exception
     */
    public function renderCompleteness(
        CompletenessInterface $completeness,
        ProductInterface $product,
        array $parameters = []
    ): string {
        $completenessTaskTemplate = '';
        foreach ($completeness->getIterator() as $task) {
            $completenessTaskTemplate .= $this->renderCompletenessTask(
                $task,
                $product,
                $parameters
            );
        }

        return $completenessTaskTemplate;
    }

    /**
     * @phpstan-param T $task
     *
     * @param array<mixed> $parameters
     */
    private function renderCompletenessTask(
        TaskInterface $task,
        ProductInterface $product,
        array $parameters = []
    ): string {
        $taskIdentifier = $task->getIdentifier();
        $taskName = sprintf(self::COMPLETENESS_TASK_NAME, $taskIdentifier);
        $template = $this->getTaskTemplate($taskIdentifier);
        $template = $this->environment->load($template);

        if (!$template->hasBlock($taskName)) {
            return '';
        }

        $parameters = array_merge(
            [
                'task' => $task,
                'product' => $product,
            ],
            $parameters
        );

        return $template->renderBlock($taskName, $parameters);
    }

    private function getTaskTemplate(string $taskIdentifier): string
    {
        $tasks = $this->getCompletenessTasks();
        $taskTemplateName = sprintf(self::COMPLETENESS_TASK_NAME, $taskIdentifier);

        foreach ($tasks as $task) {
            if (str_contains($task['template'], $taskTemplateName)) {
                return $task['template'];
            }
        }

        throw new ConfigurationException(
            sprintf('Missing completeness task template for "%s"', $taskIdentifier)
        );
    }

    /**
     * @return array<array{template: string, priority: integer}>
     */
    private function getCompletenessTasks(): array
    {
        $tasks = $this->configResolver->getParameter('product_catalog.completeness_tasks');

        usort(
            $tasks,
            static fn (array $a, array $b) => $b['priority'] <=> $a['priority']
        );

        return $tasks;
    }
}
