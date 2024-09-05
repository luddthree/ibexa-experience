<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Validation\Constraints;

use Ibexa\Bundle\CorporateAccount\IbexaCorporateAccountBundle;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class IsCorporateSiteAccessValidator extends ConstraintValidator
{
    /** @var array<string, array<string>> */
    private array $siteAccessGroups;

    /**
     * @param array<string, array<string>> $siteAccessGroups
     */
    public function __construct(array $siteAccessGroups)
    {
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess $value
     * @param \Ibexa\CorporateAccount\Validation\Constraints\IsCorporateSiteAccess $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsCorporateSiteAccess) {
            throw new UnexpectedTypeException($constraint, IsCorporateSiteAccess::class);
        }

        if (!$value instanceof SiteAccess) {
            throw new UnexpectedValueException($value, SiteAccess::class);
        }

        if (!in_array($value->name, $this->siteAccessGroups[IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME], true)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $value->name)
                ->addViolation();
        }
    }
}
