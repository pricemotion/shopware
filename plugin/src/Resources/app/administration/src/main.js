import './component/pricemotion-bulk-edit-form-field';
import './page/sw-bulk-edit-product';
import './page/sw-product-detail';
import './view/sw-product-detail-pricemotion';
import PricemotionApiService from './core/service/api/pricemotion.api.service';

const { Module, Application } = Shopware;

Module.register('kibo-pricemotion', {
  routeMiddleware(next, currentRoute) {
    if (currentRoute.name === 'sw.product.detail') {
      currentRoute.children.push({
        name: 'sw.product.detail.pricemotion',
        path: '/sw/product/detail/:id/pricemotion',
        component: 'sw-product-detail-pricemotion',
        props: (route) => ({ productId: route.params.id }),
        meta: {
          parentPath: 'sw.product.index',
        },
      });
    }
    next(currentRoute);
  },
});

Application.addServiceProvider('pricemotionApiService', (container) => {
  const initContainer = Application.getContainer('init');
  return new PricemotionApiService(initContainer.httpClient, container.loginService, undefined, initContainer.locale);
});
