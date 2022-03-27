import template from './sw-product-detail-pricemotion.html.twig';

const { Component } = Shopware;
const { mapState, mapGetters } = Component.getComponentHelper();

Component.register('sw-product-detail-pricemotion', {
  template,
  metaInfo() {
    return {
      title: 'Pricemotion',
    };
  },
  inject: ['repositoryFactory'],
  props: {
    productId: {
      type: String,
      required: false,
      default: null,
    },
  },
  computed: {
    ...mapState('swProductDetail', ['product']),
    ...mapGetters('swProductDetail', ['isLoading']),
    ean() {
      return (this.product?.ean || '').replace(/^\s+|\s+$/g, '');
    },
    widgetParams() {
      return {
        ean: this.ean,
        settings: this.getExtension().settings,
      };
    },
  },
  methods: {
    getExtension() {
      return (this.product.extensions.pricemotion ??= this.repositoryFactory
        .create('kibo_pricemotion_product')
        .create());
    },
    updateProductSettings(message) {
      if (message.isValid) {
        this.getExtension().settings = message.value;
      }
    },
  },
});
