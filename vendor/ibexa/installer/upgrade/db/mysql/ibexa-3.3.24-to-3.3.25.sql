-- IBX-3333: Segment user map is not cleaned up after removing segments
DELETE `su` FROM `ibexa_segment_user_map` AS `su`
    LEFT JOIN `ibexa_segments` AS `s` ON `su`.`segment_id` = `s`.`id`
    WHERE `s`.`id` IS NULL;
