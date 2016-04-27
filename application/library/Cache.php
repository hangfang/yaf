<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @demo $cache = new Cache(array('adapter' => 'file', 'backup' => 'file', 'key_prefix' => 'my_'));
 * @author hangfang
 * @date 2016-04-27
 */
class Cache {

	/**
	 * Valid cache drivers
	 *
	 * @var array
	 */
	protected $valid_drivers = array(
		'apc',
		'dummy',
		'file',
		'memcached',
		'redis',
		'wincache'
	);

	/**
	 * Path of cache files (if file-based cache)
	 *
	 * @var string
	 */
	protected $_cache_path = NULL;
    
    /**
	 * cache expires after $_cache_expire seconds
	 *
	 * @var string
	 */
	protected $_cache_expire = 0;

	/**
	 * Reference to the driver
	 *
	 * @var mixed
	 */
	protected $_adapter = 'dummy';

	/**
	 * Fallback driver
	 *
	 * @var string
	 */
	protected $_backup_driver = 'dummy';

	/**
	 * Cache key prefix
	 *
	 * @var	string
	 */
	public $key_prefix = '';

	/**
	 * Constructor
	 *
	 * Initialize class properties based on the configuration array.
	 *
	 * @param	array	$config = array()
	 * @return	void
	 */
	public function __construct($config = array())
	{   
		isset($config['adapter']) && $this->_adapter = $config['adapter'];
		isset($config['backup']) && $this->_backup_driver = $config['backup'];
		isset($config['key_prefix']) && $this->key_prefix = $config['key_prefix'];
        
        
        $config = Yaf_Registry::get('config');
        $this->_catch_expire = $config['application']['cache_expire'] ? $config['application']['cache_expire'] : 0;
        
		// If the specified adapter isn't available, check the backup.
		if ( ! $this->is_supported($this->_adapter))
		{
			if ( ! $this->is_supported($this->_backup_driver))
			{
				// Backup isn't supported either. Default to 'Dummy' driver.
				log_message('error', 'Cache adapter "'.$this->_adapter.'" and backup "'.$this->_backup_driver.'" are both unavailable. Cache is now using "Dummy" adapter.');
				$this->_adapter = new Cache_Drivers_Dummy();
                return true;
			}
			else
			{
				// Backup is supported. Set it to primary.
				log_message('debug', 'Cache adapter "'.$this->_adapter.'" is unavailable. Falling back to "'.$this->_backup_driver.'" backup adapter.');
                $class = 'Cache_Drivers_'. ucfirst(strtolower($this->_backup_driver));
				$this->_adapter = new $class;
                return true;
			}
		}
        
        $class = 'Cache_Drivers_'. ucfirst(strtolower($this->_adapter));
        $this->_adapter = new $class;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Look for a value in the cache. If it exists, return the data
	 * if not, return FALSE
	 *
	 * @param	string	$id
	 * @return	mixed	value matching $id or FALSE on failure
	 */
	public function get($id)
	{
		return $this->_adapter->get($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param	string	$id	Cache ID
	 * @param	mixed	$data	Data to store
	 * @param	int	$ttl	Cache TTL (in seconds)
	 * @param	bool	$raw	Whether to store the raw value
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
        $ttl = $this->_cache_expire;
		return $this->_adapter->save($this->key_prefix.$id, $data, $ttl, $raw);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	string	$id	Cache ID
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function delete($id)
	{
		return $this->_adapter->delete($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to add
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function increment($id, $offset = 1)
	{
		return $this->_adapter->increment($this->key_prefix.$id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to reduce by
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function decrement($id, $offset = 1)
	{
		return $this->_adapter->decrement($this->key_prefix.$id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function clean()
	{
		return $this->_adapter->clean();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param	string	$type = 'user'	user/filehits
	 * @return	mixed	array containing cache info on success OR FALSE on failure
	 */
	public function cache_info($type = 'user')
	{
		return $this->_adapter->cache_info($type);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	string	$id	key to get cache metadata on
	 * @return	mixed	cache item metadata
	 */
	public function get_metadata($id)
	{
		return $this->_adapter->get_metadata($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 *
	 * @param	string	$driver	The driver to test
	 * @return	array
	 */
	public function is_supported($driver)
	{
		static $support;

		if ( ! isset($support, $support[$driver]))
		{
            switch($driver){
                case 'redis':
                    $is_supported = extension_loaded('redis');
                    break;
                case 'memcached':
                    $is_supported = extension_loaded('memcached');
                    break;
                case 'wincache':
                    $is_supported = extension_loaded('wincache') && ini_get('wincache.ucenabled');
                    break;
                case 'file':
                    $config = Yaf_Registry::get('config');
                    $this->_cache_path = $config['application']['cache_path'];
                    $is_supported = is_really_writable($this->_cache_path);
                    break;
                case 'apc':
                    $is_supported = extension_loaded('apc') && ini_get('apc.enabled');
                    break;
                default :
                    $is_supported = true;
            }
			$support[$driver] = $is_supported;
		}

		return $support[$driver];
	}
}
