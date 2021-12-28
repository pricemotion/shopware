import PricemotionApiService from './core/service/api/pricemotion.api.service';

Shopware.Application.addServiceProvider(
  'pricemotionApiService',
  (container) => {
    const init = Application.getContainer('init');
    return new PricemotionApiService(init.httpClient, container.loginService);
  }
);
