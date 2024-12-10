<?php

declare(strict_types=1);

namespace ModigBlog\Core\Content\Repository;

use ModigBlog\Core\Content\Article\ArticleEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

final readonly class ArticleRepository implements EntityRepositoryInterface
{
    public function __construct(
        private EntityRepository $articleRepository,
    ) {
    }

    public function find(string $id, SalesChannelContext $context): ?ArticleEntity
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('id', $id));

        return $this->articleRepository->search($criteria, $context->getContext())->getEntities()->first();
    }
}
