include php
class { ['php::extension::intl',
    'php::extension::mcrypt',
    'php::extension::imagick',
    'php::extension::curl',
    'php::extension::gd',
    'php::extension::mysql']:
}
class { 'apache':
  default_vhost => false,
  mpm_module    => 'prefork',
  manage_group  => false,
  manage_user   => false,
  user          => 'vagrant',
  group         => 'vagrant',
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
    version      => 'v0.12.9',
    make_install => false,
}
