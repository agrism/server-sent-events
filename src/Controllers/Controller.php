<?php


namespace App\Controllers;


class Controller
{
	/**
	 * @return static
	 */
	public static function factory(): self
	{
		return new static();
	}
}