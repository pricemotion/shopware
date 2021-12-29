import template from './sw-product-detail-pricemotion.html.twig';

Shopware.Component.register('sw-product-detail-pricemotion', {
  template,
  metaInfo() {
    return {
      title: 'Pricemotion',
    };
  },
  inject: ['pricemotionApiService'],
  props: {
    productId: {
      type: String,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      url: null,
      iframeHeight: 250,
    };
  },
  async created() {
    console.log('Pricemotion: Retrieve widget URL for product', this.productId);
    this.url = await this.pricemotionApiService.getWidgetUrl(this.productId);
    this.installMessageHandler();
  },
  methods: {
    async getUrl() {
      if (!this.productId) {
        console.log('Pricemotion: Not rendering widget because productId is unset');
        return null;
      }

      const context = Shopware.Context.api;

      const response = await this.httpClient.post(
        '/pricemotion/widgetUrl',
        { productId: this.productId },
        {
          headers: {
            Accept: 'application/vnd.api+json',
            Authorization: `Bearer ${context.authToken.access}`,
            'Content-Type': 'application/json',
          },
        }
      );

      if (!response || !response.url) {
        console.log('Pricemotion: No URL in response');
        return null;
      }

      return response.url;
    },
    installMessageHandler() {
      const handler = (e) => {
        if (!this.$refs.iframe || e.source !== this.$refs.iframe.contentWindow) {
          return;
        }
        const messageOrigin = new URL(e.origin).origin;
        const expectedOrigin = new URL(this.url).origin;
        if (messageOrigin !== expectedOrigin) {
          console.error(`Got message from origin ${messageOrigin}; expected it from ${expectedOrigin}`);
          return;
        }
        const message = typeof e.data === 'string' ? JSON.parse(e.data) : e.data;
        if (message.type === 'setWidgetHeight') {
          this.iframeHeight = message.value;
        } else if (message.type === 'updateProductSettings') {
          console.log('Update', message);
        }
      };

      addEventListener('message', handler);
      this.$once('hook:beforeDestroy', () => {
        removeEventListener('message', handler);
      });
    },
  },
});
