<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Siteaccess;

use Ibexa\AdminUi\Siteaccess\AbstractSiteaccessPreviewVoter;

class FormBuilderSiteaccessPreviewVoter extends AbstractSiteaccessPreviewVoter
{
    /**
     * {@inheritdoc}
     */
    public function getRootLocationIds(string $siteaccess): array
    {
        return [
            $this->configResolver->getParameter(
                'form_builder.forms_location_id',
                null,
                $siteaccess
            ),
        ];
    }
}

class_alias(FormBuilderSiteaccessPreviewVoter::class, 'EzSystems\EzPlatformFormBuilder\Siteaccess\FormBuilderSiteaccessPreviewVoter');
