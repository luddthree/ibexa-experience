<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Core\MVC\Symfony\View\BaseView;

class ApplicationDetailsView extends BaseView
{
    private Application $application;

    public function __construct(
        string $templateIdentifier,
        Application $application
    ) {
        parent::__construct($templateIdentifier);

        $this->application = $application;
    }

    /**
     * @return array{
     *     application: \Ibexa\Contracts\CorporateAccount\Values\Application
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'application' => $this->application,
        ];
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }
}
