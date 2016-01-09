class { 'php':
}
class { [
  'php::extension::intl',
  'php::extension::mcrypt',
  'php::extension::imagick',
  'php::extension::curl',
  'php::extension::gd',
  'php::extension::mysql'
  ]:
}
class { 'apache':
  default_vhost => false,
  mpm_module    => 'prefork',
  manage_group  => false,
  manage_user   => false,
  user          => 'vagrant',
  group         => 'vagrant',
}
class { [
  'apache::mod::php',
  'apache::mod::rewrite'
  ]:
}
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
package { 'grunt-cli':
  provider => 'npm',
  require  => Class['nodejs'],
}
exec { 'install_npm_modules':
  command      => 'npm install',
  cwd          => '/vagrant',
  creates      => '/vagrant/node_modules',
  path         => [ '/bin', '/usr/bin', '/usr/local/bin', '/usr/local/node/node-default/bin' ],
  require      => Class['nodejs'],
  timeout      => '900',
  user         => 'vagrant',
  environment => ["HOME=/home/vagrant"],
}
package { 'bower':
  provider => 'npm',
  require  => Class['nodejs'],
}
exec { 'install_bower_modules':
  command     => 'bower install',
  cwd         => '/vagrant',
  creates     => '/vagrant/components',
  path        => [ '/bin', '/usr/bin', '/usr/local/bin', '/usr/local/node/node-default/bin' ],
  require     => Package['bower'],
  user        => 'vagrant',
  environment => ["HOME=/home/vagrant"],
}

class { 'composer':
  target_dir   => '/usr/local/bin',
  suhosin_enabled => false,
}
composer::exec { 'webspell':
  cmd               => 'install',
  cwd               => '/vagrant',
  dev               => true,
  scripts           => true,
  custom_installers => true,
}
