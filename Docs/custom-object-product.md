Introduction
------------

This Plugin creates an Product custom object. Please find the details as follows,

## Custom object

1. Name: `Product`
2. Alias: `product`
3. Fields:

| Label           | Alias         | Type     | Unique Identifier | Required | 
|-----------------|---------------|----------|-------------------|----------|
| Name            | name          | Text     | :x:               | :x:      |
| Description     | description   | TextArea | :x:               | :x:      |
| Link            | link          | URL      | :x:               | :x:      |
| Thumbnail Image | thumbnail     | URL      | :x:               | :x:      |
| SKU             | sku           | Text     | :x:               | :x:      |
| Quantity        | quantity      | Text     | :x:               | :x:      |
| Price           | price         | Text     | :x:               | :x:      |
| Checkout Link   | checkout_link | URL      | :x:               | :x:      |
| Type            | type          | Select   | :x:               | :x:      |

The field Type has the following options,

| Label     | Value     |
|-----------|-----------|
| Abandoned | abandoned |
| Wishlist  | wishlist  |
| Review    | review    |

The above types will differentiate the product and there usage.

## [Usage](#usage)

1. Setup an API
2. Send the payload as the following code snippet from integration,
```curl
curl --location '<mautic-innstance-host>/api/contacts/new?includeCustomObjects=true' \
--header 'Cache-Control: no-cache' \
--header 'Content-Type: application/json' \
--header 'token: <token>' \
--header 'Authorization: <Type> <Authorization_code>' \
--data-raw '{
    "email": "firstname_lastname@example.com",
    "customObjects": {
        "data": [
            {
                "alias": "product",
                "data": [
                    {
                        "name": "<AP One>",
                        "attributes": {
                            "type: "abandoned",
                            "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                            "link": "https://www.e-commerce-example.com/path/to/product/one",
                            "thumbnail": "https://www.e-commerce-example.com/path/to/image/product/one.jpg",
                            "sku": "1",
                            "quantity": "1",
                            "price": "$ 999",
                            "checkout_link": "https://www.e-commerce-example.com/path/to/checkout-cart"
                        }
                    }
                ]
            },
            {
                "alias": "product",
                "data": [
                    {
                        "name": "Product two",
                        "attributes": {
                            "type: "wishlist",
                            "description": "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.",
                            "link": "https://www.e-commerce-example.com/path/to/product/two",
                            "thumbnail": "https://www.e-commerce-example.com/path/to/image/product/two.jpg",
                            "sku": "2",
                            "quantity": "1",
                            "price": "$ 999",
                            "checkout_link": "https://www.e-commerce-example.com/path/to/checkout-cart"
                        }
                    }
                ]
            },
        ]
    }
}'
```