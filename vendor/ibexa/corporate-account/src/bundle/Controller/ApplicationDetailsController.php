<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\CorporateAccount\View\ApplicationDetailsView;
use Symfony\Component\HttpFoundation\Request;

class ApplicationDetailsController extends Controller
{
    public function detailsAction(Request $request, Application $application): ApplicationDetailsView
    {
        $location = $application->getContent()->getVersionInfo()->getContentInfo()->getMainLocation();

        if (null === $location) {
            throw new NotFoundException('location', $application->getContent()->contentInfo->mainLocationId);
        }

        return new ApplicationDetailsView(
            '@ibexadesign/corporate_account/application/details.html.twig',
            $application
        );
    }
}
