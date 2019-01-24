# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.synced_folder "./", "/vagrant", :owner => "vagrant", :group => "www-data", :mount_options => ["dmode=777","fmode=666"]
  config.vm.network "private_network", ip: "192.168.5.26"
  config.vm.provision :shell, path: "bootstrap.sh"

  config.vm.provider "virtualbox" do |v|
     v.memory = 1024
     v.cpus = 2
     v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
  end

end