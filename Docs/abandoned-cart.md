Introduction
------------

This Plugin creates an Abandoned cart custom object. Please find the details as follows,

## Custom object

1. Name: `Abandoned Product`
2. Alias: `abandoned_product`
3. Fields:

| Label           | Alias         | Type     | Unique Identifier  | Required           | 
|-----------------|---------------|----------|--------------------|--------------------|
| Name            | name          | Text     | :x:                | :x:                |
| Description     | description   | TextArea | :x:                | :x:                |
| Link            | link          | URL      | :x:                | :x:                |
| Thumbnail Image | thumbnail     | URL      | :x:                | :x:                |
| SKU             | sku           | Text     | :white_check_mark: | :white_check_mark: |
| Quantity        | quantity      | Text     | :x:                | :x:                |
| Price           | price         | Text     | :x:                | :x:                |
| Checkout Link   | checkout_link | URL      | :x:                | :x:                | 

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
                "alias": "abandoned_product",
                "data": [
                    {
                        "name": "<AP One>",
                        "attributes": {
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
                "alias": "abandoned_product",
                "data": [
                    {
                        "name": "Product two",
                        "attributes": {
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