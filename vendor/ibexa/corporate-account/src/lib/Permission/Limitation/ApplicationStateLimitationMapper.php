<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Permission\Limitation;

use Ibexa\AdminUi\Limitation\LimitationFormMapperInterface;
use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\CorporateAccount\Form\Type\Limitation\ApplicationStateLimitationType;
use Symfony\Component\Form\FormInterface;

final class ApplicationStateLimitationMapper implements LimitationValueMapperInterface, LimitationFormMapperInterface
{
    private ?string $formTemplate = null;

    /**
     * @return array<string, string[]>
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        return $limitation->limitationValues;
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data): void
    {
        $form->add('limitationValues', ApplicationStateLimitationType::class);
    }

    public function getFormTemplate(): ?string
    {
        return $this->formTemplate;
    }

    /**
     * @return array<string, string[]>
     */
    public function filterLimitationValues(Limitation $limitation): array
    {
        return $limitation->limitationValues;
    }

    public function setFormTemplate(string $formTemplate): void
    {
        $this->formTemplate = $formTemplate;
    }
}
