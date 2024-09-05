<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal;

use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\CorporateAccount\View\MemberCreateView;

final class MemberCreateViewSubscriber extends AbstractViewSubscriber
{
    protected function supports(View $view): bool
    {
        return $view instanceof MemberCreateView;
    }

    /**
     * @param \Ibexa\CorporateAccount\View\MemberCreateView $view
     */
    protected function configureView(View $view): void
    {
        $view->addParameters([
            'form' => $view->getMemberForm()->createView(),
            'members_group' => $view->getMembersGroup(),
            'parent_location' => $view->getMembersGroup()->contentInfo->getMainLocation(),
            'content_type' => $view->getMemberForm()->getData()->contentType,
            'language' => $view->getLanguage(),
            'location' => null,
            'company' => $view->getCompany(),
        ]);
    }
}
