<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Bundle\Core\Controller;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Security\PersonalizationPolicyProvider;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractPersonalizationAjaxController extends Controller
{
    protected PermissionCheckerInterface $permissionChecker;

    protected SettingServiceInterface $settingService;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        SettingServiceInterface $settingService
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->settingService = $settingService;
    }

    /**
     * @return array<int, array{code: mixed|int, message: string}>
     */
    protected function performAccessCheck(Request $request, int $customerId): array
    {
        $errors = [];

        if (false === $request->isXmlHttpRequest()) {
            $badRequestException = new BadRequestHttpException(
                'Not allowed Http Request'
            );
            $errors[] = [
                'code' => $badRequestException->getStatusCode(),
                'message' => $badRequestException->getMessage(),
            ];
        }

        if (false === $this->permissionChecker->canView($customerId)) {
            $exception = new UnauthorizedException(
                PersonalizationPolicyProvider::PERSONALIZATION_MODULE,
                PersonalizationPolicyProvider::PERSONALIZATION_VIEW_FUNCTION,
            );

            $errors[] = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ];
        }

        return $errors;
    }

    /**
     * @return array<string|array<string|mixed>>
     */
    protected function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        /** @var \Symfony\Component\Form\FormError $error */
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getFormErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}

class_alias(AbstractPersonalizationAjaxController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\AbstractPersonalizationAjaxController');
