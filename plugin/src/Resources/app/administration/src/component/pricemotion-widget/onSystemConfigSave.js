const { Application } = Shopware;

const listeners = [];

Application.addServiceProviderDecorator('systemConfigApiService', (systemConfigApiService) => {
  return new Proxy(systemConfigApiService, {
    get(target, prop, receiver) {
      const v = Reflect.get(target, prop, receiver);
      if (prop === 'batchSave') {
        return function (...args) {
          const result = v.apply(this, args);
          result.finally(() => {
            listeners.forEach((l) => {
              l();
            });
          });
          return result;
        };
      }
      return v;
    },
  });
});

export default function onSystemConfigSave(fn) {
  listeners.push(fn);
}
