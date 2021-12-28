import template from './sw-product-detail-pricemotion.html.twig';

Shopware.Component.register('sw-product-detail-pricemotion', {
  template,
  metaInfo() {
    return {
      title: 'Pricemotion',
    };
  },
});
