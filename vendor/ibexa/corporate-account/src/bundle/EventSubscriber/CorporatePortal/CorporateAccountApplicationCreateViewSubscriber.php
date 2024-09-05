<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\CorporateAccountApplicationCreateView;

class CorporateAccountApplicationCreateViewSubscriber extends AbstractViewSubscriber
{
    protected function supports(View $view): bool
    {
        return $view instanceof CorporateAccountApplicationCreateView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\CorporateAccountApplicationCreateView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'form' => $view->getCorporateAccountApplicationForm()->createView(),
            'parent_location' => $view->getParentLocation(),
            'language' => $view->getLanguage(),
            'content_type' => $view->getContentType(),
            'grouped_fields' => $view->getGroupedFields(),
            'is_submitted' => $view->isSubmitted(),
        ]);
    }
}
