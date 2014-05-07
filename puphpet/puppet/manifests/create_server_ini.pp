augeas { "php.ini":
    notify  => Service[httpd],
    require => Package[php],
    context => "/files/etc/php.ini/PHP",
    changes => [
        "set post_max_size 10M",
        "set upload_max_filesize 10M",
    ];
}