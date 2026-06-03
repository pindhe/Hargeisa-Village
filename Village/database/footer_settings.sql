-- Run once in phpMyAdmin if footer settings are missing (existing installs)
USE hargeisa_village;

INSERT INTO settings (setting_key, setting_value, description) VALUES
('footer_about', '', 'Footer description (empty = use tagline)'),
('footer_show_hours', '1', 'Show hours in footer (1=yes, 0=no)'),
('footer_copyright', '', 'Extra copyright text (optional)')
ON DUPLICATE KEY UPDATE description = VALUES(description);
