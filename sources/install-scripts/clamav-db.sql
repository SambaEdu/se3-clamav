#
# Structure de la table `clamav`
#

CREATE TABLE IF NOT EXISTS clamav_scan(
  id	INT	AUTO_INCREMENT,
  date	timestamp	NOT NULL,
  directory text NOT NULL,
  summary	mediumtext,
  result	mediumtext,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS clamav_dirs(  
  id INT AUTO_INCREMENT,
  directory varchar(255) NOT NULL,
  frequency text,
  remove tinyint,
  PRIMARY KEY (id),
  UNIQUE (directory)
)  DEFAULT CHARSET=utf8;
    
INSERT into clamav_dirs (directory,frequency,remove) VALUES ('/home','daily','0');
INSERT into clamav_dirs (directory,frequency,remove) VALUES ('/var/se3','daily','0');


