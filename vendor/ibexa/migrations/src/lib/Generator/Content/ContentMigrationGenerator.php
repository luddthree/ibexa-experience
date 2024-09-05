<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Content;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentSearchAdapter;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Migration\Generator\CriterionFactoryInterface;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use function in_array;
use Webmozart\Assert\Assert;

final class ContentMigrationGenerator implements MigrationGeneratorInterface
{
    public const TYPE_CONTENT = 'content';
    public const TYPE_USER = 'user';

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface */
    private $contentStepFactory;

    /** @var int */
    private $chunkSize;

    /** @var \Ibexa\Migration\Generator\CriterionFactoryInterface */
    private $criterionFactory;

    /** @var \Ibexa\Migration\Generator\Mode */
    private $mode;

    /** @var string */
    private $type;

    public function __construct(
        SearchService $searchService,
        StepFactoryInterface $contentStepFactory,
        CriterionFactoryInterface $criterionFactory,
        string $type,
        int $chunkSize
    ) {
        $this->searchService = $searchService;
        $this->contentStepFactory = $contentStepFactory;
        $this->criterionFactory = $criterionFactory;
        $this->type = $type;
        $this->chunkSize = $chunkSize;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->contentStepFactory->getSupportedModes();
    }

    /**
     * @phpstan-return iterable<\Ibexa\Migration\ValueObject\Step\StepInterface>
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable
    {
        Assert::keyExists($context, 'value');
        Assert::notEmpty($context['value']);
        Assert::keyExists($context, 'match-property');

        $this->mode = $migrationMode;
        $matchProperty = $context['match-property'];
        $value = $context['value'];

        $query = $this->createQuery($matchProperty, $value);

        yield from $this->generateSteps(
            new BatchIterator(
                new ContentSearchAdapter($this->searchService, $query),
                $this->chunkSize
            )
        );
    }

    /**
     * @phpstan-param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit> $searchHits
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit[] $searchHits
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(iterable $searchHits): iterable
    {
        $steps = [];
        foreach ($searchHits as $hit) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            $content = $hit->valueObject;
            $steps[] = $this->createContentStep($content);
        }

        return $steps;
    }

    /**
     * @param array<scalar> $value
     */
    private function createQuery(?string $matchProperty, array $value): Query
    {
        $query = new Query();
        $query->query = $this->criterionFactory->build($matchProperty, $value);
        $query->sortClauses = [new Query\SortClause\ContentId()];
        $query->limit = $this->chunkSize;

        return $query;
    }

    private function createContentStep(Content $content): StepInterface
    {
        return $this->contentStepFactory->create($content, $this->mode);
    }
}

class_alias(ContentMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\Content\ContentMigrationGenerator');
