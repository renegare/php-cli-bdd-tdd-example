Feature: Applying promotional offers to an order
    In order to research best marketing strategies
    As a Marketing Person
    I need to test applying promotional offers on orders

    Scenario: The cheapest product is given for free with "3 for the price of 2" offer
        Given the "3 for the price of 2" offer is enabled
        When the following products are put on the order
        | Category | Title | Price |
        | Lipstick | Rimmel Lasting Finish Lipstick 4g | 4.99 |
        | Lipstick | bareMinerals Marvelous Moxie Lipstick 3.5g | 13.95 |
        | Lipstick | Rimmel Kate Lasting Finish Matte Lipstick | 5.49 |
        Then I should get the "Rimmel Lasting Finish Lipstick 4g" for free
        And the order total should be "19.44"

    Scenario: "3 for the price of 2" is disabled
        Given the "3 for the price of 2" offer is disabled
        When the following products are put on the order
        | Category | Title | Price |
        | Lipstick | Rimmel Lasting Finish Lipstick 4g | 4.99 |
        | Lipstick | bareMinerals Marvelous Moxie Lipstick 3.5g | 13.95 |
        | Lipstick | Rimmel Kate Lasting Finish Matte Lipstick | 5.49 |
        Then I should not get anything for free
        And the order total should be "24.43"

    Scenario: "Buy Shampoo & get Conditioner for 50% off" offer
        Given the "Buy Shampoo & get Conditioner for 50% off" offer is enabled
        When the following products are put on the order
        | Category | Title | Price |
        | Shampoo | Sebamed Anti-Dandruff Shampoo 200ml | 4.99 |
        | Conditioner | L'Oréal Paris Hair Conditioner 250ml | 5.50 |
        Then I should get a 50% discount on "L'Oréal Paris Hair Conditioner 250ml"
        And the order total should be "7.74"
