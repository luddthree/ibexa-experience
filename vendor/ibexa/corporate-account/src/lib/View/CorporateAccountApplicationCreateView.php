<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class CorporateAccountApplicationCreateView extends BaseView
{
    private FormInterface $corporateAccountApplicationForm;

    private Location $parentLocation;

    private Language $language;

    private ContentType $contentType;

    /** @var array<string, array<int, string>> */
    private array $groupedFields;

    private bool $submitted;

    /**
     * @param array<string, array<int, string>> $groupedFields
     */
    public function __construct(
        string $templateIdentifier,
        FormInterface $corporateAccountApplicationForm,
        Location $parentLocation,
        Language $language,
        ContentType $contentType,
        array $groupedFields,
        bool $submitted
    ) {
        parent::__construct($templateIdentifier);

        $this->corporateAccountApplicationForm = $corporateAccountApplicationForm;
        $this->parentLocation = $parentLocation;
        $this->language = $language;
        $this->contentType = $contentType;
        $this->groupedFields = $groupedFields;
        $this->submitted = $submitted;
    }

    /**
     * @return array{
     *     corporate_account_application_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters(): array
    {
        return [
            'corporate_account_application_form' => $this->corporateAccountApplicationForm->createView(),
        ];
    }

    public function getCorporateAccountApplicationForm(): FormInterface
    {
        return $this->corporateAccountApplicationForm;
    }

    public function setCorporateAccountApplicationForm(FormInterface $corporateAccountApplicationForm): void
    {
        $this->corporateAccountApplicationForm = $corporateAccountApplicationForm;
    }

    public function getParentLocation(): Location
    {
        return $this->parentLocation;
    }

    public function setParentLocation(Location $parentLocation): void
    {
        $this->parentLocation = $parentLocation;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function setContentType(ContentType $contentType): void
    {
        $this->contentType = $contentType;
    }

    /** @return array<string, array<int, string>> */
    public function getGroupedFields(): array
    {
        return $this->groupedFields;
    }

    /** @param array<string, array<int, string>> $groupedFields */
    public function setGroupedFields(array $groupedFields): void
    {
        $this->groupedFields = $groupedFields;
    }

    public function isSubmitted(): bool
    {
        return $this->submitted;
    }

    public function setSubmitted(bool $submitted): void
    {
        $this->submitted = $submitted;
    }
}
