<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\Controller;

use Gregwar\CaptchaBundle\Generator\CaptchaGenerator;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CaptchaController extends AbstractController
{
    /** @var array */
    private $captchaOptions;

    /** @var \Gregwar\CaptchaBundle\Generator\CaptchaGenerator */
    private $captchaGenerator;

    public function __construct(array $captchaOptions, CaptchaGenerator $captchaGenerator)
    {
        $this->captchaOptions = $captchaOptions;
        $this->captchaGenerator = $captchaGenerator;
    }

    public function getCaptchaPathAction(int $fieldId, SessionInterface $session): JsonResponse
    {
        $whitelistKey = $this->captchaOptions['whitelist_key'];

        $keys = $session->get($whitelistKey, []);
        $key = CaptchaType::SESSION_KEY_PREFIX . $fieldId;
        if (!\in_array($key, $keys, true)) {
            $keys[] = $fieldId;
        }

        $session->set($whitelistKey, $keys);

        $persistedOptions = $session->get($key, []);
        $options = array_merge(
            $this->captchaOptions,
            $persistedOptions,
            [
                'as_file' => false,
                'as_url' => false,
            ]
        );

        $image = $this->captchaGenerator->getCaptchaCode($options);
        $this->captchaGenerator->setPhrase($options['phrase']);
        $persistedOptions['phrase'] = $options['phrase'];
        $session->set($key, $persistedOptions);

        $response = new JsonResponse(['image' => $image]);
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }
}

class_alias(CaptchaController::class, 'EzSystems\EzPlatformFormBuilderBundle\Controller\CaptchaController');
