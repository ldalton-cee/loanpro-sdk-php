sudo apt-get update
sudo apt-get install -y php5-dev php-pear
sudo aptitude install php5-xdebug

OUT=$(sudo find / -name xdebug.so)
sudo cp php.ini php2.ini
for y in `ls *2.ini`;
do sed "s@XDEBUG_LOC@$OUT@g" $y > temp; mv temp $y;
done

sudo cp php2.ini /etc/php5/apache2/php.ini
sudo service apache2 restart
sudo rm php2.ini