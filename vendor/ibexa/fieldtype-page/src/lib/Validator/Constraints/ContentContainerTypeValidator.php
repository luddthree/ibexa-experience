<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Validator\Constraints;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Specification\Content\ContentContainerSpecification;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContentContainerTypeValidator extends ConstraintValidator
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param int $contentId
     * @param \Symfony\Component\Validator\Constraint $constraint
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function validate($contentId, Constraint $constraint): void
    {
        if (null === $contentId) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $content = $this->contentService->loadContent((int)$contentId);
        $contentContainerSpecification = new ContentContainerSpecification();

        if ($contentContainerSpecification->isSatisfiedBy($content)) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}

class_alias(ContentContainerTypeValidator::class, 'EzSystems\EzPlatformPageFieldType\Validator\Constraints\ContentContainerTypeValidator');
