<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Core\Repository\Permission\PermissionCriterionResolver;

/**
 * Handler for permissions Criterion.
 *
 * @deprecated 6.7.7
 */
class PermissionsCriterionHandler extends PermissionCriterionResolver
{
    /**
     * Adds content, read Permission criteria if needed and return false if no access at all.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function addPermissionsCriterion(Criterion &$criterion)
    {
        $permissionCriterion = $this->getPermissionsCriterion();
        if ($permissionCriterion === true || $permissionCriterion === false) {
            return $permissionCriterion;
        }

        // Merge with original $criterion
        if ($criterion instanceof LogicalAnd) {
            $criterion->criteria[] = $permissionCriterion;
        } else {
            $criterion = new LogicalAnd(
                [
                    $criterion,
                    $permissionCriterion,
                ]
            );
        }

        return true;
    }
}

class_alias(PermissionsCriterionHandler::class, 'eZ\Publish\Core\Repository\PermissionsCriterionHandler');
