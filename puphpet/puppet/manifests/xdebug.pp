class {'puphpet::xdebug':
    webserver => 'httpd'
}
puphpet::ini {'xdebug.default_enable':
    entry       => "XDEBUG/xdebug.default_enable",
    value       => 1,
    php_version => 55,
    webserver   => 'httpd'
}
puphpet::ini {'xdebug.remote_autostart':
    entry       => "XDEBUG/xdebug.remote_autostart",
    value       => 1,
    php_version => 55,
    webserver   => 'httpd'
}
puphpet::ini {'xdebug.idekey':
    entry       => "XDEBUG/xdebug.idekey",
    value       => "phpstorm",
    php_version => 55,
    webserver   => 'httpd'
}
puphpet::ini {'xdebug.remote_connect_back':
    entry       => "XDEBUG/xdebug.remote_connect_back",
    value       => 1,
    php_version => 55,
    webserver   => 'httpd'
}
puphpet::ini {'xdebug.remote_enable':
    entry       => "XDEBUG/xdebug.remote_enable",
    value       => 1,
    php_version => 55,
    webserver   => 'httpd'
}
puphpet::ini {'xdebug.remote_handler':
    entry       => "XDEBUG/xdebug.remote_handler",
    value       => 'dbgp',
    php_version => 55,
    webserver   => 'httpd'
}
puphpet::ini {'xdebug.remote_port':
    entry       => "XDEBUG/xdebug.remote_port",
    value       => 9000,
    php_version => 55,
    webserver   => 'httpd'
}

iptables::allow {"tcp/9000":
    port  => 9000,
    protocol => 'tcp'
}
