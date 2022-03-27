import template from './pricemotion-widget.html.twig';
import onSystemConfigSave from './onSystemConfigSave.js';

const { Component } = Shopware;
const cacheBuster = Date.now();
let appUrlPromise;

onSystemConfigSave(() => {
  if (!appUrlPromise) return;
  console.log('[Pricemotion] System config was saved; clearing widget URL cache');
  appUrlPromise = undefined;
});

Component.register('pricemotion-widget', {
  template,
  inject: ['pricemotionApiService'],
  props: {
    path: String,
    params: Object,
  },
  data() {
    return {
      url: null,
      iframeHeight: 500,
      loading: true,
    };
  },
  created() {
    this.installMessageHandler();
  },
  async mounted() {
    const { url: baseUrl, token } = await this.getAppUrl();
    const url = new URL(baseUrl + this.path);
    url.search = new URLSearchParams({
      t: cacheBuster,
      assetVersion: '1.3',
    }).toString();
    url.hash = JSON.stringify({
      token,
      locale: this.$i18n.locale,
      ...this.params,
    });
    this.url = url.toString();
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
        } else {
          this.$emit(`pricemotion:${message.type}`, message);
        }
      };

      addEventListener('message', handler);
      this.$once('hook:beforeDestroy', () => {
        removeEventListener('message', handler);
      });
    },
    getAppUrl() {
      if (appUrlPromise) {
        return appUrlPromise;
      }
      const promise = this.pricemotionApiService.getAppUrl();
      promise.catch(() => {
        appUrlPromise = undefined;
      });
      appUrlPromise = promise;
      return promise;
    },
  },
});
