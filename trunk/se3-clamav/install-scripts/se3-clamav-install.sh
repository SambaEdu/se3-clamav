#!/bin/bash    
# recuperation des param de la base
if [ -e /var/www/se3/includes/config.inc.php ]; then
        dbhost=`cat /var/www/se3/includes/config.inc.php | grep "dbhost=" | cut -d = -f 2 |cut -d \" -f 2`
        dbname=`cat /var/www/se3/includes/config.inc.php | grep "dbname=" | cut -d = -f 2 |cut -d \" -f 2`
        dbuser=`cat /var/www/se3/includes/config.inc.php | grep "dbuser=" | cut -d = -f 2 |cut -d \" -f 2`
        dbpass=`cat /var/www/se3/includes/config.inc.php | grep "dbpass=" | cut -d = -f 2 |cut -d \" -f 2`
else
        echo "Fichier de conf inaccessible"
        exit 1
fi
# creation de la table pour les scan
    echo "creating database parts"
mysql -h $dbhost $dbname -u $dbuser -p$dbpass < /var/cache/se3_install/se3-clamav/clamav-db.sql >/dev/null 2>&1