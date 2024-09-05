-- IBX-6592: The state/assign policy shouldn't utilize neither Location nor Subtree limitations
DELETE
FROM "ezpolicy_limitation"
WHERE "ezpolicy_limitation".id IN
      (SELECT "ezpolicy_limitation".id
       FROM "ezpolicy_limitation"
                INNER JOIN "ezpolicy" ON "ezpolicy".id = "ezpolicy_limitation".policy_id
       WHERE "ezpolicy".function_name = 'assign'
         AND "ezpolicy".module_name = 'state'
         AND "ezpolicy_limitation".identifier IN ('Node', 'Subtree'));
DELETE
FROM "ezpolicy_limitation_value"
WHERE "ezpolicy_limitation_value".id IN
      (SELECT "ezpolicy_limitation_value".id
       FROM "ezpolicy_limitation_value"
                LEFT JOIN "ezpolicy_limitation" ON "ezpolicy_limitation".id = "ezpolicy_limitation_value".limitation_id
       WHERE "ezpolicy_limitation".id IS NULL);
