group {'postgres':
    ensure => present
}

class {'postgresql::globals':
    manage_package_repo => true,
    encoding            => 'UTF8',
    version             => '9.3'
}->class {'postgresql::server':
    postgres_password => '123',
    version           => '9.3',
    require           => Group['postgres']
}

postgresql::server::db {'movies':
    user     => 'movies',
    password => '123',
    grant    => 'ALL',
}

exec{"movies-import":
  command     => 'psql movies < /var/www/movies.zz50.co.uk/_docs/sql/schema.sql',
  logoutput   => true,
  require     => Postgresql::Server::Db['movies'],
  onlyif      => "test -f /var/www/movies.zz50.co.uk/_docs/sql/schema.sql"
}

php::module {'pgsql':
    service_autorestart => true,
}