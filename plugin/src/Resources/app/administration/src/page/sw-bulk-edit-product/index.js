import template from './sw-bulk-edit-product.html.twig';

const FORM_PRICEMOTION = 'pricemotion';

Shopware.Component.override('sw-bulk-edit-product', {
  template,
  computed: {
    pricemotionFormFields() {
      return [
        {
          name: FORM_PRICEMOTION,
          config: {
            allowOverwrite: true,
            allowClear: true,
            changeLabel: this.$tc('sw-bulk-edit.product.customFields.changeLabel', 0, { name: 'Pricemotion' }),
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
