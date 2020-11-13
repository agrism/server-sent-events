<?php

namespace App\Db;

use PDO;
use PDOException;

class Db
{
	/*** @var static */
	private static $instance;

	/*** @var string */
	private $host = "";
	/*** @var string */
	private $port = "";
	/*** @var string */
	private $username = "";
	/*** @var string */
	private $password = "";
	/*** @var string */
	private $dbName = "";

	private $connection;

	/*** Db constructor.*/
	private function __construct()
	{
		$this->host = env('DB_HOST');
		$this->username = env('DB_USERNAME');
		$this->password = env('DB_PASSWORD');
		$this->port = env('DB_PORT');
		$this->dbName = env('DB_NAME');
		$this->connect();
	}

	/*** @return PDO */
	public static function factory(): PDO
	{
		if (static::$instance) {
			return static::$instance->connection;
		}

		static::$instance = new static();

		return static::$instance->connection;
	}

	private function connect(){
		try {
			$this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbName", $this->username, $this->password);
			// set the PDO error mode to exception
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
}