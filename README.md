# Magento 2 Giftcard Extension

This Magento 2 extension provides comprehensive gift card functionality, allowing customers to easily purchase and redeem gift cards, whether for personal use or as a thoughtful gift for special occasions. Store owners can effortlessly create, manage, and track gift cards, ensuring a smooth process for both customers and administrators.

With flexible configuration options, such as customizable card values and expiration times, the extension offers great versatility to meet diverse business needs. This user guide will walk you through the installation, setup, and usage of the extension, ensuring you get the most out of this feature for your online store.

## Features

- Creation gift cards via the admin panel.
- Integration of gift card functionality into the shopping cart.
- Integration of gift card functionality into the checkout page.
- Email notifications with customizable templates for sending gift card information to customers.

# Configuration

Access the configuration going to:

    Stores => Configuration => Sales => Gift cards (by DN).

Configuration has 4 sections.

## General configuration

**Enable**. This option allows you to completely enable or disable the extension.
**Card default amounts**. Set the default amounts for gift cards. These values can be modified when creating a gift card. See the section "Creating a Gift Card" for more details.

## Email configuration

**Send gift card by email**. Allows customers to send the gift card code to a friend via email. If this option is disabled, the gift card will be sent to the purchaser.
**Email sender**. Defines the identity of the sender for the gift card email.
**Gift card email template**. The email template used when sending the gift card code to the recipient.

Please make sure to customize the email template according to your requirements in:

    Marketing => Email Templates

## Expiration

**Single use**. If enabled, customers can only apply the gift card once, even if there is a remaining balance on the card.
**Enable gift card expiration**. Allows you to enable expiration for gift cards.
**Expiration time (in days)**. Set the number of days after which the gift card will expire.

## Discounts

**Prevent purchasing gift card with another gift card**. If enabled, customers will not be allowed to redeem a gift card to purchase another. This is necessary if you want to apply discounts to gift cards.
**Prevent applying discounts on gift card products**. Enable this option to ensure that customers cannot purchase a gift card with any discounts applied.

## Creating a gift card

Creating a gift card product is as simple as creating any other Magento product.

Just go to:

    Catalog => Products

And select “Gift card“ from the dropdown “Add Product”.

<img alt="Add gift card product" width="100%" src="https://github.com/danidnm/ByDN-Magento-Giftcard-Extension/blob/master/docs/images/add-product.png"/>

Set the common data for the gift card as you would for any other product (name, attribute set, SKU, stock configuration, categories, descriptions, etc.). Set the price to 0.

In the "Customizable Options" section, you will see several custom fields. Pay special attention to the "Card Amount" field, where you can define the available gift card values.

The remaining fields will be required from the customer when purchasing a gift card, enabling the extension to send the gift card to the recipient via email.

**Important: Do not modify the SKU code of the custom options.**

See custom options configuration screenshot in the next page.

Once you create and configure your gift card, you should see something like this in the frontend.

## Applying a gift card

The extension provides two fields for customers to apply and redeem their gift card amount: one on the cart page and another on the checkout payment page.

## Tracking gift cards in the backoffice

If you want to see a list of gift cards purchased and their current status, balance and movements, in the backoffice, go to:

    Marketing => Gift card list.

You will see the most important information directly in the listing, but for more details, you can click the “Edit” link (refer to the screenshot on the next page).

On this screen, you can:

- **Modify the gift card data**. Some fields (such as amounts) are locked to protect data integrity and ensure consistency with orders, balances, etc.
- **Enable or disable the gift card**.
- **Delete the gift card if necessary**. Be careful, as deleting it will result in the loss of balance tracking for that gift card. It is recommended to disable the gift card rather than delete it, especially if it was purchased and generated from an order.
- **Adjust the email send date**. You can modify when the gift card will be sent by email.
- **Change the expiration date**. This is particularly useful if you need to extend it for a specific customer.
- **See detailed balance movements**.

## Creating a gift card code manually

From the back office, there is also the option to manually create a new gift card code, which can be useful in certain situations to reward users who have experienced issues or to correct errors when purchasing a gift card from the frontend.

To do so, just go to the gift card list and press the button “Create gift card” on the top right (see section “Tracking gift cards in the backoffice”)

## Having problems

Contact me at soy@solodani.com

