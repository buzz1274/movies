#yumrepo{'remi-php':
#    mirrorlist => 'http://rpms.famillecollet.com/enterprise/$releasever/remi/mirror',
#    enabled => 1,
#    priority => 1,
#    exclude => "mysql",
#    gpgcheck => 1,
#    gpgkey => 'file:///etc/pki/rpm-gpg/RPM-GPG-KEY-remi',
#}

#yumrepo{'remi-php55':
#    mirrorlist => 'http://rpms.famillecollet.com/enterprise/$releasever/php55/mirror',
#    enabled => 1,
#    priority => 1,
#    exclude => "mysql",
#    gpgcheck => 1,
#    gpgkey => 'file:///etc/pki/rpm-gpg/RPM-GPG-KEY-remi',
#}

#class { 'yum::repo::remi': }
#class { 'yum::repo::remi_php55':}

#$ more remi.repo
#[remi]
#name=Les RPM de remi pour Enterpise Linux $releasever - $basearch
#mirrorlist=http://rpms.famillecollet.com/enterprise/$releasever/remi/mirror
#enabled=1
#gpgcheck=1
#gpgkey=file:///etc/pki/rpm-gpg/RPM-GPG-KEY-remi
#priority=1

#[09:35 AM]-[vagrant@ffdcdev24]-[/etc/yum.repos.d]
#$ more remi-php55.repo
#[remi-php55]
#name=Les RPM de remi pour Enterpise Linux $releasever - $basearch - PHP 5.5
#mirrorlist=http://rpms.famillecollet.com/enterprise/$releasever/php55/mirror
#enabled=1
#gpgcheck=1
#gpgkey=file:///etc/pki/rpm-gpg/RPM-GPG-KEY-remi
#priority=1
