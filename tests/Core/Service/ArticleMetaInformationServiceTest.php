<?php

declare(strict_types=1);

namespace Tests\Core\Service;

use ModigBlog\Core\Content\Article\ArticleEntity;
use ModigBlog\Core\Content\Service\ArticleMetaInformationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Adapter\Translation\AbstractTranslator;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\MetaInformation;
use Shopware\Storefront\Page\Page;
use Symfony\Component\HttpFoundation\Request;

class ArticleMetaInformationServiceTest extends TestCase
{
    private ArticleMetaInformationService $articleMetaInformationService;

    private SystemConfigService&MockObject $systemConfigServiceMock;

    private SeoUrlPlaceholderHandlerInterface&MockObject $seoUrlReplacerMock;

    private AbstractTranslator&MockObject $translatorMock;

    private Page&MockObject $pageMock;

    protected function setUp(): void
    {
        $this->systemConfigServiceMock = $this->createMock(SystemConfigService::class);
        $this->seoUrlReplacerMock = $this->createMock(SeoUrlPlaceholderHandlerInterface::class);
        $this->translatorMock = $this->createMock(AbstractTranslator::class);
        $this->pageMock = $this->createMock(Page::class);
        $this->articleMetaInformationService = new ArticleMetaInformationService(
            $this->systemConfigServiceMock,
            $this->seoUrlReplacerMock,
            $this->translatorMock,
        );
        parent::setUp();
    }

    /**
     * @dataProvider provideSetListMetaInformationCases
     */
    public function testSetListMetaInformation(string $metaTitle, string $metaDescription,
        string $metaKeywords, string $canonical): void
    {
        $this->systemConfigServiceMock
            ->method('get')
            ->willReturnCallback(
                static function (string $arg) use ($metaTitle, $metaDescription, $metaKeywords): string {
                    switch ($arg) {
                        case 'ModigBlog.config.metaTitle':
                            return $metaTitle;
                        case 'ModigBlog.config.metaDescription':
                            return $metaDescription;
                        case 'ModigBlog.config.metaKeywords':
                            return $metaKeywords;
                    }

                    throw new RuntimeException(sprintf('Unexpected argument %s!', $arg));
                }
            );

        $request = new Request();
        $metaInformation = new MetaInformation();

        $this->seoUrlReplacerMock
            ->expects(self::once())
            ->method('generate')
            ->willReturn($canonical);

        $this->pageMock
            ->expects(self::exactly(2))
            ->method('getMetaInformation')
            ->willReturn($metaInformation);

        $this->pageMock
            ->expects(self::once())
            ->method('setMetaInformation')
            ->with($metaInformation);

        $this->articleMetaInformationService->setListMetaInformation($this->pageMock, $request);

        self::assertSame($metaTitle, $metaInformation->getMetaTitle());
        self::assertSame($metaDescription, $metaInformation->getMetaDescription());
        self::assertSame($metaKeywords, $metaInformation->getMetaKeywords());
    }

    public static function provideSetListMetaInformationCases(): iterable
    {
        return [
            'Get articles list metainformation' => [
                'metaTitle' => 'Great article',
                'metaDescription' => 'Article description',
                'metaKeywords' => 'key1, key1, key3',
                'canonical' => 'canonical text',
            ],
        ];
    }

    /**
     * @dataProvider provideSetViewMetaInformationCases
     */
    public function testSetViewMetaInformation(ArticleEntity $article, string $metaDescription,
        string $metaKeywords, string $canonical): void
    {
        $this->systemConfigServiceMock
            ->method('get')
            ->willReturnCallback(
                static function (string $arg) use ($metaDescription, $metaKeywords): string {
                    switch ($arg) {
                        case 'ModigBlog.config.metaDescription':
                            return $metaDescription;
                        case 'ModigBlog.config.metaKeywords':
                            return $metaKeywords;
                    }

                    throw new RuntimeException(sprintf('Unexpected argument %s!', $arg));
                }
            );

        $request = new Request();
        $metaInformation = new MetaInformation();

        $this->seoUrlReplacerMock
            ->expects(self::once())
            ->method('generate')
            ->willReturn($canonical);

        $this->pageMock
            ->expects(self::exactly(2))
            ->method('getMetaInformation')
            ->willReturn($metaInformation);

        $this->pageMock
            ->expects(self::once())
            ->method('setMetaInformation')
            ->with($metaInformation);

        $this->articleMetaInformationService->setViewMetaInformation($this->pageMock, $article);

        self::assertSame($article->getTitle(), $metaInformation->getMetaTitle());
        self::assertSame($article->getAuthor(), $metaInformation->getAuthor());
        self::assertSame($metaDescription, $metaInformation->getMetaDescription());
        self::assertSame($metaKeywords, $metaInformation->getMetaKeywords());
    }

    public static function provideSetViewMetaInformationCases(): iterable
    {
        $article = new ArticleEntity();
        $article->setId('abc');
        $article->setTitle('Article 1');
        $article->setAuthor('John Smith');

        return [
            'Get article view metainformation' => [
                'article' => $article,
                'metaDescription' => 'Article description',
                'metaKeywords' => 'key1, key1, key3',
                'canonical' => 'canonical text',
            ],
        ];
    }
}
