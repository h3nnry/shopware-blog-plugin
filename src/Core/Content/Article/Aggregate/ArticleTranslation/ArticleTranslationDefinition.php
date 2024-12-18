<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Article\Aggregate\ArticleTranslation;

use ModigBlog\Core\Content\Article\ArticleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ArticleTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'article_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ArticleTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ArticleTranslationCollection::class;
    }

    public function getParentDefinitionClass(): string
    {
        return ArticleDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new Required()),
            (new StringField('teaser', 'teaser'))->addFlags(new Required()),
            (new LongTextField('content', 'content'))->addFlags(new Required()),
        ]);
    }
}
