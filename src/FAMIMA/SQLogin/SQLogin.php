<?php

namespace FAMIMA\SQLogin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\event\player\{PlayerLoginEvent, PlayerJoinEvent};
use pocketmine\utils\TextFormat as TF;
use FAMIMA\SQLogin\SQLdatabase;


class SQLogin extends PluginBase implements Listener
{

	private $db;

	private $server;

	private $message = [];

	public function onEnable()
	{
		$this->server = Server::getInstance();
		$this->server->getPluginManager()->registerEvents($this, $this);
		$d = $this->getDatafolder();
		@mkdir($d, 0755);
		$this->db = new SQLdatabase($d."playerdata.sqlite3");
	}

	public function onPreLogin(PlayerLoginEvent $e)
	{
		$p = $e->getPlayer();
		$n = strtolower($p->getName());
		$cid = $p->getClientId();
		$ip = $p->getAddress();
		if($this->db->isExists($n))
		{
			$data = $this->db->getPlayerdata($n);
			if($data[0]["ip"] == ip2long($ip) and $data[0]["cid"] == $cid)
			{
				$this->message[$n] = TF::GREEN."[SQLogin]".TF::WHITE."正常に認証されました";
			}else if($data[0]["ip"] == ip2long($ip) or $data[0]["cid"] == $cid)
			{
				$this->message[$n] = TF::GREEN."[SQLogin]".TF::WHITE."正常に認証されました";
				$this->db->updatePlayerdata($n, $cid, $ip);
			}else{
				$p->kick(TF::GREEN."[SQLogin]".TF::WHITE."情報が変わっています!");
				$e->setCancelled();
			}
		}else{
			$this->db->createPlayerdata($n, $cid, $ip);
			$this->message[$n] = TF::GREEN."[SQLogin]".TF::WHITE."正常にアカウントが作成されました";
		}
	}

	public function onJoin(PlayerJoinEvent $e)
	{
		$p = $e->getPlayer();
		$p->sendMessage($this->message[strtolower($p->getName())]);
	}
}