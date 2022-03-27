const { Classes } = Shopware;

export default class PricemotionApiService extends Classes.ApiService {
  constructor(httpClient, loginService, apiEndpoint = 'pricemotion') {
    super(httpClient, loginService, apiEndpoint);
    this.name = 'pricemotionService';
  }

  async getWidgetUrl() {
    const headers = this.getBasicHeaders();
    const response = await this.httpClient.post(`${this.getApiBasePath()}/get-widget-url`, { headers });
    return this.handleResponse(response);
  }
}
