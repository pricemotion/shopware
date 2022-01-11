import template from './sw-product-detail-pricemotion.html.twig';

const { Component } = Shopware;
const { mapState } = Component.getComponentHelper();
const cacheBuster = Date.now();
let widgetUrlCache;

Component.register('sw-product-detail-pricemotion', {
  template,
  metaInfo() {
    return {
      title: 'Pricemotion',
    };
  },
  inject: ['pricemotionApiService', 'repositoryFactory'],
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
  computed: {
    ...mapState('swProductDetail', ['product']),
    ean() {
      return (this.product?.ean || '').replace(/^\s+|\s+$/g, '');
    },
  },
  watch: {
    url() {
      if (!this.url) {
        this.loading = false;
      }

      this.loading = true;

      setTimeout(() => {
        this.loading = false;
      }, 5e3);

      this.$nextTick(() => {
        try {
          this.$refs.iframe.contentWindow.postMessage({ type: 'updateWidgetHeight' }, '*');
        } catch (e) {}
      });
    },
  },
  created() {
    this.installMessageHandler();
  },
  async mounted() {
    const { url: baseUrl, token } = await this.getWidgetUrl();
    const url = new URL(baseUrl);
    url.search = new URLSearchParams({
      t: cacheBuster,
      assetVersion: '1.3',
    }).toString();
    url.hash = JSON.stringify({
      token,
      ean: this.ean,
      settings: this.getExtension().settings,
    });
    this.url = url.toString();
  },
  methods: {
    installMessageHandler() {
      const handler = (e) => {
        if (!this.$refs.iframe || e.source !== this.$refs.iframe.contentWindow) {
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
          if (message.isValid) {
            this.getExtension().settings = message.value;
          }
        }
      };

      addEventListener('message', handler);
      this.$once('hook:beforeDestroy', () => {
        removeEventListener('message', handler);
      });
    },
    getExtension() {
      return (this.product.extensions.pricemotion ??= this.repositoryFactory
        .create('kibo_pricemotion_product')
        .create());
    },
    getWidgetUrl() {
      const product = this.product;
      if (widgetUrlCache && widgetUrlCache.product === product) {
        return widgetUrlCache.promise;
      }
      const promise = this.pricemotionApiService.getWidgetUrl();
      promise.catch(() => {
        widgetUrlCache = undefined;
      });
      widgetUrlCache = {
        promise,
        product,
      };
      return promise;
    },
  },
});
