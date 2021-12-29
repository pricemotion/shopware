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
      iframeHeight: 500,
      loading: true,
    };
  },
  async mounted() {
    console.log('Pricemotion: Retrieve widget URL for product', this.productId);
    this.url = await this.pricemotionApiService.getWidgetUrl(this.productId);
    if (this.url) {
      this.installMessageHandler();
      setTimeout(() => {
        this.loading = false;
      }, 5e3);
    } else {
      this.loading = false;
    }
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
        if (e.source !== this.$refs.iframe.contentWindow) {
          return;
        }
        const messageOrigin = new URL(e.origin).origin;
        const expectedOrigin = new URL(this.url).origin;
        if (messageOrigin !== expectedOrigin) {
          throw new Error(`Got message from origin ${messageOrigin}; expected it from ${expectedOrigin}`);
        }
        const message = typeof e.data === 'string' ? JSON.parse(e.data) : e.data;
        if (message.type === 'setWidgetHeight') {
          this.iframeHeight = message.value;
          this.loading = false;
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
