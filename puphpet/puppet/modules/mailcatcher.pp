create_resources('class', {'mailcatcher' =>
    {'smtp_ip' => '0.0.0.0',
     'smtp_port' => '1025',
     'http_ip' => '0.0.0.0',
     'http_port' => '1080',
     'mailcatcher_path' => '/usr/local/bin',
     'log_path' => '/var/log/mailcatcher/mailcatcher.log'}})

if !defined(Package['tilt']) {
    package { 'tilt':
        ensure   => '1.3',
        provider => 'gem',
        before   => Class['mailcatcher']
    }
}

iptables::allow {"tcp/1080":
    port  => 1080,
    protocol => 'tcp'
}

iptables::allow {"tcp/1025":
    port  => 1025,
    protocol => 'tcp'
}

if !defined(Class['supervisord']) {
    class { 'supervisord':
        install_pip => true,
    }
}

supervisord::program {'mailcatcher':
    command     =>
        "mailcatcher --smtp-ip 0.0.0.0 --smtp-port 1025 --http-ip 0.0.0.0 --http-port 1080 -f  >> /var/log/mailcatcher/mailcatcher.log",
    priority    => '100',
    user        => 'mailcatcher',
    autostart   => true,
    autorestart => true,
    environment => {
        'PATH' => "/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin"
    },
    require => Package['mailcatcher']
}
