<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Article;

use DateTimeImmutable;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityHydrator;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\Uuid\Uuid;

class ArticleHydrator extends EntityHydrator
{
    protected function assign(EntityDefinition $definition, Entity $entity, string $root, array $row, Context $context): Entity
    {
        if (isset($row[$root.'.id'])) {
            $entity->id = Uuid::fromBytesToHex($row[$root.'.id']);
        }
        if (isset($row[$root.'.author'])) {
            $entity->author = $row[$root.'.author'];
        }
        if (isset($row[$root.'.status'])) {
            $entity->status = (bool) $row[$root.'.status'];
        }
        if (isset($row[$root.'.createdAt'])) {
            $entity->createdAt = new DateTimeImmutable($row[$root.'.createdAt']);
        }
        if (isset($row[$root.'.updatedAt'])) {
            $entity->updatedAt = new DateTimeImmutable($row[$root.'.updatedAt']);
        }
        if (isset($row[$root.'.publishedAt'])) {
            $entity->publishedAt = new DateTimeImmutable($row[$root.'.publishedAt']);
        }

        $this->translate($definition, $entity, $row, $root, $context, $definition->getTranslatedFields());
        $this->hydrateFields($definition, $entity, $root, $row, $context, $definition->getExtensionFields());
        $this->customFields($definition, $row, $root, $entity, $definition->getField('customFields'), $context);

        return $entity;
    }
}
