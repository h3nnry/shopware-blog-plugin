<?php

declare(strict_types=1);

namespace ModigBlog\Storefront\Controller;

use ModigBlog\Core\Content\DataResolver\ArticleListResolver;
use ModigBlog\Core\Content\Repository\ArticleRepository;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
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
        private readonly SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
    ) {
    }

    #[Route(path: '/blog', name: 'frontend.modigblog.index', methods: ['GET'])]
    public function index(Request $request, SalesChannelContext $context): Response
    {
        if ($this->systemConfigService->get('ModigBlog.config.enabled') !== true) {
            throw new PageNotFoundException('blog');
        }

        $articles = $this->articleListResolver->getArticles($request, $context);
        $page = $this->genericPageLoader->load($request, $context);
        $pagination = $this->articleListResolver->getPagination($context);

        $metaInformation = $page->getMetaInformation();

        if ($metaInformation !== null) {
            $metaTitle = $this->systemConfigService->get('ModigBlog.config.metaTitle');
            $metaDescription = $this->systemConfigService->get('ModigBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('ModigBlog.config.metaKeywords');
            $pageNumber = $request->query->get('page');
            $pageNumberText = $pageNumber > 1 ? $this->trans('page').' '.$pageNumber.' - ' : '';
            $metaInformation->setMetaTitle($pageNumberText.$metaTitle);
            $metaInformation->setMetaDescription($metaDescription ?: '');
            $metaInformation->setMetaKeywords($metaKeywords ?: '');
            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate(
                'frontend.modigblog.index',
                ['page' => $pageNumber > 1 ? $pageNumber : null]
            );
            $page->getMetaInformation()->setCanonical($canonical);
        }

        return $this->renderStorefront('@ModigBlog/storefront/blog/index.html.twig', [
            'page' => $page,
            'articles' => $articles,
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/blog/{id}', name: 'frontend.modigblog.view', methods: ['GET'])]
    public function view(string $id, Request $request, SalesChannelContext $context): Response
    {
        $article = $this->articleRepository->find($id, $context);

        if ($article === null || $this->systemConfigService->get('ModigBlog.config.enabled') !== true) {
            throw new PageNotFoundException($id);
        }

        $page = $this->genericPageLoader->load($request, $context);
        $metaInformation = $page->getMetaInformation();

        if ($metaInformation !== null) {
            $metaDescription = $this->systemConfigService->get('ModigBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('ModigBlog.config.metaKeywords');

            $metaInformation->setMetaTitle($article->getTitle());
            $metaInformation->setMetaKeywords($metaKeywords ?: '');
            $metaInformation->setMetaDescription($metaDescription ?: $article->getTeaser());
            $metaInformation->setAuthor($article->getAuthor());

            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate('frontend.modigblog.view', ['id' => $id]);
            $page->getMetaInformation()->setCanonical($canonical);
        }

        return $this->renderStorefront('@ModigBlog/storefront/blog/view.html.twig', [
            'article' => $article,
        ]);
    }
}
