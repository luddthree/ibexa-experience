-- IBX-3042: Invalid values for Custom Price Rule for small product prices
ALTER TABLE ibexa_product_specification_price
    ADD COLUMN custom_price_rule DECIMAL(6,2) DEFAULT NULL;

UPDATE ibexa_product_specification_price ipsp1
    INNER JOIN ibexa_product_specification_price_customer_group ipspcg on ipsp1.id = ipspcg.id
    INNER JOIN ibexa_customer_group icg on ipspcg.customer_group_id = icg.id
    SET ipsp1.custom_price_rule = (ipsp1.custom_price_amount - ipsp1.amount) * 100 / ipsp1.amount
WHERE ipsp1.amount <> ipsp1.custom_price_amount
  AND round(ipsp1.amount) <> 0
  AND round(icg.global_price_rate) = 0;
