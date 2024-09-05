<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Type;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\LanguageChoiceLoader;
use Ibexa\AdminUi\Form\Type\Language\LanguageChoiceType;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;

/**
 * @template T of object
 *
 * @covers \Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupCreateType
 */
abstract class AbstractCustomerGroupTypeTest extends AbstractTypeTestCase
{
    /**
     * @return T
     */
    abstract protected function getData(): object;

    /**
     * @param T $model
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    abstract protected function getForm(object $model): FormInterface;

    /**
     * @dataProvider provideValidData
     *
     * @param array<string, mixed> $formData
     * @param callable(T): void $expectation
     */
    public function testSubmitValidData(array $formData, callable $expectation): void
    {
        $model = $this->getData();
        $form = $this->getForm($model);

        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid(), (string) $form->getErrors(true, true));

        $expectation($model);
    }

    /**
     * @phpstan-return iterable<array{
     *     array<string, mixed>,
     *     callable(T): void,
     * }>
     */
    abstract public function provideValidData(): iterable;

    /**
     * @dataProvider provideInvalidData
     *
     * @param array<string, mixed> $formData
     */
    public function testSubmitInvalidData(array $formData): void
    {
        $model = $this->getData();
        $form = $this->getForm($model);
        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertFalse($form->isValid(), (string) $form->getErrors(true, true));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    abstract public function provideInvalidData(): iterable;

    /**
     * @return array<\Symfony\Component\Form\FormExtensionInterface>
     */
    protected function getExtensions(): array
    {
        $languageService = $this->createMock(LanguageService::class);
        $languageService
            ->method('loadLanguages')
            ->willReturn([
                new Language([
                    'id' => 4,
                    'languageCode' => 'eng-GB',
                    'enabled' => true,
                ]),
            ])
        ;

        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->method('getParameter')
            ->willReturn(['eng-GB']);

        $languageChoiceLoader = new LanguageChoiceLoader(
            $languageService,
            $configResolver,
        );

        $type = new LanguageChoiceType($languageChoiceLoader);

        $extensions = parent::getExtensions();
        $extensions[] = new PreloadedExtension([$type], []);

        return $extensions;
    }
}
