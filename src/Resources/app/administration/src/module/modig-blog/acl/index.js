/*
 * @package inventory
 */

Shopware.Service('privileges').addPrivilegeMappingEntry({
    category: 'permissions',
    parent: 'content',
    key: 'article',
    roles: {
        viewer: {
            privileges: [
                'article:read',
            ],
            dependencies: [],
        },
        editor: {
            privileges: [
                'article:update',
            ],
            dependencies: [
                'article.viewer',
            ],
        },
        creator: {
            privileges: [
                'article:create',
            ],
            dependencies: [
                'article.viewer',
                'article.editor',
            ],
        },
        deleter: {
            privileges: [
                'article:delete',
            ],
            dependencies: [
                'article.viewer',
            ],
        },
    },
});
