#
# Regular cron jobs for the se3-clamav package
#
7 21	* * *	root	/usr/bin/freshclam --quiet
7 22	 * * 1	root /usr/sbin/se3-clamav lundi
7 22	 * * 2 	root /usr/sbin/se3-clamav mardi
7 22	 * * 3 	root /usr/sbin/se3-clamav mercredi
7 22	 * * 4 	root /usr/sbin/se3-clamav jeudi
7 22	 * * 5 	root /usr/sbin/se3-clamav vendredi
7 22	 * * 6 	root /usr/sbin/se3-clamav samedi
7 22	 * * 7 	root /usr/sbin/se3-clamav dimanche
7 22	 * * 0-5 root /usr/sbin/se3-clamav daily
7 22	* *  6	root /usr/sbin/se3-clamav weekly
