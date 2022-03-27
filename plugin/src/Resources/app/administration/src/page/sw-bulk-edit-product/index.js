import template from './sw-bulk-edit-product.html.twig';

const FORM_PRICEMOTION = 'pricemotion';

Shopware.Component.override('sw-bulk-edit-product', {
  template,
  computed: {
    pricemotionFormFields() {
      console.log('pricemotionFormFields');
      return [
        {
          name: FORM_PRICEMOTION,
          config: {
            allowOverwrite: true,
            allowClear: true,
            changeLabel: this.$tc('pricemotion.bulkChangeLabel'),
          },
        },
      ];
    },
  },
  methods: {
    loadBulkEditData() {
      this.$super('loadBulkEditData');
      this.defineBulkEditData(FORM_PRICEMOTION);
    },
  },
});
