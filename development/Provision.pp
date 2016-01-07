include php
class { ['php::extension::intl', 'php::extension::mcrypt', 'php::extension::imagick', 'php::extension::curl', 'php::extension::gd', 'php::extension::mysql']:
}
class { 'apache':
  default_vhost => false,
  mpm_module => 'prefork',
  manage_group => false,
  manage_user => false,
  user => 'vagrant',
  group => 'vagrant',
}
include apache::mod::php
include apache::mod::rewrite
apache::vhost { 'webspell.dev':
  port    => '80',
  docroot => '/vagrant/',
}
class { '::mysql::server':
  root_password           => 'webspelldevroot',
  remove_default_accounts => true,
}
mysql::db { 'webspelldev':
  user     => 'webspelldev',
  password => 'webspelldev',
  host     => 'localhost',
  grant    => ['ALL'],
}
class { 'nodejs':
  repo_url_suffix => 'node_0.12',
}
#package { 'grunt-cli':
#  ensure   => 'present',
#  provider => 'npm',
#}
#package { 'bower':
#  ensure   => 'present',
#  provider => 'npm',
#}
