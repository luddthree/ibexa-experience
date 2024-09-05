<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Type;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;

abstract class AbstractTypeTestCase extends TypeTestCase
{
    protected ContainerInterface $validatorMocksContainer;

    /**
     * @return \Symfony\Component\Form\FormExtensionInterface[]
     *
     * @throws \Exception
     */
    protected function getExtensions(): array
    {
        $validatorFactory = $this->buildValidatorFactory();

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory($validatorFactory)
            ->enableAnnotationMapping(true)
            ->addYamlMapping(__DIR__ . '/../../../../src/bundle/Resources/config/validation.yaml')
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        return [
            new ValidatorExtension($validator, false),
        ];
    }

    protected function buildValidatorFactory(): ContainerConstraintValidatorFactory
    {
        $this->validatorMocksContainer = new Container();
        foreach ($this->getConstraintValidatorMocks() as $key => $mock) {
            $this->validatorMocksContainer->set($key, $mock);
        }

        return new ContainerConstraintValidatorFactory($this->validatorMocksContainer);
    }

    /**
     * @phpstan-return iterable<
     *     class-string<\Symfony\Component\Validator\ConstraintValidatorInterface>,
     *     \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\Validator\ConstraintValidatorInterface
     * >
     */
    protected function getConstraintValidatorMocks(): iterable
    {
        yield from [];
    }
}
