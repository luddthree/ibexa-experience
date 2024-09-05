<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\Httpable;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;
use Throwable;

class FormNotFoundException extends NotFoundException implements Translatable, Httpable
{
    use TranslatableBase;

    /**
     * @param int $contentId
     * @param int|null $versionNo
     * @param array $languageCodes
     * @param int|null $formId
     * @param \Throwable $previous
     */
    public function __construct(
        int $contentId = null,
        ?int $versionNo = null,
        array $languageCodes = [],
        int $formId = null,
        Throwable $previous = null
    ) {
        $identifiers = [];

        if (!empty($contentId)) {
            $identifiers['contentId'] = $contentId;
        }

        if (!empty($formId)) {
            $identifiers['formId'] = $formId;
        }

        if (!empty($versionNo)) {
            $identifiers['versionNo'] = $versionNo;
        }

        if (!empty($languageCodes)) {
            $identifiers['languageCodes'] = sprintf('[%s]', implode(', ', $languageCodes));
        }

        foreach ($identifiers as $key => &$value) {
            $value .= sprintf('%s: %s', $key, $value);
        }

        $this->setMessageTemplate("Could not find Form for '%identifier%'");
        $this->setParameters(['%identifier%' => implode(', ', $identifiers)]);

        parent::__construct($this->getBaseTranslation(), self::NOT_FOUND, $previous);
    }
}

class_alias(FormNotFoundException::class, 'EzSystems\EzPlatformFormBuilder\Exception\FormNotFoundException');
