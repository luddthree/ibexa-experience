-- IBX-6592: The state/assign policy shouldn't utilize neither Location nor Subtree limitations
DELETE l
FROM `ezpolicy_limitation` l
INNER JOIN `ezpolicy` p ON p.id = l.policy_id
WHERE p.function_name = 'assign'
  AND p.module_name = 'state'
  AND l.identifier IN ('Node', 'Subtree');

DELETE lv
FROM `ezpolicy_limitation_value` lv
LEFT JOIN `ezpolicy_limitation` ON `ezpolicy_limitation`.id = lv.limitation_id
WHERE `ezpolicy_limitation`.id IS NULL;
