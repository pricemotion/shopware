const { Classes } = Shopware;

export default class PricemotionApiService extends Classes.ApiService {
  constructor(httpClient, loginService, apiEndpoint = 'pricemotion') {
    super(httpClient, loginService, apiEndpoint);
    this.name = 'pricemotionService';
  }

  async getAppUrl() {
    const headers = this.getBasicHeaders();
    const response = await this.httpClient.post(`${this.getApiBasePath()}/get-app-url`, {}, { headers });
    return this.constructor.handleResponse(response);
  }
}
