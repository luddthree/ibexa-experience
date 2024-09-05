-- IBX-3333: Segment user map is not cleaned up after removing segments
DELETE FROM "ibexa_segment_user_map" su
    WHERE NOT EXISTS (
        SELECT FROM "ibexa_segments" s WHERE s.id = su.segment_id
    );
