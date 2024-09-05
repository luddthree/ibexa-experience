<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Data\Submission;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

class SubmissionRemoveData
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null */
    protected $contentInfo;

    /** @var array|null */
    protected $submissions;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param array|null $submissions
     */
    public function __construct(?ContentInfo $contentInfo = null, array $submissions = [])
    {
        $this->contentInfo = $contentInfo;
        $this->submissions = $submissions;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null
     */
    public function getContentInfo(): ?ContentInfo
    {
        return $this->contentInfo;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|null $contentInfo
     */
    public function setContentInfo(?ContentInfo $contentInfo): void
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return array|null
     */
    public function getSubmissions(): ?array
    {
        return $this->submissions;
    }

    /**
     * @param array|null $submissions
     */
    public function setSubmissions(?array $submissions): void
    {
        $this->submissions = $submissions;
    }
}

class_alias(SubmissionRemoveData::class, 'EzSystems\EzPlatformFormBuilder\Form\Data\Submission\SubmissionRemoveData');
