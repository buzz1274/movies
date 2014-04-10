class { 'mysql::server':
  root_password => '123',
  override_options => {'mysqld' => {'bind_address' => '0.0.0.0',
                                    'lower_case_table_names' => 1}}
}

mysql::db {'ffdc': user => 'ffdc', password => '123', host => 'localhost'}
mysql::db {'blog': user => 'blog', password => '123', host => 'localhost'}

mysql_user {"ffdc@%": password_hash => mysql_password("123")}

mysql_grant {'ffdc@%/ffdc':
                privileges => ['ALL'],
                table => 'ffdc.*',
                user => 'ffdc@%',}
mysql_grant {'ffdc@%/blog':
                privileges => ['ALL'],
                table => 'blog.*',
                user => 'ffdc@%',}
mysql_grant {'ffdc@localhost/blog':
                privileges  => ['ALL'],
                table => 'blog.*',
                user => 'ffdc@localhost',}

php::module {'mysqlnd': service_autorestart => true}

iptables::allow {"tcp/3306":
  port  => 3306,
  protocol => 'tcp'
}
