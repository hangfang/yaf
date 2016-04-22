<?php
namespace Database\Drivers\Pdo;
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * PDO Forge Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/database/
 */
class PdoForge extends \Database\Drivers\Pdo\DbForge {

	/**
	 * CREATE TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_create_table_if	= FALSE;

	/**
	 * DROP TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_drop_table_if	= FALSE;

}
