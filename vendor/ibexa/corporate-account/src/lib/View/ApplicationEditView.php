<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class ApplicationEditView extends BaseView
{
    private Application $application;

    private FormInterface $applicationForm;

    public function __construct(
        string $templateIdentifier,
        Application $application,
        FormInterface $applicationForm
    ) {
        parent::__construct($templateIdentifier);

        $this->application = $application;
        $this->applicationForm = $applicationForm;
    }

    /**
     * @return array{
     *     application: \Ibexa\Contracts\CorporateAccount\Values\Application,
     *     application_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'application' => $this->application,
            'application_form' => $this->applicationForm->createView(),
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

    public function getApplicationForm(): FormInterface
    {
        return $this->applicationForm;
    }

    public function setApplicationForm(FormInterface $applicationForm): void
    {
        $this->applicationForm = $applicationForm;
    }
}
