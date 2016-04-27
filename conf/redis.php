<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Memcached settings
| -------------------------------------------------------------------------
| Your Memcached servers can be specified below.
|
|	See: https://codeigniter.com/user_guide/libraries/caching.html#memcached
|
*/
$redis = array(
		'host'      => '192.168.0.118',
		//'password'      => '',
		'port'          => '6379',
		'timeout'       => '5',
        'socket'        => '/var/run/redis.sock',
        'socket_type'   => 'tcp'
    );
