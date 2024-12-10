<?php

declare(strict_types=1);

namespace ModigBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1733144790CreateArticleTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1733144790;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `article` (
                `id` BINARY(16) NOT NULL,
                `status` TINYINT(1),
                `author` VARCHAR(255) DEFAULT NULL,
                `published_at` DATETIME NOT NULL,
                `created_at` DATETIME(3) DEFAULT NULL,
                `updated_at` DATETIME(3) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx.author` (`author`),
                KEY `idx.published_at` (`published_at`)
            )
                ENGINE = InnoDB
                DEFAULT CHARSET = utf8mb4
                COLLATE = utf8mb4_unicode_ci;
        SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = 'DROP TABLE `article`;';
        $connection->executeStatement($sql);
    }
}
