import template from './sw-product-detail.html.twig';
import { settingsValid } from '../../symbols.js';

const { Component } = Shopware;

Component.override('sw-product-detail', {
  template,
  methods: {
    onSave() {
      if (this.product.extensions?.pricemotion?.[settingsValid] === false) {
        this.createNotificationError({
          message: this.$tc('pricemotion.invalidSettingsError'),
        });
        return Promise.resolve();
      }

      return this.$super('onSave');
    },
  },
});
