<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Customer;

final class BaseInformation
{
    /** @var int|null */
    private $customerId;

    /** @var string|null */
    private $version;

    /** @var string|null */
    private $website;

    /** @var string|null */
    private $alphanumericItems;

    /** @var string|null */
    private $solutionType;

    /** @var bool|null */
    private $enabled;

    private function __construct(
        ?int $customerId = null,
        ?string $version = null,
        ?string $website = null,
        ?string $alphanumericItems = null,
        ?string $solutionType = null,
        ?bool $enabled = null
    ) {
        $this->customerId = $customerId;
        $this->version = $version;
        $this->website = $website;
        $this->alphanumericItems = $alphanumericItems;
        $this->solutionType = $solutionType;
        $this->enabled = $enabled;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getAlphanumericItems(): ?string
    {
        return $this->alphanumericItems;
    }

    public function getSolutionType(): ?string
    {
        return $this->solutionType;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public static function fromArray(array $parameters): self
    {
        return new self(
            isset($parameters['id']) ? (int)$parameters['id'] : null,
            $parameters['version'] ?? null,
            $parameters['website'] ?? null,
            $parameters['alphanumericItems'] ?? null,
            $parameters['solutionType'] ?? null,
            isset($parameters['enabled']) ? (bool)$parameters['enabled'] : null,
        );
    }
}

class_alias(BaseInformation::class, 'Ibexa\Platform\Personalization\Value\Customer\BaseInformation');
