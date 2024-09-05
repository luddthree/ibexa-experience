<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\StepExecutor\ReferenceDefinition;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Webmozart\Assert\Assert;

/**
 * Unpacks the Company object for use in Content-based reference definition resolvers.
 */
final class ContentResolver implements ResolverInterface
{
    private ResolverInterface $contentResolver;

    public function __construct(ResolverInterface $contentResolver)
    {
        $this->contentResolver = $contentResolver;
    }

    public function resolve(ReferenceDefinition $referenceDefinition, ValueObject $valueObject): Reference
    {
        Assert::isInstanceOf($valueObject, Company::class);

        return $this->contentResolver->resolve($referenceDefinition, $valueObject->getContent());
    }
}
