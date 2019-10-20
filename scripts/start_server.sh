#!/bin/bash
#service httpd start

sudo cp -a /var/www/html/AnagramApplication2/vendor/ /var/www/html/AnagramApplication
cd /var/www/html/AnagramApplication
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache