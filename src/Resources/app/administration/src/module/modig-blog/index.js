import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';
import './acl';

const { Application } = Shopware;

Shopware.Component.register('sw-article-list', () => import('./page/sw-article-list'));
Shopware.Component.register('sw-article-detail', () => import('./page/sw-article-detail'));

Application.addServiceProviderDecorator('searchTypeService', searchTypeService => {
    searchTypeService.upsertType('article', {
        entityName: 'article',
        placeholderSnippet: 'sw-article.general.placeholderSearchBar',
        listingRoute: 'article',
        hideOnGlobalSearchBar: false,
    });

    return searchTypeService;
});

Shopware.Module.register('sw-article', {
    title: 'sw-article',
    entity: 'article',
    description: 'sw-article',
    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
    routes: {
        index: {
            components: {
                default: 'sw-article-list',
            },
            path: 'index',
            meta: {
                privilege: 'article.viewer',
            },
        },
        create: {
            component: 'sw-article-detail',
            path: 'create',
            meta: {
                parentPath: 'sw.article.index',
                privilege: 'article.creator',
            },
        },
        detail: {
            component: 'sw-article-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'sw.article.index',
                privilege: 'article.viewer',
            },
            props: {
                default(route) {
                    return {
                        articleId: route.params.id,
                    };
                },
            },
        },
    },
    navigation: [{
        id: 'sw-article',
        label: 'sw-article.general.mainMenuItemList',
        color: '#ff3d58',
        path: 'sw.article.index',
        parent: 'sw-content',
        position: 100
    }],

    defaultSearchConfiguration: {
        _searchable: true,
        author: {
            _searchable: true,
            _score: 500,
        },
        title: {
            _searchable: true,
            _score: 500,
        },
    },
});