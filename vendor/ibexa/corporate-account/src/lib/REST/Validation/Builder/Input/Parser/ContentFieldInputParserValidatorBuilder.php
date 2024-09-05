<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\CorporateAccount\REST\Validation\Constraint as IbexaAssert;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContentFieldInputParserValidatorBuilder extends BaseInputParserValidatorBuilder
{
    private ContentType $contentType;

    public function __construct(ValidatorInterface $validator, ContentType $contentType)
    {
        $this->contentType = $contentType;

        parent::__construct($validator);
    }

    protected function buildConstraint(): Constraint
    {
        return new IbexaAssert\FieldValuesListInput($this->contentType);
    }
}
