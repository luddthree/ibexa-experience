<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\CorporatePortal;

use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class MyProfileEditView extends BaseView
{
    private FormInterface $form;

    private Member $member;

    public function __construct(
        string $templateIdentifier,
        Member $member,
        FormInterface $form
    ) {
        parent::__construct($templateIdentifier);

        $this->form = $form;
        $this->member = $member;
    }

    /**
     * @return array{
     *     user: \Ibexa\Contracts\Core\Repository\Values\User\User,
     *     form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'form' => $this->form->createView(),
            'user' => $this->member->getUser(),
        ];
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): void
    {
        $this->member = $member;
    }
}
