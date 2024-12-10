<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Repository;

use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface EntityRepositoryInterface
{
    public function find(string $id, SalesChannelContext $context): ?object;
}
