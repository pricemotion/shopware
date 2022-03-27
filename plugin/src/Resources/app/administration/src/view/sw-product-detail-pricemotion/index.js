import template from './sw-product-detail-pricemotion.html.twig';
import { settingsValid } from '../../symbols.js';

const { Component } = Shopware;
const { mapState } = Component.getComponentHelper();

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
    ean() {
      return (this.product?.ean || '').replace(/^\s+|\s+$/g, '');
    },
    widgetParams() {
      if (!this.product || !this.product.id) {
        return;
      }
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
      this.getExtension()[settingsValid] = message.isValid;
      if (message.isValid) {
        this.getExtension().settings = message.value;
      }
    },
  },
});
