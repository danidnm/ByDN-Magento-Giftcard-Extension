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




