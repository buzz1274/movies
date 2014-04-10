#!/bin/sh
#update OS
yum -y update

#remove folders
rm -rf /var/www/cgi-bin/
rm -rf /var/www/error/
rm -rf /var/www/html/
rm -rf /var/www/icons/
