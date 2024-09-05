<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FormSubmission;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmissionList;

interface FormSubmissionServiceInterface
{
    /**
     * @phpstan-param array<array{id: int, identifier: string, name: string, value: mixed}> $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\FormBuilder\Exception\FormFieldNotFoundException
     */
    public function create(ContentInfo $content, string $languageCode, Form $form, array $data): FormSubmission;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission $submission
     */
    public function delete(FormSubmission $submission): void;

    /**
     * @param int $id
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmission
     */
    public function loadById(int $id): FormSubmission;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $content
     * @param string|null $languageCode
     * @param int $offset
     * @param int $limit
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FormSubmissionList
     */
    public function loadByContent(ContentInfo $content, ?string $languageCode = null, int $offset = 0, int $limit = 25): FormSubmissionList;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $content
     * @param string|null $languageCode
     *
     * @return int
     */
    public function getCount(ContentInfo $content, ?string $languageCode = null): int;
}

class_alias(FormSubmissionServiceInterface::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\FormSubmissionServiceInterface');
