# Correctif pour les bases de données de clamav

ALTER TABLE se3db.clamav_dirs CONVERT TO CHARACTER SET utf8;
ALTER TABLE se3db.clamav_scan CONVERT TO CHARACTER SET utf8;