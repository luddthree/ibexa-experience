<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Exception\InvalidModeException;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ServiceLocator;

abstract class AbstractStepFactory implements StepFactoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Symfony\Component\DependencyInjection\ServiceLocator */
    private $serviceLocator;

    /** @var string[] */
    private $supportedModes;

    /** @var string */
    private $type;

    public function __construct(ServiceLocator $serviceLocator, string $type)
    {
        $this->serviceLocator = $serviceLocator;
        $this->supportedModes = array_keys($serviceLocator->getProvidedServices());
        $this->logger = new NullLogger();
        $this->type = $type;
    }

    /**
     * @return string[]
     */
    final public function getSupportedModes(): array
    {
        return $this->supportedModes;
    }

    final public function create(ValueObject $valueObject, Mode $mode): StepInterface
    {
        $this->guardMode($mode);

        $builder = $this->getBuilder($mode);
        $this->log($valueObject, $mode);

        return $builder->build($valueObject);
    }

    private function guardMode(Mode $mode): void
    {
        if (false === $this->serviceLocator->has($mode->getMode())) {
            throw new InvalidModeException($mode->getMode(), $this->supportedModes, $this->type);
        }
    }

    private function getBuilder(Mode $mode): StepBuilderInterface
    {
        return $this->serviceLocator->get($mode->getMode());
    }

    private function log(ValueObject $valueObject, Mode $mode): void
    {
        $message = $this->prepareLogMessage($valueObject, $mode, $this->type);
        if (null !== $message) {
            $this->getLogger()->notice($message);
        }
    }

    protected function prepareLogMessage(ValueObject $valueObject, Mode $mode, string $type): ?string
    {
        return null;
    }
}

class_alias(AbstractStepFactory::class, 'Ibexa\Platform\Migration\Generator\StepBuilder\AbstractStepFactory');
