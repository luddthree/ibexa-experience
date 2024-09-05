<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use JMS\TranslationBundle\Annotation\Desc;

abstract class AbstractDetailsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    public function getIdentifier(): string
    {
        return 'details';
    }

    public function getName(): string
    {
        /** @Desc("Details") */
        return $this->translator->trans('tab.name.details', [], 'ibexa_product_catalog');
    }

    public function getOrder(): int
    {
        return 100;
    }

    abstract public function getTemplate(): string;

    abstract public function getTemplateParameters(array $contextParameters = []): array;
}
