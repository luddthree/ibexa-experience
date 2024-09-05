<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface;
use Pagerfanta\Adapter\AdapterInterface;

class SubmissionsAdapter implements AdapterInterface
{
    /** @var \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface */
    private $formSubmissionService;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var string */
    private $languageCode;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FormSubmission\FormSubmissionServiceInterface $formSubmissionService
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param string $languageCode
     */
    public function __construct(
        FormSubmissionServiceInterface $formSubmissionService,
        ContentInfo $contentInfo,
        string $languageCode
    ) {
        $this->formSubmissionService = $formSubmissionService;
        $this->contentInfo = $contentInfo;
        $this->languageCode = $languageCode;
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     */
    public function getNbResults(): int
    {
        return $this->formSubmissionService->getCount($this->contentInfo, $this->languageCode);
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return array|\Traversable the slice
     *
     * @throws \Exception
     */
    public function getSlice($offset, $length)
    {
        return $this->formSubmissionService->loadByContent($this->contentInfo, $this->languageCode, $offset, $length);
    }
}

class_alias(SubmissionsAdapter::class, 'EzSystems\EzPlatformFormBuilder\Pagination\Pagerfanta\SubmissionsAdapter');
