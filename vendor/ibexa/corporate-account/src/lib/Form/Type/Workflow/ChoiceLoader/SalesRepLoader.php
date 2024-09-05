<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Workflow\ChoiceLoader;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;

final class SalesRepLoader extends ChoiceLoader
{
    private ContentService $contentService;

    private LocationService $locationService;

    public function __construct(
        CorporateAccountConfiguration $configuration,
        ContentService $contentService,
        LocationService $locationService
    ) {
        parent::__construct($configuration);

        $this->contentService = $contentService;
        $this->locationService = $locationService;
    }

    public function loadChoiceList(callable $value = null): ChoiceListInterface
    {
        return new LazyChoiceList(new CallbackChoiceLoader(function () {
            $users = $this->contentService->find(
                new Filter(
                    new LogicalAnd([
                        new Subtree(
                            $this->locationService->loadLocationByRemoteId(
                                $this->configuration->getSalesRepUserGroupRemoteId()
                            )->pathString
                        ),
                        new ContentTypeIdentifier('user'),
                    ])
                )
            );

            $choices = [];
            foreach ($users->getIterator() as $user) {
                $choices[$user->getName()] = $user->id;
            }

            return $choices;
        }), $value);
    }
}
