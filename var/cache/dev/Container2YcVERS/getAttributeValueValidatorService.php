<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAttributeValueValidatorService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValueValidator' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValueValidator
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/validator/ConstraintValidatorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/validator/ConstraintValidator.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Validator/Constraints/AttributeValueValidator.php';

        return $container->privates['Ibexa\\Bundle\\ProductCatalog\\Validator\\Constraints\\AttributeValueValidator'] = new \Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValueValidator(($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Attribute\\ValueValidatorRegistry'] ?? $container->load('getValueValidatorRegistryService')));
    }
}
