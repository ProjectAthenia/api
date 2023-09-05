#!/usr/bin/env bash

sudo adduser vagrant --disabled-password --gecos "Vagrant User"
sudo usermod -aG sudo vagrant
sudo echo "vagrant ALL=NOPASSWD: ALL" >> /etc/sudoers
mkdir /home/vagrant/.ssh
cat /tmp/key.pub >> /home/vagrant/.ssh/authorized_keys
chgrp -R vagrant /home/vagrant/.ssh
chown -R vagrant /home/vagrant/.ssh
sudo add-apt-repository ppa:deadsnakes/ppa -y

sudo apt-get update

sudo apt-get install python3.7 -y