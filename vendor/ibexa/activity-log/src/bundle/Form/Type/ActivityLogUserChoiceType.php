<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Form\Type;

use Ibexa\AdminUi\Form\Type\UserChoiceType;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\IsUserBased;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ActivityLogUserChoiceType extends AbstractType
{
    private ContentService $contentService;

    private UserService $userService;

    private PermissionResolver $permissionResolver;

    public function __construct(
        ContentService $contentService,
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
    }

    public function getParent(): string
    {
        return UserChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault(
            'choice_loader',
            ChoiceList::lazy(
                $this,
                function (): array {
                    $currentUserReference = $this->permissionResolver->getCurrentUserReference();
                    $currentUser = $this->userService->loadUser($currentUserReference->getUserId());

                    $users = [$currentUser];

                    $criterion = new IsUserBased();
                    $sortClauses = [new ContentName()];
                    $filter = new Filter($criterion, $sortClauses);
                    $filter = $filter->withLimit(50);

                    $contentList = $this->contentService->find($filter);

                    foreach ($contentList as $item) {
                        if ($item->id === $currentUser->getUserId()) {
                            continue;
                        }
                        $users[] = $this->userService->loadUser($item->id);
                    }

                    return $users;
                },
            )
        );
    }
}
