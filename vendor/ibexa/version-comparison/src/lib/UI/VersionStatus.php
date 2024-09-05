<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\UI;

use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VersionStatus
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function translateStatus(int $status): string
    {
        switch ($status) {
            case VersionInfo::STATUS_DRAFT:
                /** @Desc("Draft") */
                return $this->translator->trans('version_info.status.draft', [], 'ibexa_fieldtypes_comparison_ui');
            case VersionInfo::STATUS_PUBLISHED:
                /** @Desc("Published") */
                return $this->translator->trans('version_info.status.published', [], 'ibexa_fieldtypes_comparison_ui');
            case VersionInfo::STATUS_ARCHIVED:
                /** @Desc("Archived") */
                return $this->translator->trans('version_info.status.archived', [], 'ibexa_fieldtypes_comparison_ui');
            default:
                throw new InvalidArgumentException('$status', sprintf('Unrecognized versionInfo status: "%d"', $status));
        }
    }
}

class_alias(VersionStatus::class, 'EzSystems\EzPlatformVersionComparison\UI\VersionStatus');
