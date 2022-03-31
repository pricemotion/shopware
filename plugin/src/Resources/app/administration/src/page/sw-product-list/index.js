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
          property: 'extensions.pricemotion.lowestPrice',
          label: this.$tc('pricemotion.columnLowestPrice'),
          allowResize: true,
          visible: false,
          align: 'right',
        },
      ];

      return result;
    },
  },
});
