include puphpet::params
include apache::params

$webroot_location = '/var/www/'

exec {"exec mkdir -p ${webroot_location}":
    command => "mkdir -p ${webroot_location}",
    creates => $webroot_location,
}

file {$webroot_location:
    ensure  => directory,
    group   => 'www-data',
    mode    => 0775,
    require => [
        Exec["exec mkdir -p /var/www/"],
        Group['www-data']
    ]
}

iptables::allow { 'tcp/80':
    port     => '80',
    protocol => 'tcp'
}

iptables::allow { 'tcp/443':
    port     => '443',
    protocol => 'tcp'
}

class {'apache': mpm_module => 'prefork',
                 conf_template  => $apache::params::conf_template,
                 sendfile       => 'Off',
                 apache_version => $apache::version::default,
                 user => 'www-data',
                 group => 'www-data',
                 default_vhost => false,
                 manage_user => false,
                 manage_group => false
}

exec { "exec mkdir -p /var/www/movies.zz50.co.uk/movies/html":
    command => "mkdir -p /var/www/movies.zz50.co.uk/movies/html",
    creates => "/var/www/movies.zz50.co.uk/movies/html",
}

file { "/var/www/movies.zz50.co.uk/movies/html":
    ensure  => directory,
    mode    => 0765,
    require => Exec["exec mkdir -p /var/www/movies.zz50.co.uk/movies/html"]
}

apache::vhost {'alpha.movie.zz50.co.uk non_ssl':
    custom_fragment => '',
    ssl             => false,
    ssl_cert        => false,
    ssl_key         => false,
    ssl_chain       => false,
    ssl_certs_dir   => false,
    servername      => 'alpha.movie.zz50.co.uk',
    serveraliases   => [],
    docroot         => '/var/www/movies.zz50.co.uk/movies/html',
    port            => '80',
    setenv          => 'APP_ENV dev',
    override        => 'All',
}

apache::vhost {'alpha.movie.zz50.co.uk ssl':
  custom_fragment => '',
  ssl             => true,
  ssl_cert        => undef,
  ssl_key         => undef,
  ssl_chain       => undef,
  ssl_certs_dir   => undef,
  servername      => 'alpha.movie.zz50.co.uk',
  serveraliases   => [],
  docroot         => '/var/www/movies.zz50.co.uk/movies/html',
  port            => '443',
  setenv          => 'APP_ENV dev',
  override        => 'All',
}

class { "apache::mod::php": }
