#update OS
yum -y update

#remove folders
rm -rf /var/www/cgi-bin/
rm -rf /var/www/error/
rm -rf /var/www/html/
rm -rf /var/www/icons/

#symlink appropriate files & folders
if [ ! -h "/var/www/ffdc/public/assets" ]; then
    cd /var/www/ffdc/public/; ln -s /var/www/assets/ffdc/ assets
fi
if [ ! -h "/var/www/blog/wordpress/wp-content/uploads" ]; then
    cd /var/www/blog/wordpress/wp-content/; ln -s /var/www/assets/blog/ uploads
fi
if [ ! -h "/var/www/blog/server.ini" ]; then
    cd /var/www/blog/; ln -s ../ffdc/server.ini server.inißß
fi
