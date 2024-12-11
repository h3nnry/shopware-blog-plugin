<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Service;

use ModigBlog\Core\Content\Article\ArticleEntity;
use Shopware\Storefront\Page\Page;
use Symfony\Component\HttpFoundation\Request;

interface ArticleMetaInformationServiceInterface
{
    public function setListMetaInformation(Page $page, Request $request): void;

    public function setViewMetaInformation(Page $page, ArticleEntity $article): void;
}
