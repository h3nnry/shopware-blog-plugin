/*
 * @package inventory
 */

import template from './sw-article-detail.html.twig';
import './sw-article-detail.scss';

const {
    Mixin,
    Data: { Criteria },
} = Shopware;

const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    compatConfig: Shopware.compatConfig,

    inject: [
        'repositoryFactory',
        'acl',
    ],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
        Mixin.getByName('discard-detail-page-changes')('article'),
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
        ESCAPE: 'onCancel',
    },

    props: {
        articleId: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            article: null,
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.article, 'title');
        },

        articleIsLoading() {
            return this.isLoading || this.article == null;
        },

        articleRepository() {
            return this.repositoryFactory.create('article');
        },

        tooltipSave() {
            if (this.acl.can('article.editor')) {
                const systemKey = this.$device.getSystemKey();

                return {
                    message: `${systemKey} + S`,
                    appearance: 'light',
                };
            }

            return {
                showDelay: 300,
                message: this.$tc('sw-privileges.tooltip.warning'),
                disabled: this.acl.can('article.editor'),
                showOnDisabledElements: true,
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },

        ...mapPropertyErrors('article', ['title', 'teaser', 'content', 'status', 'publishedAt', 'author']),
    },

    watch: {
        articleId() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            Shopware.ExtensionAPI.publishData({
                id: 'sw-article-detail__article',
                path: 'article',
                scope: this,
            });
            if (this.articleId) {
                this.loadEntityData();
                return;
            }

            Shopware.State.commit('context/resetLanguageToDefault');
            this.article = this.articleRepository.create();
        },

        async loadEntityData() {
            this.isLoading = true;

            this.article = await this.articleRepository.get(this.articleId);

            this.isLoading = false;

            if (!this.article) {
                this.createNotificationError({
                    message: this.$tc('global.notification.notificationLoadingDataErrorMessage'),
                });
            }
            console.log("88888++++++", this.article);
        },

        abortOnLanguageChange() {
            return this.articleRepository.hasChanges(this.article);
        },

        saveOnLanguageChange() {
            return this.onSave();
        },

        onChangeLanguage() {
            this.loadEntityData();
        },

        onSave() {
            if (!this.acl.can('article.editor')) {
                return;
            }

            this.isLoading = true;

            this.articleRepository
                .save(this.article)
                .then(() => {
                    this.isLoading = false;
                    this.isSaveSuccessful = true;
                    if (this.articleId === null) {
                        this.$router.push({
                            name: 'sw.article.detail',
                            params: { id: this.article.id },
                        });
                        return;
                    }

                    this.loadEntityData();
                })
                .catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        message: this.$tc('global.notification.notificationSaveErrorMessageRequiredFieldsInvalid'),
                    });
                    throw exception;
                });
        },

        onCancel() {
            this.$router.push({ name: 'sw.article.index' });
        },
    },
};
