import template from './pricemotion-bulk-edit-form-field.html.twig';

const { Component } = Shopware;

Component.register('pricemotion-bulk-edit-form-field', {
  template,
  methods: {
    updateProductSettings(message) {
      if (message.isValid) {
        this.$emit('input', { settings: message.value });
      } else {
        this.$emit('input', false);
      }
    },
  },
});
