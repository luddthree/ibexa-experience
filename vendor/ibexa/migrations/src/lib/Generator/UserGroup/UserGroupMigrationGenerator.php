<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\UserGroup;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentFilteringAdapter;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\UserGroup\StepBuilder\Factory;
use function in_array;
use Webmozart\Assert\Assert;

final class UserGroupMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'user_group';

    /** @var \Ibexa\Migration\Generator\UserGroup\StepBuilder\Factory */
    private $userGroupStepFactory;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(Factory $userGroupStepFactory, ContentService $contentService, UserService $userService)
    {
        $this->userGroupStepFactory = $userGroupStepFactory;
        $this->contentService = $contentService;
        $this->userService = $userService;
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
        return $this->userGroupStepFactory->getSupportedModes();
    }

    public function generate(Mode $migrationMode, array $context): iterable
    {
        Assert::keyExists($context, 'value');
        Assert::notEmpty($context['value']);

        foreach ($this->getUserGroups($context) as $userGroup) {
            yield $this->userGroupStepFactory->create($userGroup, $migrationMode);
        }
    }

    /**
     * @param array<mixed> $context
     *
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\UserGroup>
     */
    private function getUserGroups(array $context): iterable
    {
        if (isset($context['match-property'])) {
            Assert::isIterable($context['value']);
            Assert::notEmpty($context['value']);
            $values = $context['value'];

            switch ($context['match-property']) {
                case 'remoteId':
                    foreach ($values as $value) {
                        Assert::stringNotEmpty($value);
                        yield $this->userService->loadUserGroupByRemoteId($value);
                    }

                    return;
                case 'id':
                    foreach ($values as $value) {
                        Assert::integerish($value);
                        yield $this->userService->loadUserGroup((int) $value);
                    }

                    return;
            }
        }

        $filter = new Filter();
        $filter->andWithCriterion(new LogicalAnd([
            new ContentTypeIdentifier('user_group'),
        ]));

        $contentList = new BatchIterator(new ContentFilteringAdapter($this->contentService, $filter));
        foreach ($contentList as $content) {
            yield $this->userService->loadUserGroup($content->id);
        }
    }
}

class_alias(UserGroupMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\UserGroup\UserGroupMigrationGenerator');
