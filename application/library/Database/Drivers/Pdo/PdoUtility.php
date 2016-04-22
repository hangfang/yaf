<?php
namespace Database\Drivers\Pdo;
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * PDO Utility Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/database/
 */
class PdoUtility extends \Database\Drivers\Pdo\DbUtility {

	/**
	 * Export
	 *
	 * @param	array	$params	Preferences
	 * @return	mixed
	 */
	protected function _backup($params = array())
	{
		// Currently unsupported
		return $this->db->display_error('db_unsupported_feature');
	}

}
