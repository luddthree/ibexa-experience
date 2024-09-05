<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Connect\DependencyInjection;

use Ibexa\Bundle\Connect\DependencyInjection\IbexaConnectExtension;
use Ibexa\Connect\PageBuilder\Template;
use Ibexa\Connect\PageBuilder\TemplateRegistry;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Reference;

final class IbexaConnectExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaConnectExtension(),
        ];
    }

    public function testWorksWithoutAnyConfiguration(): void
    {
        $this->load();

        self::assertContainerBuilderHasService(TemplateRegistry::class);
    }

    public function testConfiguration(): void
    {
        $this->load([
            'scenario_block' => [
                'block_templates' => [
                    'foo' => [
                        'template' => '@ibexa_design/foo.html.twig',
                    ],
                    'bar' => [
                        'label' => 'Barex',
                        'template' => '@ibexa_design/bar.html.twig',
                        'parameters' => [
                            'foo_param' => [
                                'type' => 'foo_type',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        self::assertContainerBuilderHasService(TemplateRegistry::class);

        self::assertContainerBuilderHasService('ibexa.connect.template.foo', Template::class);
        self::assertContainerBuilderHasServiceDefinitionWithTag('ibexa.connect.template.foo', 'ibexa.connect.template', [
            'id' => 'foo',
            'name' => 'foo',
            'template' => '@ibexa_design/foo.html.twig',
        ]);
        self::assertContainerBuilderHasService('ibexa.connect.template.bar', Template::class);
        self::assertContainerBuilderHasServiceDefinitionWithTag('ibexa.connect.template.bar', 'ibexa.connect.template', [
            'id' => 'bar',
            'name' => 'Barex',
            'template' => '@ibexa_design/bar.html.twig',
        ]);
        self::assertContainerBuilderHasService('ibexa.connect.template.bar.foo_param', Template\Parameter::class);

        self::assertContainerBuilderHasServiceDefinitionWithArgument('ibexa.connect.template.foo', '$label', 'foo');
        self::assertContainerBuilderHasServiceDefinitionWithArgument('ibexa.connect.template.foo', '$parameters', []);
        self::assertContainerBuilderHasServiceDefinitionWithArgument('ibexa.connect.template.bar', '$label', 'Barex');
        self::assertContainerBuilderHasServiceDefinitionWithArgument('ibexa.connect.template.bar', '$parameters', [
            new Reference('ibexa.connect.template.bar.foo_param'),
        ]);
    }
}
