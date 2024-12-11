<?php

declare(strict_types=1);

namespace Tests\Core\DataResolver;

use ModigBlog\Core\Content\DataResolver\ArticleListResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class ArticleListResolverTest extends TestCase
{
    private ArticleListResolver $articleListResolver;

    private EntityRepository&MockObject $articleRepositoryMock;

    private SystemConfigService&MockObject $systemConfigServiceMock;
    private SalesChannelContext&MockObject $contextMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(SalesChannelContext::class);
        $this->articleRepositoryMock = $this->createMock(EntityRepository::class);
        $this->systemConfigServiceMock = $this->createMock(SystemConfigService::class);
        $this->articleListResolver = new ArticleListResolver(
            $this->articleRepositoryMock,
            $this->systemConfigServiceMock,
        );

        parent::setUp();
    }

    /**
     * @dataProvider provideGetArticlesCases
     */
    public function testGetArticles(int $limit, int $defaultLimit, string $sortBy, Request $request): void
    {
        $this->systemConfigServiceMock
            ->method('get')
            ->willReturnCallback(
                static function (string $arg) use ($limit, $defaultLimit, $sortBy): int|string {
                    switch ($arg) {
                        case 'ModigBlog.config.articlesPerPage':
                            return $limit;
                        case 'ModigBlog.config.articlesPerPageDefault':
                            return $defaultLimit;
                        case 'ModigBlog.config.articlesSortBy':
                            return $sortBy;
                    }

                    throw new RuntimeException(sprintf('Unexpected argument %s!', $arg));
                }
            );

        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('search')
            ->willReturn($this->createMock(EntitySearchResult::class));

        $this->articleListResolver->getArticles($request, $this->contextMock);
    }

    public static function provideGetArticlesCases(): iterable
    {
        return [
            'Get articles list' => [
                'limit' => 0,
                'defaultLimit' => 10,
                'sortBy' => 'publishedAt',
                'request' => new Request(),
            ],
        ];
    }

    /**
     * @dataProvider provideGetPaginationCases
     */
    public function testGetPagination(int $limit, int $defaultLimit, int $total, int $expected): void
    {
        $this->systemConfigServiceMock->method('get')
            ->willReturnCallback(
                static function (string $arg) use ($limit, $defaultLimit): int|string {
                    switch ($arg) {
                        case 'ModigBlog.config.articlesPerPage':
                            return $limit;
                        case 'ModigBlog.config.articlesPerPageDefault':
                            return $defaultLimit;
                    }

                    throw new RuntimeException(sprintf('Unexpected argument %s!', $arg));
                }
            );

        $entityCollection = $this->createMock(EntityCollection::class);
        $entityCollection
            ->expects(self::once())
            ->method('count')
            ->willReturn($total);
        $entitySearchResult = $this->createMock(EntitySearchResult::class);
        $entitySearchResult
            ->expects(self::once())
            ->method('getEntities')
            ->willReturn($entityCollection);

        $this->articleRepositoryMock
            ->expects(self::once())
            ->method('search')
            ->willReturn($entitySearchResult);

        $result = $this->articleListResolver->getPagination($this->contextMock);

        self::assertSame(count($result), $expected);
    }

    public static function provideGetPaginationCases(): iterable
    {
        return [
            'Get articles pagination' => [
                'limit' => 0,
                'defaultLimit' => 10,
                'total' => 33,
                'expected' => 4,
            ],
        ];
    }
}
