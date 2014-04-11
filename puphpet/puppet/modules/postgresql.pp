group {'postgres':
    ensure => present
}

class {'postgresql::globals':
    manage_package_repo => true,
    encoding            => 'UTF8',
    version             => '9.3'
}

class {'postgresql::server':
    postgres_password => '123',
    version           => '9.3',
    require           => Group['postgres']
}

postgresql::server::db {'movies':
    user     => 'movies',
    password => '123',
    grant    => 'ALL'
}

php::module {'pgsql':
    service_autorestart => true,
}
