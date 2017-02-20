# Correctif pour les bases de données de clamav

ALTER TABLE se3db.clamav_dirs CONVERT TO CHARACTER SET utf8;
ALTER TABLE se3db.clamav_scan CONVERT TO CHARACTER SET utf8;

ALTER TABLE se3db.clamav_dirs MODIFY frequency varchar(255);
ALTER TABLE se3db.clamav_scan MODIFY directory varchar(255);
ALTER TABLE se3db.clamav_scan MODIFY summary blob;
ALTER TABLE se3db.clamav_scan MODIFY result blob;
