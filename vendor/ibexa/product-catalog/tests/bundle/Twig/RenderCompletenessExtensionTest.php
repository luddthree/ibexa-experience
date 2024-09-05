<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Twig;

use ArrayIterator;
use Ibexa\Bundle\ProductCatalog\Twig\RenderProductCompletenessExtension;
use Ibexa\Bundle\ProductCatalog\Twig\RenderProductCompletenessRuntime;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\CompletenessInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Percent;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class RenderCompletenessExtensionTest extends IntegrationTestCase
{
    private const COMPLETENESS_TASKS_PARAMETER = 'product_catalog.completeness_tasks';
    private const ATTRIBUTES_TASK_IDENTIFIER = 'attributes';
    private const AVAILABILITY_TASK_IDENTIFIER = 'availability';
    private const CONTENT_TASK_IDENTIFIER = 'content';

    /**
     * @return \Twig\RuntimeLoader\RuntimeLoaderInterface[]
     */
    public function getRuntimeLoaders(): array
    {
        return [
            new class($this->getConfigResolver(), $this->getEnvironment()) implements RuntimeLoaderInterface {
                private ConfigResolverInterface $configResolver;

                private Environment $environment;

                public function __construct(
                    ConfigResolverInterface $configResolver,
                    Environment $environment
                ) {
                    $this->configResolver = $configResolver;
                    $this->environment = $environment;
                }

                public function load(string $class): ?RuntimeExtensionInterface
                {
                    if ($class === RenderProductCompletenessRuntime::class) {
                        return new RenderProductCompletenessRuntime(
                            $this->configResolver,
                            $this->environment
                        );
                    }

                    return null;
                }
            },
        ];
    }

    public function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/RenderProductCompletenessExtension/';
    }

    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        return [
            new RenderProductCompletenessExtension(),
        ];
    }

    /**
     * @return \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getConfigResolver(): ConfigResolverInterface
    {
        $tasks = [
            [
                'template' => $this->getTemplatePath(self::ATTRIBUTES_TASK_IDENTIFIER . '_completeness_task.html.twig'),
                'priority' => 10,
            ],
            [
                'template' => $this->getTemplatePath(self::AVAILABILITY_TASK_IDENTIFIER . '_completeness_task.html.twig'),
                'priority' => 20,
            ],
            [
                'template' => $this->getTemplatePath(self::CONTENT_TASK_IDENTIFIER . '_completeness_task.html.twig'),
                'priority' => 30,
            ],
        ];

        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->method('getParameter')
            ->with(self::COMPLETENESS_TASKS_PARAMETER)
            ->willReturn($tasks);

        return $configResolver;
    }

    public function getEnvironment(): Environment
    {
        return new Environment(
            new FilesystemLoader($this->getFixturesDir())
        );
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\UI\Completeness\CompletenessInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getCompleteness(): CompletenessInterface
    {
        $percent = new Percent(50.0);

        $attributesTask = $this->createMock(TaskInterface::class);
        $attributesTask->method('getIdentifier')->willReturn(self::ATTRIBUTES_TASK_IDENTIFIER);

        $availabilityTask = $this->createMock(TaskInterface::class);
        $availabilityTask->method('getIdentifier')->willReturn(self::AVAILABILITY_TASK_IDENTIFIER);

        $contentTask = $this->createMock(TaskInterface::class);
        $contentTask->method('getIdentifier')->willReturn(self::CONTENT_TASK_IDENTIFIER);

        $completeness = $this->createMock(CompletenessInterface::class);
        $completeness->method('getValue')->willReturn($percent);
        $completeness->method('getIterator')->willReturn(
            new ArrayIterator([$attributesTask, $availabilityTask, $contentTask])
        );

        return $completeness;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getProduct(string $code): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn($code);

        return $product;
    }

    private function getTemplatePath(string $template): string
    {
        return 'templates/' . $template;
    }
}
