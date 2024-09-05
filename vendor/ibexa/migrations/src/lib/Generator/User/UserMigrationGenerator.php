<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\User;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentFilteringAdapter;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Migration\Generator\CriterionFactoryInterface;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use function in_array;
use Webmozart\Assert\Assert;

final class UserMigrationGenerator implements MigrationGeneratorInterface
{
    public const TYPE = 'user';

    private UserService $userService;

    private StepFactoryInterface $stepFactory;

    private int $chunkSize;

    private CriterionFactoryInterface $criterionFactory;

    private Mode $mode;

    private ContentService $contentService;

    public function __construct(
        ContentService $contentService,
        UserService $userService,
        StepFactoryInterface $stepFactory,
        CriterionFactoryInterface $criterionFactory,
        int $chunkSize
    ) {
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->stepFactory = $stepFactory;
        $this->criterionFactory = $criterionFactory;
        $this->chunkSize = $chunkSize;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->stepFactory->getSupportedModes();
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

        $filter = new Filter();
        $logicalAnd = new LogicalAnd([$this->criterionFactory->build($matchProperty, $value)]);
        $filter->withCriterion($logicalAnd);
        $filter->withLimit($this->chunkSize);

        yield from $this->generateSteps(
            new BatchIterator(
                new ContentFilteringAdapter($this->contentService, $filter),
                $this->chunkSize
            )
        );
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $userContents
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(iterable $userContents): iterable
    {
        $steps = [];
        foreach ($userContents as $content) {
            $user = $this->userService->loadUser($content->id);
            $steps[] = $this->createUserStep($user);
        }

        return $steps;
    }

    private function createUserStep(User $user): StepInterface
    {
        return $this->stepFactory->create($user, $this->mode);
    }
}
