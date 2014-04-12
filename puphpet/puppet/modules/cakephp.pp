git::reposync {'cakephp':
  source_url      => 'https://github.com/cakephp/cakephp.git',
  destination_dir => '/usr/local/cakephp/',
}