<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Workflow\ChoiceLoader;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;

final class RejectReasonsLoader extends ChoiceLoader
{
    public function loadChoiceList(callable $value = null): ChoiceListInterface
    {
        return new ArrayChoiceList(
            $this->configuration->getApplicationStageReasons('reject'),
            $value
        );
    }
}
