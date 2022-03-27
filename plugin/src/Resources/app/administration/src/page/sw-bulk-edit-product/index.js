import template from './sw-bulk-edit-product.html.twig';

const { Component } = Shopware;

const FORM_PRICEMOTION = 'pricemotion';

Component.override('sw-bulk-edit-product', {
  template,
  computed: {
    pricemotionFormFields() {
      return [
        {
          name: FORM_PRICEMOTION,
          config: {
            changeLabel: this.$tc('sw-bulk-edit.product.customFields.changeLabel', 0, { name: 'Pricemotion' }),
            componentName: 'pricemotion-bulk-edit-form-field',
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
