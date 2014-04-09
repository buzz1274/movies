class yum {
	exec { 'yum-update':
		command => '/usr/bin/yum -y update'
  	}	
}

class httpd {

  	package { "httpd":
    	ensure => installed,
  	}

  	package { "httpd-devel":
    	ensure  => installed,
  	}

  	service {"httpd":
  		ensure => running,
  		require => Package['httpd']
  	}

}