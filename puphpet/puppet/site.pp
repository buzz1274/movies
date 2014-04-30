import 'manifests/*.pp'

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
