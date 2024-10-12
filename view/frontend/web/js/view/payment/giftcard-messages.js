define([
    'Magento_Ui/js/view/messages',
    'Bydn_Giftcard/js/model/payment/giftcard-messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});
