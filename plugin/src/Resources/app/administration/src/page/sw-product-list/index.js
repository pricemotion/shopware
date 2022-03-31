const { Component } = Shopware;

import template from './sw-product-list.html.twig';

Component.override('sw-product-list', {
  template,
  methods: {
    getProductColumns() {
      let result = this.$super('getProductColumns');

      result = [
        ...result,
        {
          property: 'pricemotionHasPriceRule',
          label: this.$tc('pricemotion.columnPriceRule'),
          allowResize: true,
          visible: false,
        },
        {
          property: 'extensions.pricemotion.lowestPrice',
          label: this.$tc('pricemotion.columnLowestPrice'),
          allowResize: true,
          visible: false,
          align: 'right',
        },
      ];

      return result;
    },
    hasPriceRule(product) {
      const rule = product.extensions.pricemotion?.settings?.rule;
      console.log(rule);
      return rule && rule !== 'disabled';
    },
  },
});
