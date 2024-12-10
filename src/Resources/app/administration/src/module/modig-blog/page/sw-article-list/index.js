/*
 * @package inventory
 */

import template from './sw-article-list.html.twig';

const { Mixin } = Shopware;
const { Criteria } = Shopware.Data;


// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: [
        'repositoryFactory',
        'acl',
        'filterFactory',
    ],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            articles: null,
            isLoading: true,
            sortBy: 'publishedAt',
            sortDirection: 'DESC',
            total: 0,
            searchConfigEntity: 'article',
            filterCriteria: [],
            defaultFilters: [
                'status-filter',
                'published-at-filter'
            ],
            storeKey: 'grid.filter.article',
            activeFilterNumber: 0,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        articleRepository() {
            return this.repositoryFactory.create('article');
        },

        articleColumns() {
            return [
                {
                    property: 'name',
                    dataIndex: 'name',
                    allowResize: true,
                    routerLink: 'sw.article.detail',
                    label: 'sw-article.list.columnId',
                    primary: true,
                    visible: false,
                },
                {
                    property: 'title',
                    label: 'sw-article.list.columnTitle',
                    allowResize: true,
                    visible: true,
                    inlineEdit: 'string',
                },
                {
                    property: 'teaser',
                    dataIndex: 'teaser',
                    label: 'sw-article.list.columnTeaser',
                    visible: false,
                },
                {
                    property: 'content',
                    dataIndex: 'content',
                    label: 'sw-article.list.columnContent',
                    visible: false,
                },
                {
                    property: 'status',
                    dataIndex: 'status',
                    label: 'sw-article.list.columnStatus',
                    allowResize: true,
                    inlineEdit: 'boolean',
                },
                {
                    property: 'publishedAt',
                    dataIndex: 'publishedAt',
                    label: 'sw-article.list.columnPublishedAt',
                    allowResize: true,
                    inlineEdit: 'date',
                },
                {
                    property: 'author',
                    label: 'sw-article.list.columnAuthor',
                    align: 'right',
                    allowResize: true,
                    inlineEdit: 'string',
                }
            ];
        },

        articleCriteria() {
            const articleCriteria = new Criteria(this.page, this.limit);

            articleCriteria.setTerm(this.term);
            articleCriteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.filterCriteria.forEach((filter) => {
                articleCriteria.addFilter(filter);
            });

            return articleCriteria;
        },

        listFilterOptions() {
            return {
                'status-filter': {
                    property: 'status',
                    label: this.$tc('sw-article.filters.statusFilter.label'),
                    placeholder: this.$tc('sw-article.filters.statusFilter.placeholder'),
                },
                'published-at-filter': {
                    property: 'publishedAt',
                    label: this.$tc('sw-article.filters.publishedAtFilter.label'),
                    dateType: 'datetime-local',
                    fromFieldLabel: null,
                    toFieldLabel: null,
                    showTimeframe: true,
                },
            };
        },

        listFilters() {
            return this.filterFactory.create('article', this.listFilterOptions);
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        },
    },

    watch: {
        articleCriteria: {
            handler() {
                this.getList();
            },
            deep: true,
        },
    },

    methods: {
        onChangeLanguage(languageId) {
            this.getList(languageId);
        },

        updateCriteria(criteria) {
            this.page = 1;

            this.filterCriteria = criteria;
        },

        async getList() {
            this.isLoading = true;

            let criteria = await Shopware.Service('filterService').mergeWithStoredFilters(
                this.storeKey,
                this.articleCriteria,
            );

            criteria = await this.addQueryScores(this.term, criteria);

            this.activeFilterNumber = criteria.filters.length;

            if (!this.entitySearchable) {
                this.isLoading = false;
                this.total = 0;

                return false;
            }

            if (this.freshSearchTerm) {
                criteria.resetSorting();
            }

            if (this.freshSearchTerm) {
                criteria.resetSorting();
            }

            return this.articleRepository.search(criteria).then((searchResult) => {
                this.articles = searchResult;
                this.total = searchResult.total;
                this.isLoading = false;
            });
        },

        updateTotal({ total }) {
            this.total = total;
        },
    },
};
