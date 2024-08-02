var config = {
    map: {
        '*': {
            giftcardCode: 'Bydn_Giftcard/js/giftcard-codes'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-payment-method': {
                'Bydn_Giftcard/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor': {
                'Bydn_Giftcard/js/model/shipping-save-processor-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Bydn_Giftcard/js/model/place-order-mixin': true
            }
        }
    }
};
