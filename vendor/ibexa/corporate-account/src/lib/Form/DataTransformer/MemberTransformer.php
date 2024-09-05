<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\CorporateAccount\Form\Data\Member\MemberData;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class MemberTransformer implements DataTransformerInterface
{
    private MemberService $memberService;

    public function __construct(
        MemberService $memberService
    ) {
        $this->memberService = $memberService;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Member) {
            throw new TransformationFailedException('Expected a ' . Member::class . ' object.');
        }

        $data = new MemberData();
        $data->setUser($value->getUser());
        $data->setCompany($value->getCompany());

        return $data;
    }

    public function reverseTransform($value): ?Member
    {
        if (empty($value)) {
            return null;
        }

        if (!$value instanceof MemberData) {
            throw new TransformationFailedException('Expected a ' . MemberData::class . ' object.');
        }

        try {
            return $this->memberService->getMember($value->getUser()->getUserId(), $value->getCompany());
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
