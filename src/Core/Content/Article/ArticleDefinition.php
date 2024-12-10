<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Article;

use ModigBlog\Core\Content\Article\Aggregate\ArticleTranslation\ArticleTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ArticleDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'article';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ArticleEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ArticleCollection::class;
    }

    public function getHydratorClass(): string
    {
        return ArticleHydrator::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new Required(), new PrimaryKey()),
            (new TranslatedField('title'))->addFlags(new ApiAware(), new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('teaser'))->addFlags(new ApiAware(), new Required()),
            (new TranslatedField('content'))->addFlags(new ApiAware(), new Required()),
            (new TranslationsAssociationField(
                ArticleTranslationDefinition::class,
                'article_id'
            ))->addFlags(new ApiAware(), new Required()),
            (new BoolField('status', 'status'))->addFlags(new ApiAware(), new Required()),
            (new DateTimeField('published_at', 'publishedAt'))->addFlags(new ApiAware(), new Required()),
            (new StringField('author', 'author'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
        ]);
    }
}
