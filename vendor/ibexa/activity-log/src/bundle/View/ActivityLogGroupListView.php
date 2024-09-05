<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class ActivityLogGroupListView extends BaseView
{
    /** @var iterable<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface> */
    private iterable $activityLogGroups;

    private FormInterface $searchForm;

    /**
     * @param iterable<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface> $activityLogGroups
     */
    public function __construct($templateIdentifier, iterable $activityLogGroups, FormInterface $searchForm)
    {
        parent::__construct($templateIdentifier);
        $this->activityLogGroups = $activityLogGroups;
        $this->searchForm = $searchForm;
    }

    /**
     * @phpstan-return array{
     *     activity_logs_groups: iterable<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface>,
     *     search_form: \Symfony\Component\Form\FormView,
     * }
     */
    protected function getInternalParameters(): array
    {
        return [
            'activity_logs_groups' => $this->activityLogGroups,
            'search_form' => $this->searchForm->createView(),
        ];
    }
}
