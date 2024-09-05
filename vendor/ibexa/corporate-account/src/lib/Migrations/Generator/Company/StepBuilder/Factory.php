<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Generator\Company\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use function sprintf;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\CorporateAccount\Values\Company $company
     */
    protected function prepareLogMessage(ValueObject $company, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($company, Company::class);

        return sprintf('[Step] Preparing %s %s for %s', $type, $mode->getMode(), $company->getName());
    }
}
