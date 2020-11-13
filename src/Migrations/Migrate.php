<?php

namespace App\Migrations;

use App\Db\Db;

class Migrate
{
	public static function factory()
	{
		return new static;
	}

	public function index()
	{
		foreach (get_class_methods($this) as $method) {
			if (!method_exists($this, $method)) {
				continue;
			}

			$currentMethod = __METHOD__;
			$currentMethod = explode('::', $currentMethod);
			$currentMethod = array_pop($currentMethod);

			if ($currentMethod === $method) {
				continue;
			}

			$this->{$method}();
		}
	}

	private function createEventsTable()
	{
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS  `events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL;

		(Db::factory()->prepare($sql))->execute();
	}

	private function createClientEventsTable()
	{
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS  `client_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `client_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `delivered` tinyint(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL;

		(Db::factory()->prepare($sql))->execute();

	}


}