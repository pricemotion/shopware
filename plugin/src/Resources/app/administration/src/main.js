import './page/sw-product-detail';
import './view/sw-product-detail-pricemotion';

Shopware.Module.register('kibo-pricemotion', {
  routeMiddleware(next, currentRoute) {
    if (currentRoute.name === 'sw.product.detail') {
      currentRoute.children.push({
        name: 'sw.product.detail.pricemotion',
        path: '/sw/product/detail/:id/pricemotion',
        component: 'sw-product-detail-pricemotion',
        meta: {
          parentPath: 'sw.product.index',
        },
      });
    }
    next(currentRoute);
  },
});
