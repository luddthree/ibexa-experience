<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Tab;

use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\ProductCatalog\Tab\AbstractTranslationsTab;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class BaseTranslationsTabTest extends TestCase
{
    protected AbstractTranslationsTab $translationTab;

    protected function setUp(): void
    {
        /** @var \Ibexa\ProductCatalog\Tab\AbstractTranslationsTab $translationTab */
        $translationTab = $this->getTranslationTabName();

        $this->translationTab = new $translationTab(
            $this->createMock(Environment::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createFormFactoryMock(),
            $this->createMock(PermissionResolverInterface::class),
            $this->createLanguageServiceMock(),
            $this->createMock(UrlGeneratorInterface::class)
        );
    }

    protected function createFormFactoryMock(): FormFactoryInterface
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($this->createMock(FormView::class));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('createNamed')->willReturn($form);

        return $formFactory;
    }

    protected function createLanguageServiceMock(): LanguageService
    {
        $languageService = $this->createMock(LanguageService::class);
        $languageService
            ->method('loadLanguageListByCode')
            ->willReturnMap([
                [
                    [/* Empty list of codes */],
                    [],
                ],
                [
                    ['eng-GB'],
                    [new Language(['id' => 2,  'languageCode' => 'eng-GB'])],
                ],
                [
                    ['eng-GB', 'pol-PL'],
                    [
                        new Language(['id' => 2,  'languageCode' => 'eng-GB']),
                        new Language(['id' => 4,  'languageCode' => 'pol-PL']),
                    ],
                ],
            ]);

        return $languageService;
    }

    /**
     * @dataProvider providerForEvaluate
     *
     * @param mixed $evaluatedParameter
     */
    public function testEvaluate($evaluatedParameter, bool $expectedResult): void
    {
        self::assertSame(
            $expectedResult,
            $this->translationTab->evaluate([$this->getParameterKey() => $evaluatedParameter])
        );
    }

    /**
     * @return iterable<string,array{mixed,bool}>
     */
    abstract public function providerForEvaluate(): iterable;

    abstract public function getParameterKey(): string;

    abstract public function getTranslationTabName(): string;
}
