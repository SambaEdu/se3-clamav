#!/usr/bin/make -f
# Makefile for se3_clamav

all:

install:
    # Add here commands to install the package into debian/slis-core.
    # Install des fichiers de configuration et de cron.
	cp -R www/* $(DESTDIR)/var/www/se3/clamav
	cp -R menu/* $(DESTDIR)/var/www/se3/includes/menu.d/
	cp -R install-scripts/* $(DESTDIR)/var/cache/se3_install/se3-clamav/       
	cp -R sbin/* $(DESTDIR)/usr/sbin

clean:
