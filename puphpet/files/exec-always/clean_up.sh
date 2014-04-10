#!/bin/sh
#update OS
yum -y update

#remove folders
rm -rf /var/www/html/
rm -rf /var/www/cgi-bin/
rm -rf /var/www/error/
rm -rf /var/www/html/
rm -rf /var/www/icons/
rm -rf /var/www/movies.zz50.co.uk/cgi-bin/
rm -rf /var/www/movies.zz50.co.uk/error/
rm -rf /var/www/movies.zz50.co.uk/html/
rm -rf /var/www/movies.zz50.co.uk/icons/

#update/install cakephp
if [ ! -d "/usr/local/cakephp" ]; then
    mkdir -p /usr/local/cakephp
    cd /usr/local/cakephp && git clone https://github.com/cakephp/cakephp.git .
else
    cd /usr/local/cakephp && git pull
fi
