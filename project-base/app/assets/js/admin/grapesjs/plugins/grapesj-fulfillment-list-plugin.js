import grapesjs from 'grapesjs';
import Translator from 'bazinga-translator';

export default grapesjs.plugins.add('fulfillment-list', (editor) => {
    const dataListType = 'data-list-type';
    const dataFulfillmentListEvent = 'change:attributes:data-list-type';
    let shippingItems = null;
    let paymentItems = null;

    const getFulfillmentList = () => {
        const response = $.get({
            url: `${window.location.origin}/admin/transport-and-payment/transport-and-payment-list/`,
            async: false
        });

        if (response.status === 200) {
            const lists = response.responseJSON;
            shippingItems = decorateList(lists['shippingItems']);
            paymentItems = decorateList(lists['paymentItems']);
        }
    };

    const decorateList = (listItems) => {
        let html = '<ul>';

        listItems.forEach(function (item) {
            html += '<li>' + item + '<span>999 Kč</span></li>';
        });

        html += '</ul>';
        return html;
    };

    const updateFulfillmentList = (element, listType) => {
        if (shippingItems === null || paymentItems === null) {
            getFulfillmentList();
        }

        const components = element.components();
        const componentCount = components.length;
        for (let i = componentCount - 1; i >= 0; i--) {
            components.remove(components.models[i]);
        }

        if (listType === 'shipping') {
            components.add(shippingItems);
        } else {
            components.add(paymentItems);
        }
    };

    editor.Blocks.add('fulfillmentList', {
        id: 'fulfillment-list',
        label: Translator.trans('Fulfillment list'),
        category: 'Basic',
        media: '<svg xmlns="http://www.w3.org/2000/svg" width="48px" height="48px" viewBox="0 0 576 512"><path d="M867.5,10h-735C64.9,10,10,66.7,10,136.5v727.1C10,933.3,64.9,990,132.5,990h735c67.6,0,122.5-56.7,122.5-126.5V136.5C990,66.7,935.1,10,867.5,10z M928.8,863.5c0,34.9-27.5,63.2-61.3,63.2h-735c-33.8,0-61.3-28.4-61.3-63.2V136.5c0-34.9,27.5-63.2,61.3-63.2h735c33.8,0,61.3,28.4,61.3,63.2V863.5z M806.3,231.3H285.6c-16.9,0-30.6,14.2-30.6,31.6c0,17.4,13.7,31.6,30.6,31.6h520.6c16.9,0,30.6-14.1,30.6-31.6C836.9,245.5,823.2,231.3,806.3,231.3z M806.3,389.4H285.6c-16.9,0-30.6,14.1-30.6,31.6c0,17.4,13.7,31.6,30.6,31.6h520.6c16.9,0,30.6-14.2,30.6-31.6C836.9,403.5,823.2,389.4,806.3,389.4z M806.3,547.4H285.6c-16.9,0-30.6,14.2-30.6,31.6s13.7,31.6,30.6,31.6h520.6c16.9,0,30.6-14.1,30.6-31.6S823.2,547.4,806.3,547.4z M806.3,705.5H285.6c-16.9,0-30.6,14.1-30.6,31.6c0,17.4,13.7,31.6,30.6,31.6h520.6c16.9,0,30.6-14.1,30.6-31.6C836.9,719.6,823.2,705.5,806.3,705.5z M163.1,294.5h61.3v-63.2h-61.3V294.5z M163.1,452.6h61.3v-63.2h-61.3V452.6z M163.1,610.6h61.3v-63.2h-61.3V610.6z M163.1,768.7h61.3v-63.2h-61.3V768.7z"/></svg>',
        content: {
            type: 'fulfillment-list'
        }
    });

    editor.DomComponents.addType('fulfillment-list', {
        isComponent: (element) => element.classList && element.classList.contains('gjs-fulfillment-list'),
        model: {
            init () {
                updateFulfillmentList(this, this.getAttributes()[dataListType]);

                this.on(dataFulfillmentListEvent, (element, params) => {
                    updateFulfillmentList(element, params);
                });
            },
            defaults: {
                attributes: {
                    [dataListType]: 'shipping',
                    class: ['gjs-fulfillment-list']
                },
                droppable: false,
                components: shippingItems,
                traits: [
                    {
                        type: 'select',
                        name: dataListType,
                        label: Translator.trans('Type of fulfillment'),
                        options: [
                            {
                                id: 'shipping',
                                label: Translator.trans('Shipping')
                            },
                            {
                                id: 'payment',
                                label: Translator.trans('Payment')
                            }
                        ]
                    }
                ]
            }
        }
    });
});
