import ApiService from 'src/core/service/api.service';

const { Application } = Shopware;

export default class PricemotionApiService extends ApiService {
  constructor(httpClient, loginService, apiEndpoint = 'pricemotion') {
    super(httpClient, loginService, apiEndpoint);
    this.name = 'pricemotionService';
  }

  async getWidgetUrl() {
    const headers = this.getBasicHeaders();
    const response = await this.httpClient.post(`${this.getApiBasePath()}/get-widget-url`, { headers });
    return ApiService.handleResponse(response);
  }
}
