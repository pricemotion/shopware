const { Component } = Shopware;

Component.override('sw-product-list', {
  methods: {
    getProductColumns() {
      let result = this.$super('getProductColumns');

      result = [
        ...result,
        {
          property: 'pricemotionLowestPrice',
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
