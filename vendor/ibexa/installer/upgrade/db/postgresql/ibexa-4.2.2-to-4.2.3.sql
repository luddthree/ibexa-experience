-- IBX-3042: Invalid values for Custom Price Rule for small product prices
ALTER TABLE "ibexa_product_specification_price" ADD COLUMN "custom_price_rule" DECIMAL(6,2) DEFAULT NULL;

UPDATE ibexa_product_specification_price ipsp1
SET custom_price_rule = (custom_price_amount - amount) * 100 / amount
WHERE
ipsp1.id IN
    (
        SELECT ipsp2.id
        FROM ibexa_product_specification_price ipsp2
            JOIN ibexa_product_specification_price_customer_group ipspcg on ipsp2.id = ipspcg.id
            JOIN ibexa_customer_group icg on ipspcg.customer_group_id = icg.id
        WHERE round(icg.global_price_rate) = 0
    )
AND ipsp1.amount <> ipsp1.custom_price_amount
AND round(ipsp1.amount) <> 0;
