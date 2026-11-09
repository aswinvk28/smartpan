<?php

$settings['environment'] = 'local';

$settings['mail_settings'] = array(
    'host' => 'smtp.gmail.com',
    'username' => 'aswinvk28.local@gmail.com',
    'password' => 'Y2TdTOn10',
    'port' => 25,
    'localhost' => '127.0.0.1',
    'sendmail_from' => 'aswinvk28.local@gmail.com'
);

$settings['drupal_form_token_salt'] = 'c816d6_b2558111cbb9-90729/a2d1c60_93bd13f7403c416fb9b39b1a_03b87f7613';
$settings['smartpan_form_token_salt'] = 'n_l3EX2vf-KUJiIDLMMCcO87z5_ibh1H3rYdDjjMIOw';
$settings['api_token_salt'] = '7805cae_da3efa1f0078b1a79_583f8bd71d770bfa';

$databases['stock_enhanced_db']['default'] = array(
  'database' => 'smartpan_stock_local',
  'username' => 'root',
  'password' => 'UnYrd56Bnu',
  'prefix' => array(
      'sessions' => 'smartpan_financial_test.',
      'users' => 'smartpan_financial_test.'
  ),
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['memcache_servers']['127.0.0.1:11211'] = 'default';

$settings['memcache_bins']['stock.crawl'] = 'default';
$settings['memcache_bins']['stock.db.profile'] = 'default';
$settings['memcache_bins']['stock.db.history'] = 'default';
$settings['memcache_bins']['stock.db.return'] = 'default';
$settings['memcache_bins']['stock.backend'] = 'default';

$settings['memcache_persistent'] = false;
$settings['memcache_extension'] = 'Memcache';

$settings['braintree_account_type'] = 'production';

$settings['active_account_theme'] = 'flatui';