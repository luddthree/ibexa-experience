<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form;

use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\CorporateAccount\Form\Type\Workflow\AcceptType;
use Ibexa\CorporateAccount\Form\Type\Workflow\OnHoldType;
use Ibexa\CorporateAccount\Form\Type\Workflow\RejectType;
use Symfony\Component\Form\FormInterface;

final class ApplicationWorkflowFormFactory extends ContentFormFactory
{
    /**
     * @param array<string, mixed>|object|null $data
     */
    public function getForm(string $state, $data = null): FormInterface
    {
        switch ($state) {
            case 'on_hold': return $this->getOnHoldForm($data);
            case 'accept': return $this->getAcceptForm($data);
            case 'reject': return $this->getRejectForm($data);
        }

        throw new InvalidArgumentException('state', 'This factory does not support "' . $state . '" form.');
    }

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function getOnHoldForm($data = null): FormInterface
    {
        return $this->formFactory->create(OnHoldType::class, $data);
    }

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function getAcceptForm($data = null): FormInterface
    {
        return $this->formFactory->create(AcceptType::class, $data);
    }

    /**
     * @param array<string, mixed>|object|null $data
     */
    public function getRejectForm($data = null): FormInterface
    {
        return $this->formFactory->create(RejectType::class, $data);
    }
}
