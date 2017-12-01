<?php

defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/errors.html
 */
class Log {

	/**
	 * Path to save log files
	 *
	 * @var string
	 */
	protected $_log_path;

	/**
	 * File permissions
	 *
	 * @var	int
	 */
	protected $_file_permissions = 0644;

	/**
	 * Level of logging
	 *
	 * @var int
	 */
	protected $_threshold = 1;

	/**
	 * Array of threshold levels to log
	 *
	 * @var array
	 */
	protected $_threshold_array = array();

	/**
	 * Format of timestamp for log files
	 *
	 * @var string
	 */
	protected $_date_fmt = 'Y-m-d H:i:s';

	/**
	 * Filename extension
	 *
	 * @var	string
	 */
	protected $_file_ext;

	/**
	 * Whether or not the logger can write to the log files
	 *
	 * @var bool
	 */
	protected $_enabled = TRUE;

	/**
	 * Predefined logging levels
	 *
	 * @var array
	 */
	protected $_levels = array('ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4);

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$config = Yaf_Registry::get('config');

		$this->_log_path = $config['application']['logPath'] ? $config['application']['logPath'] : APPLICATION_PATH.'/logs/';
		$this->_file_ext = (isset($config['application']['logFileExtension']) && $config['application']['logFileExtension'])
			? ltrim($config['application']['logFileExtension'], '.') : 'log';

		file_exists($this->_log_path) OR mkdir($this->_log_path, 0755, TRUE);

		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path))
		{
			$this->_enabled = FALSE;
		}

		if (is_numeric($config['application']['logThreshold']))
		{
			$this->_threshold = (int) $config['application']['logThreshold'];
		}
		elseif (is_array($config['application']['logThreshold']))
		{
			$this->_threshold = 0;
			$this->_threshold_array = array_flip($config['application']['logThreshold']);
		}

		if ( ! empty($config['application']['logDateFormat']))
		{
			$this->_date_fmt = $config['application']['logDateFormat'];
		}

		if ( ! empty($config['application']['logFilePermissions']) && is_int($config['application']['logFilePermissions']))
		{
			$this->_file_permissions = $config['application']['logFilePermissions'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	$level 	The error level: 'error', 'debug' or 'info'
	 * @param	string	$msg 	The error message
	 * @return	bool
	 */
	public function write_log($level, $msg)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if (( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
			&& ! isset($this->_threshold_array[$this->_levels[$level]]))
		{
			return FALSE;
		}

		$filepath = $this->_log_path.$level;
		$message = '';
        
        if ( ! file_exists($this->_log_path.$level.'/')){
            mkdir ($filepath, 0777, true);
        }
        
        $env = Yaf_Registry::get('app')->environ();
		$filepath .= '/log-'. date('Y-m-d') .'-'.$env.'.'.$this->_file_ext;
		if ( ! file_exists($filepath))
		{
			$newfile = TRUE;
			// Only add protection to php files
			if ($this->_file_ext === 'php')
			{
				$message .= "<?php defined('APPLICATION_PATH') OR exit('No direct script access allowed'); ?>\n\n";
			}
		}

		if ( ! $fp = @fopen($filepath, 'ab'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		// Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
		if (strpos($this->_date_fmt, 'u') !== FALSE)
		{
			$microtime_full = microtime(TRUE);
			$microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
			$date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
			$date = $date->format($this->_date_fmt);
		}
		else
		{
			$date = date($this->_date_fmt);
		}

		$message .= $this->_format_line($level, $date, $msg);

		for ($written = 0, $length = strlen($message); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($message, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			chmod($filepath, $this->_file_permissions);
		}

		return is_int($result);
	}

	// --------------------------------------------------------------------

	/**
	 * Format the log line.
	 *
	 * This is for extensibility of log formatting
	 * If you want to change the log format, extend the CI_Log class and override this method
	 *
	 * @param	string	$level 	The error level
	 * @param	string	$date 	Formatted date string
	 * @param	string	$msg 	The log message
	 * @return	string	Formatted log line with a new line character '\n' at the end
	 */
	protected function _format_line($level, $date, $message)
	{
		return $level.' - '.$date.' --> '.$message."\n";
	}
}
