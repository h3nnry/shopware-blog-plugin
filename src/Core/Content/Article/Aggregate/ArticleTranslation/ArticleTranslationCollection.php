<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Article\Aggregate\ArticleTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                          add(ArticleTranslationEntity $entity)
 * @method void                          set(string $key, ArticleTranslationEntity $entity)
 * @method ArticleTranslationEntity[]    getIterator()
 * @method ArticleTranslationEntity[]    getElements()
 * @method ArticleTranslationEntity|null get(string $key)
 * @method ArticleTranslationEntity|null first()
 * @method ArticleTranslationEntity|null last()
 */
class ArticleTranslationCollection extends EntityCollection
{
    /**
     * @return array<string>
     */
    public function getArticleIds(): array
    {
        return $this->fmap(static fn (ArticleTranslationEntity $articleTranslationEntity) => $articleTranslationEntity->getArticleId());
    }

    public function filterByArticleId(string $id): self
    {
        return $this->filter(static fn (ArticleTranslationEntity $articleTranslationEntity) => $articleTranslationEntity->getArticleId() === $id);
    }

    /**
     * @return array<string>
     */
    public function getLanguageIds(): array
    {
        return $this->fmap(static fn (ArticleTranslationEntity $articleTranslationEntity) => $articleTranslationEntity->getLanguageId());
    }

    public function filterByLanguageId(string $id): self
    {
        return $this->filter(static fn (ArticleTranslationEntity $articleTranslationEntity) => $articleTranslationEntity->getLanguageId() === $id);
    }

    public function getApiAlias(): string
    {
        return 'article_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return ArticleTranslationEntity::class;
    }
}
