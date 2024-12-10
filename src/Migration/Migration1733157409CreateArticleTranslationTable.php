<?php

declare(strict_types=1);

namespace ModigBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1733157409CreateArticleTranslationTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1733157409;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `article_translation` (
                `article_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `teaser` TEXT NOT NULL,
                `content` LONGTEXT NOT NULL,
                `created_at` DATETIME(3) DEFAULT NULL,
                `updated_at` DATETIME(3) DEFAULT NULL,
                PRIMARY KEY (`article_id`, `language_id`),
                KEY `idx.title` (`title`),
                CONSTRAINT `fk.article_translation.article_id` FOREIGN KEY (`article_id`)
                    REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.article_translation.language_id` FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            )
                ENGINE = InnoDB
                DEFAULT CHARSET = utf8mb4
                COLLATE = utf8mb4_unicode_ci;
        SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = 'DROP TABLE `article_translation`;';
        $connection->executeStatement($sql);
    }
}
