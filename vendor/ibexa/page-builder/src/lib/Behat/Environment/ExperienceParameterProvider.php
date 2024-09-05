<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Environment;

use Ibexa\Behat\Browser\Environment\ParameterProviderInterface;

class ExperienceParameterProvider implements ParameterProviderInterface
{
    /** @var \Ibexa\Behat\Browser\Environment\ParameterProviderInterface */
    private $innerParameterProvider;

    public function __construct(ParameterProviderInterface $innerParameterProvider)
    {
        $this->innerParameterProvider = $innerParameterProvider;
        $this->setParameter('root_content_name', 'Ibexa Digital Experience Platform');
    }

    public function getParameter(string $parameterName): string
    {
        return $this->innerParameterProvider->getParameter($parameterName);
    }

    public function setParameter(string $key, $value): void
    {
        $this->innerParameterProvider->setParameter($key, $value);
    }
}
