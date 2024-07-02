# Magento 2 Giftcard Extension

Welcome to the Magento 2 Giftcard Extension repository! This module adds robust gift card functionality to your Magento e-commerce platform, allowing customers to purchase, redeem, and manage gift cards seamlessly. It integrates deeply with Magento's existing structures, ensuring that gift cards are a first-class feature in the e-commerce experience.

## Features

- Creation and management of gift cards via the admin panel.
- Integration of gift card functionality into the shopping cart.
- Application of gift card discounts to cart totals.
- Email notifications with customizable templates for sending gift card information to customers.
- API interfaces for various gift card operations.
- Custom event handling for gift card-related actions.

## Installation

To install this module, follow these steps:

1. Clone the repository to your Magento 2 `app/code` directory:
    ```bash
    git clone https://github.com/your-repo/ByDN-Magento-Giftcard-Extension app/code/Bydn/Giftcard
    ```
2. Enable the module:
    ```bash
    php bin/magento module:enable Bydn_Giftcard
    ```
3. Run the setup upgrade command:
    ```bash
    php bin/magento setup:upgrade
    ```
4. Deploy static content:
    ```bash
    php bin/magento setup:static-content:deploy
    ```
5. Clear the cache:
    ```bash
    php bin/magento cache:clean
    ```

## Configuration

After installation, you can configure the module in the Magento Admin panel:

1. Go to the Magento Admin panel.
2. Navigate to `Stores` > `Configuration` > `Giftcard (by DN)`.
3. Enable the module and configure the settings according to your preferences.
4. Save the configuration.

## Usage

### Admin Panel

1. **Manage Gift Cards:**
   - Navigate to `Marketing > Giftcard > Giftcard list` to create, view, and manage gift cards.
   
2. **Configure Email Templates:**
   - Customize the email templates used for sending gift card information to customers.

### Frontend

1. **Apply Gift Cards:**
   - Customers can apply gift cards in the cart by entering the gift card code in the provided field.
   
2. **Gift Card Balance:**
   - The applied gift card balance will be deducted from the order total.

## Contributing

We welcome contributions to this project. If you have an idea for an improvement or find a bug, please open an issue or submit a pull request.

## License

This project is licensed under the MIT License.

## Acknowledgments

- The Magento community for their continuous support and contributions.

Thank you for using the Magento 2 Giftcard Extension!
