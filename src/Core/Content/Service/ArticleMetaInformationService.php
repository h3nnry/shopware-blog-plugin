<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Service;

use ModigBlog\Core\Content\Article\ArticleEntity;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Adapter\Translation\AbstractTranslator;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Page;
use Symfony\Component\HttpFoundation\Request;

final readonly class ArticleMetaInformationService implements ArticleMetaInformationServiceInterface
{
    public function __construct(
        private SystemConfigService $systemConfigService,
        private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
        private AbstractTranslator $translator,
    ) {
    }

    public function setListMetaInformation(Page $page, Request $request): void
    {
        $metaInformation = $page->getMetaInformation();

        if ($metaInformation !== null) {
            $metaTitle = $this->systemConfigService->get('ModigBlog.config.metaTitle');
            $metaDescription = $this->systemConfigService->get('ModigBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('ModigBlog.config.metaKeywords');
            $pageNumber = $request->query->get('page');
            $pageNumberText = $pageNumber > 1 ? $this->translator->trans('page').' '.$pageNumber.' - ' : '';
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
    }

    public function setViewMetaInformation(Page $page, ArticleEntity $article): void
    {
        $metaInformation = $page->getMetaInformation();

        if ($metaInformation !== null) {
            $metaDescription = $this->systemConfigService->get('ModigBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('ModigBlog.config.metaKeywords');

            $metaInformation->setMetaTitle($article->getTitle());
            $metaInformation->setMetaKeywords($metaKeywords ?: '');
            $metaInformation->setMetaDescription($metaDescription ?: $article->getTeaser());
            $metaInformation->setAuthor($article->getAuthor());

            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate('frontend.modigblog.view', ['id' => $article->getId()]);
            $page->getMetaInformation()->setCanonical($canonical);
        }
    }
}
