import ApiService from 'src/core/service/api.service';

class PricemotionApiService extends ApiService {
  constructor(httpClient, loginService, apiEndpoint = 'pricemotion') {
    super(httpClient, loginService, apiEndpoint);
    this.name = 'pricemotionService';
  }

  getWidgetUrl(productId) {
    const headers = this.getBasicHeaders();

    return this.httpClient
      .post(
        `/_action/${this.getApiBasePath()}/getWidgetUrl`,
        { productId },
        { headers }
      )
      .then((response) => {
        return ApiService.handleResponse(response);
      });
  }
}
