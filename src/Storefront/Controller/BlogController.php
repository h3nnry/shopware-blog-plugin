<?php

declare(strict_types=1);

namespace ModigBlog\Storefront\Controller;

use ModigBlog\Core\Content\DataResolver\ArticleListResolver;
use ModigBlog\Core\Content\Repository\ArticleRepository;
use ModigBlog\Core\Content\Service\ArticleMetaInformationServiceInterface;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
final class BlogController extends StorefrontController
{
    public function __construct(
        private readonly ArticleListResolver $articleListResolver,
        private readonly ArticleRepository $articleRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly GenericPageLoaderInterface $genericPageLoader,
        private readonly ArticleMetaInformationServiceInterface $articleMetaInformationService,
    ) {
    }

    #[Route(path: '/blog', name: 'frontend.modigblog.index', methods: ['GET'])]
    public function index(Request $request, SalesChannelContext $context): Response
    {
        $this->checkPluginAccess();

        $articles = $this->articleListResolver->getArticles($request, $context);
        $page = $this->genericPageLoader->load($request, $context);
        $this->articleMetaInformationService->setListMetaInformation($page, $request);
        $pagination = $this->articleListResolver->getPagination($context);

        return $this->renderStorefront('@ModigBlog/storefront/blog/index.html.twig', [
            'page' => $page,
            'articles' => $articles,
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/blog/{id}', name: 'frontend.modigblog.view', methods: ['GET'])]
    public function view(string $id, Request $request, SalesChannelContext $context): Response
    {
        $this->checkPluginAccess();

        $article = $this->articleRepository->find($id, $context);
        if ($article === null) {
            throw new PageNotFoundException($id);
        }

        $page = $this->genericPageLoader->load($request, $context);
        $this->articleMetaInformationService->setViewMetaInformation($page, $article);

        return $this->renderStorefront('@ModigBlog/storefront/blog/view.html.twig', [
            'article' => $article,
            'page' => $page,
        ]);
    }

    private function checkPluginAccess(): void
    {
        if ($this->systemConfigService->get('ModigBlog.config.enabled') !== true) {
            throw new PageNotFoundException('blog');
        }
    }
}
