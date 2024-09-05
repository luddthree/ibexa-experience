<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\Section\Executor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\StepExecutor\SectionCreateStepExecutor;
use Ibexa\Migration\ValueObject\Section\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\SectionCreateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\SectionCreateStepExecutor
 */
final class SectionCreateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private const SECTION_IDENTIFIER = '__standard__';
    private const SECTION_NAME = '__NAME__';

    /** @var \Ibexa\Migration\StepExecutor\SectionCreateStepExecutor */
    private $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executor = new SectionCreateStepExecutor(
            self::getTransactionHandler(),
            self::getSectionService(),
            self::getServiceByClassName(Executor::class),
        );

        $this->configureExecutor($this->executor, [
            ResolverInterface::class => self::getReferenceResolver('section'),
        ]);
    }

    public function testHandle(): void
    {
        $collector = self::getServiceByClassName(CollectorInterface::class);
        $references = $collector->getCollection();
        self::assertCount(0, $references->getAll());

        $section = $this->findSection();
        self::assertNull($section);

        $step = $this->createStep();

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $createdSection = $this->findSection();
        self::assertNotNull($createdSection);
        self::assertSame(self::SECTION_IDENTIFIER, $createdSection->identifier);
        self::assertSame(self::SECTION_NAME, $createdSection->name);

        $collector = self::getServiceByClassName(CollectorInterface::class);
        $references = $collector->getCollection();

        self::assertCount(1, $references->getAll());
        self::assertTrue($references->has('foo'));
        $reference = $references->get('foo');
        self::assertSame('foo', $reference->getName());
        self::assertIsInt($reference->getValue());
    }

    private function findSection(): ?Section
    {
        try {
            return self::getSectionService()->loadSectionByIdentifier(self::SECTION_IDENTIFIER);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    private function createStep(): SectionCreateStep
    {
        return new SectionCreateStep(
            CreateMetadata::createFromArray([
                'identifier' => self::SECTION_IDENTIFIER,
                'name' => self::SECTION_NAME,
            ]),
            [
                new ReferenceDefinition('foo', 'section_id'),
            ],
        );
    }
}
