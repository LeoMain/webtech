<?php
require 'config.php';
require 'medoo.php';

class Connect
{
	static function database()
	{
		try {
			return new medoo([
				// required
				'database_type' => 'mysql',
				'database_name' => DB_DATABASE,
				'server' => DB_HOST,
				'username' => DB_USER,
				'password' => DB_PASSWORD,
				'charset' => 'utf8'
			]);
		} catch (Exception $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			exit();
		}

		
	}
}
?>