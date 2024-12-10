<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Article\Aggregate\ArticleTranslation;

use ModigBlog\Core\Content\Article\ArticleEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class ArticleTranslationEntity extends TranslationEntity
{
    protected string $articleId;

    protected string $title;

    protected string $teaser;

    protected string $content;

    protected ArticleEntity $article;

    public function getArticleId(): string
    {
        return $this->articleId;
    }

    public function setArticleId(string $articleId): void
    {
        $this->articleId = $articleId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTeaser(): string
    {
        return $this->teaser;
    }

    public function setTeaser(string $teaser): void
    {
        $this->teaser = $teaser;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getArticle(): ArticleEntity
    {
        return $this->article;
    }

    public function setArticle(ArticleEntity $article): void
    {
        $this->article = $article;
    }
}
