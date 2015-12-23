apt-get install -y apache2
apt-get install -y php5
apt-get install -y git
rm -rf /var/www
ln -fs /vagrant /var/www
cd /var/www/html/composer
curl -sS https://getcomposer.org/installer | php
sudo apt-get update
sudo apt-get install php5-curl
sudo service apache2 restart
php composer.phar install
