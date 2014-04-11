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


/*

    ini:
        display_errors: On
        error_reporting: '-1'
        session.save_path: /tmp/php/session
    timezone: Europe/London

if count($php_values['ini']) > 0 {
each( $php_values['ini'] ) |$key, $value| {
if is_array($value) {
each( $php_values['ini'][$key] ) |$innerkey, $innervalue| {
puphpet::ini { "${key}_${innerkey}":
entry       => "CUSTOM_${innerkey}/${key}",
value       => $innervalue,
php_version => $php_values['version'],
webserver   => 'httpd'
}
}
} else {
puphpet::ini { $key:
entry       => "CUSTOM/${key}",
value       => $value,
php_version => $php_values['version'],
webserver   => 'httpd'
}
}
}


puphpet::ini { $key:
  entry       => 'CUSTOM/date.timezone',
  value       => $php_values['timezone'],
  php_version => $php_values['version'],
  webserver   => $php_webserver_service_ini
}

*/
