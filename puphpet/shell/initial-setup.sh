#!/bin/bash

VAGRANT_CORE_FOLDER=$(echo "$1")

OS=$(/bin/bash "${VAGRANT_CORE_FOLDER}/shell/os-detect.sh" ID)
CODENAME=$(/bin/bash "${VAGRANT_CORE_FOLDER}/shell/os-detect.sh" CODENAME)

if [[ ! -d /.puphpet-stuff ]]; then
    mkdir /.puphpet-stuff

    echo "${VAGRANT_CORE_FOLDER}" > "/.puphpet-stuff/vagrant-core-folder.txt"

    cat "${VAGRANT_CORE_FOLDER}/shell/self-promotion.txt"
    echo "Created directory /.puphpet-stuff"
fi

if [[ ! -f /.puphpet-stuff/initial-setup-repo-update ]]; then
    echo "Running initial-setup yum update"
    yum install yum-plugin-fastestmirror -y >/dev/null
    yum check-update -y >/dev/null
    yum -y update
    echo "Finished running initial-setup yum update"

    echo "Updating to Ruby 1.9.3"
    yum install centos-release-SCL >/dev/null
    yum remove ruby >/dev/null
    yum install ruby193 facter hiera ruby193-ruby-irb ruby193-ruby-doc ruby193-rubygem-json ruby193-libyaml ruby-rgen >/dev/null
    gem update --system >/dev/null
    gem install haml >/dev/null
    echo "Finished updating to Ruby 1.9.3"

    echo "Installing basic development tools (CentOS)"
    yum -y groupinstall "Development Tools" >/dev/null
    echo "Finished installing basic development tools (CentOS)"
    touch /.puphpet-stuff/initial-setup-repo-update
fi
