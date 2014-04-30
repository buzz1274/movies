class {'yum::repo::remi':}
class {'yum::repo::remi_php55':}

Class['Php'] -> Class['Php::Devel'] -> Php::Module <| |> -> Php::Pear::Module <| |> -> Php::Pecl::Module <| |>

include apache::params


$php_fpm_ini = '/etc/php.ini'
$php_webserver_user    = $apache::params::user

class {'php': service => 'httpd'}
class {'php::devel':}

exec {"mkdir -p /tmp/php/sessions":
    onlyif  => "test ! -d /tmp/php/sessions",
}

file {'/tmp/php/sessions':
    ensure  => directory,
    group   => 'www-data',
    mode    => 0775,
    require => Exec["mkdir -p /tmp/php/sessions"]
}

php::module {'cli':
    service_autorestart => true,
}

php::module {'intl':
    service_autorestart => true,
}

php::module {'mcrypt':
    service_autorestart => true,
}

php::module {'soap':
    service_autorestart => true,
}

php::pecl::module {'pecl_http':
    use_package         => false,
    service_autorestart => true,
}

class {'composer':
    target_dir      => '/usr/local/bin',
    composer_file   => 'composer',
    download_method => 'curl',
    logoutput       => false,
    tmp_path        => '/tmp',
    php_package     => "${php::params::module_prefix}cli",
    curl_package    => 'curl',
    suhosin_enabled => false,
}


php::augeas {
    'php-memorylimit':
        target => $php::config_file,
        entry  => 'PHP/memory_limit',
        value  => '256M',
        require => Class['php'];
    'php-expose_php':
        target => $php::config_file,
        entry  => 'PHP/expose_php',
        value  => 'Off',
        require => Class['php'];
    'php-error_reporting':
        target => $php::config_file,
        entry  => 'PHP/error_reporting',
        value  => 'E_ALL & ~E_DEPRECATED & ~E_STRICT',
        require => Class['php'];
    'php-display_errors':
        target => $php::config_file,
        entry  => 'PHP/display_errors',
        value  => 'On',
        require => Class['php'];
    'php-error_log':
        target => $php::config_file,
        entry  => 'PHP/error_log',
        ensure => absent,
        require => Class['php'];
    'php-sendmail_path':
        target => $php::config_file,
        entry  => 'mail function/sendmail_path',
        value  => '/usr/bin/env catchmail',
        require => Class['php'];
    'php-date_timezone':
        target => $php::config_file,
        entry  => 'Date/date.timezone',
        value  => 'Europe/London',
        require => Class['php'];
}
