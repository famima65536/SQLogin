<?php

namespace FAMIMA\SQLogin;

class SQLdatabase
{
	private $db;

	public function __construct($path)
	{
		$this->db = new \SQLite3($path);
		$this->db->exec(
			"CREATE TABLE IF NOT EXISTS playerdata(
			name TEXT NOT NULL PRIMARY KEY,
			cid INTEGER NOT NULL,
			ip INTEGER NOT NULL
			)"
		);
	}

	public function createPlayerdata($name, $cid, $ip)
	{
		$address = ip2long($ip);
		$this->db->exec("INSERT INTO playerdata VALUES(\"$name\", $cid, $address)");
	}

	public function updatePlayerdata($name, $cid, $ip)
	{
		$address = ip2long($ip);
		$this->db->exec("UPDATE playerdata SET cid = $cid, ip = $address WHERE name = \"$name\"");
	}

	public function isExists($name)
	{
		$que = $this->db->prepare("SELECT * FROM playerdata WHERE name = :name");
		$que->bindValue(":name", $name, SQLITE3_TEXT);
		$data = $que->execute();
		$result = [];
		while($d = $data->fetchArray())
		{
			$result[] = $d;
		}
		return count($result) > 0;
	}

	public function getPlayerdata($name)
	{
		$que = $this->db->prepare("SELECT * FROM playerdata WHERE name = :name");
		$que->bindValue(":name", $name, SQLITE3_TEXT);
		$data = $que->execute();
		$result = [];
		while($d = $data->fetchArray())
		{
			$result[] = $d;
		}
		return $result;
	}
}