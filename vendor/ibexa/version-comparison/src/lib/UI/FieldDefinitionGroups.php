<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\UI;

use Ibexa\Contracts\VersionComparison\VersionDiff;
use Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList;

class FieldDefinitionGroups
{
    /** @var \Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $fieldsGroupsListHelper;

    /**
     * @param \Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList $fieldsGroupsListHelper
     */
    public function __construct(FieldsGroupsList $fieldsGroupsListHelper)
    {
        $this->fieldsGroupsListHelper = $fieldsGroupsListHelper;
    }

    public function groupFieldDefinitionsDiff(VersionDiff $versionDiff): array
    {
        $fieldDefinitionsByGroup = [];
        foreach ($this->fieldsGroupsListHelper->getGroups() as $groupId => $groupName) {
            $fieldDefinitionsByGroup[$groupId] = [
                'name' => $groupName,
                'fieldValueDiff' => [],
            ];
        }

        /** @var \Ibexa\Contracts\VersionComparison\FieldValueDiff $fieldDiff */
        foreach ($versionDiff as $fieldDiff) {
            $groupId = $fieldDiff->getFieldDefinition()->fieldGroup;
            if (!$groupId) {
                $groupId = $this->fieldsGroupsListHelper->getDefaultGroup();
            }

            $fieldDefinitionsByGroup[$groupId]['fieldValueDiff'][] = $fieldDiff;
            $fieldDefinitionsByGroup[$groupId]['name'] = $fieldDefinitionsByGroup[$groupId]['name'] ?? $groupId;
        }

        return $fieldDefinitionsByGroup;
    }
}

class_alias(FieldDefinitionGroups::class, 'EzSystems\EzPlatformVersionComparison\UI\FieldDefinitionGroups');
