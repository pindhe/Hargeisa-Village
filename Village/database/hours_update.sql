-- Update operating hours: 7 days, morning 8:30 AM - 3:00 PM, afternoon 4:30 PM - 11:00 PM
-- Run in phpMyAdmin on database hargeisa_village

USE hargeisa_village;

INSERT INTO settings (`key`, `value`, description) VALUES
('hours_morning', '8:30 AM - 3:00 PM', 'Morning hours (all days)'),
('hours_afternoon', '4:30 PM - 11:00 PM', 'Afternoon hours (all days)')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), description = VALUES(description);

UPDATE settings SET `value` = '8:30 AM - 3:00 PM · 4:30 PM - 11:00 PM'
WHERE `key` IN (
    'hours_monday', 'hours_tuesday', 'hours_wednesday', 'hours_thursday',
    'hours_friday', 'hours_saturday', 'hours_sunday'
);
