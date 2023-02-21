# -*- mode: ruby -*-
# vi: set ft=ruby :

project_name = "rewindradio"
project_url = "rewind.radio"

Vagrant.configure("2") do |config|
  config.vm.box = "debian/bullseye64"
  config.vm.network "private_network", ip: "192.168.33.25"

  # Configure a specific port to forward to the guest machine
  config.vm.network "forwarded_port", guest: 80, host: 8080

  config.vm.hostname = project_name+"-vm"

  # Remove the default sync folder
config.vm.synced_folder ".", "/vagrant", disabled: true
# Synced folders
  config.vm.synced_folder "./", "/var/www/#{project_name}" 
  
  config.vm.provider "virtualbox" do |vb|
    vb.name = project_name+"-vm"
    vb.memory = "1024"
    vb.cpus = 1
    vb.customize ["modifyvm", :id, "--ioapic", "on"]
    vb.customize ["modifyvm", :id, "--hwvirtex", "on"]
    vb.customize ["modifyvm", :id, "--vtxvpid", "on"]
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.gui = false
  end
  
  config.vm.provision "shell", inline: <<-SHELL
  sudo apt-get update
  sudo apt-get install -y apache2 curl -y
  sudo a2enmod rewrite
  sudo service apache2 restart

touch /etc/apache2/sites-available/#{project_name}.conf

 # Create directories and virtual hosts for the projects
    mkdir /var/www/#{project_name}

    echo "<VirtualHost *:80>
            ServerName #{project_url}
            DocumentRoot /var/www/#{project_name}/public
            <Directory /var/www/#{project_name}>
                AllowOverride All
                Order allow,deny
                Allow from all
            </Directory>
        </VirtualHost>" > /etc/apache2/sites-available/#{project_name}.conf
sudo a2ensite #{project_name}.conf
sudo service apache2 reload

sudo apt update && sudo apt upgrade -y 
sudo apt install -y lsb-release ca-certificates apt-transport-https software-properties-common gnupg2 -y
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/sury-php.list
sudo wget -qO - https://packages.sury.org/php/apt.gpg | sudo gpg --no-default-keyring --keyring gnupg-ring:/etc/apt/trusted.gpg.d/debian-php-8.gpg --import
sudo chmod 644 /etc/apt/trusted.gpg.d/debian-php-8.gpg
sudo apt update && sudo apt upgrade -y
sudo apt install php8.1 -y
sudo apt install php8.1-cli php8.1-mbstring php8.1-xml php8.1-common php8.1-curl php8.1-mysql -y
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer -y
    
    sudo apt-get install git nodejs npm -y
    sudo apt-get install apt-transport-https curl -y
sudo curl -o /etc/apt/trusted.gpg.d/mariadb_release_signing_key.asc 'https://mariadb.org/mariadb_release_signing_key.asc'
sudo sh -c "echo 'deb https://ftp.osuosl.org/pub/mariadb/repo/10.6/debian bullseye main' >>/etc/apt/sources.list"
sudo apt-get update
# sudo apt-get install mariadb-server

  SHELL
end