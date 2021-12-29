import ApiService from 'src/core/service/api.service';

export default class PricemotionApiService extends ApiService {
  constructor(httpClient, loginService, apiEndpoint = 'pricemotion') {
    super(httpClient, loginService, apiEndpoint);
    this.name = 'pricemotionService';
  }

  async getWidgetUrl(productId) {
    const headers = this.getBasicHeaders();

    const response = await this.httpClient.post(
      `${this.getApiBasePath()}/get-widget-url`,
      { productId },
      { headers }
    );

    const data = ApiService.handleResponse(response);

    return data.url;
  }
}
