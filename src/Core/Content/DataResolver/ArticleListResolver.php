<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\DataResolver;

use ModigBlog\Core\Content\Article\ArticleEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

final readonly class ArticleListResolver
{
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_BY = 'publishedAt';

    public function __construct(
        private EntityRepository $articleRepository,
        private SystemConfigService $systemConfigService,
    ) {
    }

    /**
     * @return ArticleEntity[]
     */
    public function getArticles(Request $request, SalesChannelContext $context): array
    {
        $limit = (int) $this->systemConfigService->get('ModigBlog.config.articlesPerPage');
        $articleListLimit = (int) ($this->systemConfigService->get('ModigBlog.config.articlesPerPageDefault') ?? self::DEFAULT_PER_PAGE);
        $sortBy = $this->systemConfigService->get('ModigBlog.config.articlesSortBy') ?? self::DEFAULT_SORT_BY;

        if (!$limit) {
            $limit = $articleListLimit;
        }

        $pageNumber = $this->getPage($request);
        $pageOffset = ($pageNumber - 1) * $limit;

        $postsCriteria = (new Criteria())
            ->addFilter(new EqualsFilter('status', 1))
            ->addSorting(new FieldSorting($sortBy, FieldSorting::DESCENDING))
            ->setLimit($limit)
            ->setOffset($pageOffset);

        return $this->articleRepository->search($postsCriteria, $context->getContext())->getEntities()->getElements();
    }

    public function getPagination(SalesChannelContext $context): array
    {
        $criteria = new Criteria();

        $limit = (int) $this->systemConfigService->get('ModigBlog.config.articlesPerPage');
        $articleListLimit = (int) $this->systemConfigService->get('ModigBlog.config.articlesPerPageDefault');
        $totalArticles = $this->articleRepository->search($criteria, $context->getContext())->getEntities()->count();

        if (!$limit) {
            $limit = $articleListLimit;
        }

        $pages = [];
        $pagesCount = $totalArticles / $limit;
        for ($i = 1; $i <= ceil($pagesCount); ++$i) {
            $pages[$i] = $totalArticles / $limit > 1 ? $limit : $totalArticles;
            $totalArticles -= $limit;
        }

        return $pages;
    }

    private function getPage(Request $request): int
    {
        $page = $request->query->getInt('page', 1);

        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt('page', $page);
        }

        return $page <= 0 ? 1 : $page;
    }
}
