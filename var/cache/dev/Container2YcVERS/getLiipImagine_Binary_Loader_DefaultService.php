<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getLiipImagine_Binary_Loader_DefaultService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'liip_imagine.binary.loader.default' shared service.
     *
     * @return \Liip\ImagineBundle\Binary\Loader\FileSystemLoader
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/liip/imagine-bundle/Binary/Loader/FileSystemLoader.php';
        include_once \dirname(__DIR__, 4).'/vendor/liip/imagine-bundle/Binary/Locator/LocatorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/liip/imagine-bundle/Binary/Locator/FileSystemLocator.php';

        $a = \Symfony\Component\Mime\MimeTypes::getDefault();

        return $container->services['liip_imagine.binary.loader.default'] = new \Liip\ImagineBundle\Binary\Loader\FileSystemLoader($a, $a, new \Liip\ImagineBundle\Binary\Locator\FileSystemLocator([0 => (\dirname(__DIR__, 4).'/public')], false));
    }
}
