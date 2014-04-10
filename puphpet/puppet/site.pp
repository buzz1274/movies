## Begin Server manifest

import 'modules/*.pp'

class {'ntp':}

Exec {path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/' ]}

group {'puppet':   ensure => present}
group {'www-data': ensure => present}
group {'www-user': ensure => present}

user {vagrant:
  shell   => '/bin/bash',
  home    => "/home/vagrant",
  ensure  => present,
  groups  => ['www-data', 'www-user'],
  require => [Group['www-data'], Group['www-user']]
}







if $server_values == undef {
  $server_values = hiera('server', false)
}


include 'puphpet'
include 'puphpet::params'

user { ['apache', 'nginx', 'httpd', 'www-data']:
  shell  => '/bin/bash',
  ensure => present,
  groups => 'www-data',
  require => Group['www-data']
}

file { "/home/${::ssh_username}":
    ensure => directory,
    owner  => $::ssh_username,
}

# copy dot files to ssh user's home directory
exec { 'dotfiles':
  cwd     => "/home/${::ssh_username}",
  command => "cp -r /vagrant/puphpet/files/dot/.[a-zA-Z0-9]* /home/${::ssh_username}/ \
              && chown -R ${::ssh_username} /home/${::ssh_username}/.[a-zA-Z0-9]* \
              && cp -r /vagrant/puphpet/files/dot/.[a-zA-Z0-9]* /root/",
  onlyif  => 'test -d /vagrant/puphpet/files/dot',
  returns => [0, 1],
  require => User[$::ssh_username]
}

case $::osfamily {
  # redhat, centos
  'redhat': {
    class { 'yum': extrarepo => ['epel'] }

    class { 'yum::repo::rpmforge': }
    class { 'yum::repo::repoforgeextras': }

    Class['::yum'] -> Yum::Managed_yumrepo <| |> -> Package <| |>

    if defined(Package['git']) == false {
      package { 'git':
        ensure  => latest,
        require => Class['yum::repo::repoforgeextras']
      }
    }

    exec { 'bash_git':
      cwd     => "/home/${::ssh_username}",
      command => "curl https://raw.github.com/git/git/master/contrib/completion/git-prompt.sh > /home/${::ssh_username}/.bash_git",
      creates => "/home/${::ssh_username}/.bash_git"
    }

    exec { 'bash_git for root':
      cwd     => '/root',
      command => "cp /home/${::ssh_username}/.bash_git /root/.bash_git",
      creates => '/root/.bash_git',
      require => Exec['bash_git']
    }

    file_line { 'link ~/.bash_git':
      ensure  => present,
      line    => 'if [ -f ~/.bash_git ] ; then source ~/.bash_git; fi',
      path    => "/home/${::ssh_username}/.bash_profile",
      require => [
        Exec['dotfiles'],
        Exec['bash_git'],
      ]
    }

    file_line { 'link ~/.bash_git for root':
      ensure  => present,
      line    => 'if [ -f ~/.bash_git ] ; then source ~/.bash_git; fi',
      path    => '/root/.bashrc',
      require => [
        Exec['dotfiles'],
        Exec['bash_git'],
      ]
    }

    file_line { 'link ~/.bash_aliases':
      ensure  => present,
      line    => 'if [ -f ~/.bash_aliases ] ; then source ~/.bash_aliases; fi',
      path    => "/home/${::ssh_username}/.bash_profile",
      require => File_line['link ~/.bash_git']
    }

    file_line { 'link ~/.bash_aliases for root':
      ensure  => present,
      line    => 'if [ -f ~/.bash_aliases ] ; then source ~/.bash_aliases; fi',
      path    => '/root/.bashrc',
      require => File_line['link ~/.bash_git for root']
    }

    ensure_packages( ['augeas'] )
  }
}

if $php_values == undef {
  $php_values = hiera('php', false)
}

case $::operatingsystem {
  'redhat', 'centos': {
    if hash_key_equals($php_values, 'install', 1) {
      if $php_values['version'] == '54' {
        #class { 'yum::repo::remi': }
      }
      # remi_php55 requires the remi repo as well
      elsif $php_values['version'] == '55' {
        class { 'yum::repo::remi': }
        class { 'yum::repo::remi_php55': }
      }
    }
  }
}

if !empty($server_values['packages']) {
  ensure_packages( $server_values['packages'] )
}

define add_dotdeb ($release){
   apt::source { $name:
    location          => 'http://packages.dotdeb.org',
    release           => $release,
    repos             => 'all',
    required_packages => 'debian-keyring debian-archive-keyring',
    key               => '89DF5277',
    key               => '89DF5277',
    key_server        => 'keys.gnupg.net',
    include_src       => true
  }
}

## Begin Apache manifest

if $yaml_values == undef {
  $yaml_values = loadyaml('/vagrant/puphpet/config.yaml')
} if $apache_values == undef {
  $apache_values = $yaml_values['apache']
} if $php_values == undef {
  $php_values = hiera('php', false)
} if $hhvm_values == undef {
  $hhvm_values = hiera('hhvm', false)
}

if hash_key_equals($apache_values, 'install', 1) {
  include puphpet::params
  include apache::params

  $webroot_location = $puphpet::params::apache_webroot_location

  exec { "exec mkdir -p ${webroot_location}":
    command => "mkdir -p ${webroot_location}",
    creates => $webroot_location,
  }

  if (downcase($::provisioner_type) in ['virtualbox', 'vmware_fusion'])
    and ! defined(File[$webroot_location])
  {
    file { $webroot_location:
      ensure  => directory,
      mode    => 0775,
      require => [
        Exec["exec mkdir -p ${webroot_location}"],
        Group['www-data']
      ]
    }
  }

  if !(downcase($::provisioner_type) in ['virtualbox', 'vmware_fusion'])
    and ! defined(File[$webroot_location])
  {
    file { $webroot_location:
      ensure  => directory,
      group   => 'www-data',
      mode    => 0775,
      require => [
        Exec["exec mkdir -p ${webroot_location}"],
        Group['www-data']
      ]
    }
  }

  if hash_key_equals($hhvm_values, 'install', 1) {
    $mpm_module           = 'worker'
    $disallowed_modules   = ['php']
    $apache_conf_template = 'puphpet/apache/hhvm-httpd.conf.erb'
    $apache_php_package   = 'hhvm'
  } elsif hash_key_equals($php_values, 'install', 1) {
    $mpm_module           = 'prefork'
    $disallowed_modules   = []
    $apache_conf_template = $apache::params::conf_template
    $apache_php_package   = 'php'
  } else {
    $mpm_module           = 'prefork'
    $disallowed_modules   = []
    $apache_conf_template = $apache::params::conf_template
    $apache_php_package   = ''
  }

  if $::operatingsystem == 'ubuntu'
  and hash_key_equals($php_values, 'install', 1)
  and hash_key_equals($php_values, 'version', 55)
  {
    $apache_version = '2.4'
  } else {
    $apache_version = $apache::version::default
  }

  $apache_settings = merge($apache_values['settings'], {
    'mpm_module'     => $mpm_module,
    'conf_template'  => $apache_conf_template,
    'sendfile'       => $apache_values['settings']['sendfile'] ? { 1 => 'On', default => 'Off' },
    'apache_version' => $apache_version
  })

  create_resources('class', { 'apache' => $apache_settings })

  if $::osfamily == 'redhat' and ! defined(Iptables::Allow['tcp/80']) {
    iptables::allow { 'tcp/80':
      port     => '80',
      protocol => 'tcp'
    }
  }

  if $::osfamily == 'redhat' and ! defined(Iptables::Allow['tcp/443']) {
    iptables::allow { 'tcp/443':
      port     => '443',
      protocol => 'tcp'
    }
  }

  if hash_key_equals($apache_values, 'mod_pagespeed', 1) {
    class { 'puphpet::apache::modpagespeed': }
  }

  if hash_key_equals($apache_values, 'mod_spdy', 1) {
    class { 'puphpet::apache::modspdy':
      php_package => $apache_php_package
    }
  }

  if count($apache_values['vhosts']) > 0 {
    each( $apache_values['vhosts'] ) |$key, $vhost| {
      exec { "exec mkdir -p ${vhost['docroot']} @ key ${key}":
        command => "mkdir -p ${vhost['docroot']}",
        creates => $vhost['docroot'],
      }

      if (downcase($::provisioner_type) in ['virtualbox', 'vmware_fusion'])
        and ! defined(File[$vhost['docroot']])
      {
        file { $vhost['docroot']:
          ensure  => directory,
          mode    => 0765,
          require => Exec["exec mkdir -p ${vhost['docroot']} @ key ${key}"]
        }
      }

      if !(downcase($::provisioner_type) in ['virtualbox', 'vmware_fusion'])
        and ! defined(File[$vhost['docroot']])
      {
        file { $vhost['docroot']:
          ensure  => directory,
          group   => 'www-user',
          mode    => 0765,
          require => [
            Exec["exec mkdir -p ${vhost['docroot']} @ key ${key}"],
            Group['www-user']
          ]
        }
      }

      create_resources(apache::vhost, { "${key}" => merge($vhost, {
          'custom_fragment' => template('puphpet/apache/custom_fragment.erb'),
          'ssl'             => 'ssl' in $vhost and str2bool($vhost['ssl']) ? { true => true, default => false },
          'ssl_cert'        => $vhost['ssl_cert'] ? { undef => undef, '' => undef, default => $vhost['ssl_cert'] },
          'ssl_key'         => $vhost['ssl_key'] ? { undef => undef, '' => undef, default => $vhost['ssl_key'] },
          'ssl_chain'       => $vhost['ssl_chain'] ? { undef => undef, '' => undef, default => $vhost['ssl_chain'] },
          'ssl_certs_dir'   => $vhost['ssl_certs_dir'] ? { undef => undef, '' => undef, default => $vhost['ssl_certs_dir'] }
        })
      })
    }
  }

  if count($apache_values['modules']) > 0 {
    apache_mod { $apache_values['modules']: }
  }
}

define apache_mod {
  if ! defined(Class["apache::mod::${name}"]) and !($name in $disallowed_modules) {
    class { "apache::mod::${name}": }
  }
}

## Begin PHP manifest

if $php_values == undef {
  $php_values = hiera('php', false)
} if $apache_values == undef {
  $apache_values = hiera('apache', false)
} if $nginx_values == undef {
  $nginx_values = hiera('nginx', false)
}

if hash_key_equals($php_values, 'install', 1) {
  Class['Php'] -> Class['Php::Devel'] -> Php::Module <| |> -> Php::Pear::Module <| |> -> Php::Pecl::Module <| |>

  if $php_prefix == undef {
    $php_prefix = $::operatingsystem ? {
      /(?i:Ubuntu|Debian|Mint|SLES|OpenSuSE)/ => 'php5-',
      default                                 => 'php-',
    }
  }

  if $php_fpm_ini == undef {
    $php_fpm_ini = $::operatingsystem ? {
      /(?i:Ubuntu|Debian|Mint|SLES|OpenSuSE)/ => '/etc/php5/fpm/php.ini',
      default                                 => '/etc/php.ini',
    }
  }

  if hash_key_equals($apache_values, 'install', 1) {
    include apache::params

    if has_key($apache_values, 'mod_spdy') and $apache_values['mod_spdy'] == 1 {
      $php_webserver_service_ini = 'cgi'
    } else {
      $php_webserver_service_ini = 'httpd'
    }

    $php_webserver_service = 'httpd'
    $php_webserver_user    = $apache::params::user
    $php_webserver_restart = true

    class { 'php':
      service => $php_webserver_service
    }
  } else {
    $php_webserver_service     = undef
    $php_webserver_service_ini = undef
    $php_webserver_restart     = false

    class { 'php':
      package             => "${php_prefix}cli",
      service             => $php_webserver_service,
      service_autorestart => false,
    }
  }

  class { 'php::devel': }

  if count($php_values['modules']['php']) > 0 {
    php_mod { $php_values['modules']['php']:; }
  }
  if count($php_values['modules']['pear']) > 0 {
    php_pear_mod { $php_values['modules']['pear']:; }
  }
  if count($php_values['modules']['pecl']) > 0 {
    php_pecl_mod { $php_values['modules']['pecl']:; }
  }
  if count($php_values['ini']) > 0 {
    each( $php_values['ini'] ) |$key, $value| {
      if is_array($value) {
        each( $php_values['ini'][$key] ) |$innerkey, $innervalue| {
          puphpet::ini { "${key}_${innerkey}":
            entry       => "CUSTOM_${innerkey}/${key}",
            value       => $innervalue,
            php_version => $php_values['version'],
            webserver   => $php_webserver_service_ini
          }
        }
      } else {
        puphpet::ini { $key:
          entry       => "CUSTOM/${key}",
          value       => $value,
          php_version => $php_values['version'],
          webserver   => $php_webserver_service_ini
        }
      }
    }

    if $php_values['ini']['session.save_path'] != undef {
      exec {"mkdir -p ${php_values['ini']['session.save_path']}":
        onlyif  => "test ! -d ${php_values['ini']['session.save_path']}",
      }

      file { $php_values['ini']['session.save_path']:
        ensure  => directory,
        group   => 'www-data',
        mode    => 0775,
        require => Exec["mkdir -p ${php_values['ini']['session.save_path']}"]
      }
    }
  }

  puphpet::ini { $key:
    entry       => 'CUSTOM/date.timezone',
    value       => $php_values['timezone'],
    php_version => $php_values['version'],
    webserver   => $php_webserver_service_ini
  }

  if hash_key_equals($php_values, 'composer', 1) {
    class { 'composer':
      target_dir      => '/usr/local/bin',
      composer_file   => 'composer',
      download_method => 'curl',
      logoutput       => false,
      tmp_path        => '/tmp',
      php_package     => "${php::params::module_prefix}cli",
      curl_package    => 'curl',
      suhosin_enabled => false,
    }
  }
}

define php_mod {
  if ! defined(Php::Module[$name]) {
    php::module { $name:
      service_autorestart => $php_webserver_restart,
    }
  }
}
define php_pear_mod {
  if ! defined(Php::Pear::Module[$name]) {
    php::pear::module { $name:
      use_package         => false,
      service_autorestart => $php_webserver_restart,
    }
  }
}
define php_pecl_mod {
  if ! defined(Php::Pecl::Module[$name]) {
    php::pecl::module { $name:
      use_package         => false,
      service_autorestart => $php_webserver_restart,
    }
  }
}
