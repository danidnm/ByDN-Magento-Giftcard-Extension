define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    $.widget('mage.giftcardCode', {
        options: {
        },

        /** @inheritdoc */
        _create: function () {
            this.giftcardCode = $(this.options.giftcardCodeSelector);
            this.removeGiftcard = $(this.options.removeGiftcardSelector);

            $(this.options.applyButton).on('click', $.proxy(function () {
                this.giftcardCode.attr('data-validate', '{required:true}');
                this.removeGiftcard.attr('value', '0');
                $(this.element).validation().trigger('submit');
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function () {
                this.giftcardCode.removeAttr('data-validate');
                this.removeGiftcard.attr('value', '1');
                this.element.trigger('submit');
            }, this));
        }
    });

    return $.mage.giftcardCode;
});
