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
use Ibexa\Migration\StepExecutor\SectionUpdateStepExecutor;
use Ibexa\Migration\ValueObject\Section\Matcher;
use Ibexa\Migration\ValueObject\Section\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\SectionUpdateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\SectionUpdateStepExecutor
 */
final class SectionUpdateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private const KNOWN_FIXTURE_SECTION_IDENTIFIER = 'standard';
    private const KNOWN_FIXTURE_SECTION_ID = 1;
    private const NEW_FIXTURE_SECTION_IDENTIFIER = '__NEW_IDENTIFIER__';
    private const SECTION_NAME = '__NAME__';

    /** @var \Ibexa\Migration\StepExecutor\SectionUpdateStepExecutor */
    private $executor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executor = new SectionUpdateStepExecutor(
            self::getTransactionHandler(),
            self::getSectionService(),
            self::getServiceByClassName(Executor::class),
        );

        $this->configureExecutor($this->executor, [
            ResolverInterface::class => self::getReferenceResolver('section'),
        ]);
    }

    /**
     * @dataProvider provideSteps
     */
    public function testHandle(SectionUpdateStep $step): void
    {
        $collector = self::getServiceByClassName(CollectorInterface::class);
        $references = $collector->getCollection();
        self::assertCount(0, $references->getAll());

        $section = $this->findSection(self::KNOWN_FIXTURE_SECTION_IDENTIFIER);
        self::assertNotNull($section);
        self::assertNotEquals(self::NEW_FIXTURE_SECTION_IDENTIFIER, $section->identifier);
        self::assertNotEquals(self::SECTION_NAME, $section->name);

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $updatedSection = $this->findSection(self::NEW_FIXTURE_SECTION_IDENTIFIER);
        self::assertNotNull($updatedSection);
        self::assertSame(self::NEW_FIXTURE_SECTION_IDENTIFIER, $updatedSection->identifier);
        self::assertSame(self::SECTION_NAME, $updatedSection->name);

        $collector = self::getServiceByClassName(CollectorInterface::class);
        $references = $collector->getCollection();

        self::assertCount(1, $references->getAll());
        self::assertTrue($references->has('foo'));
        $reference = $references->get('foo');
        self::assertSame('foo', $reference->getName());
        self::assertIsInt($reference->getValue());
    }

    private function findSection(string $identifier): ?Section
    {
        try {
            return self::getSectionService()->loadSectionByIdentifier($identifier);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\SectionUpdateStep}>
     */
    public function provideSteps(): iterable
    {
        $metadata = UpdateMetadata::createFromArray([
            'identifier' => self::NEW_FIXTURE_SECTION_IDENTIFIER,
            'name' => self::SECTION_NAME,
        ]);
        $references = [
            new ReferenceDefinition('foo', 'section_id'),
        ];

        yield [
            new SectionUpdateStep(
                new Matcher(Matcher::IDENTIFIER, self::KNOWN_FIXTURE_SECTION_IDENTIFIER),
                $metadata,
                $references,
            ),
        ];

        yield [
            new SectionUpdateStep(
                new Matcher(Matcher::ID, self::KNOWN_FIXTURE_SECTION_ID),
                $metadata,
                $references,
            ),
        ];
    }
}

class_alias(SectionUpdateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\SectionUpdateStepExecutorTest');
