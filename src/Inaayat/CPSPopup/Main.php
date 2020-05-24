<?php

namespace Inaayat\CPSPopup;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\utils\Config;
use Inaayat\CPSPopup\CPSListener;
use Inaayat\CPSPopup\CPSTask;

class Main extends PluginBase implements Listener {

    private $clicks;
    public $config;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if ($this->config->get("CPSListener" == true)) {
            $this->getServer()->getPluginManager()->registerEvents(new CPSListener($this), $this);
        }else{
            if($this->config->get("CPSTask" == true)){
                $this->getScheduler()->scheduleRepeatingTask(new CPSTask($this), 10);
            }
        }
    }

    public function getCPS(Player $player): int{
        if(!isset($this->clicks[$player->getLowerCaseName()])){
            return 0;
        }
        $time = $this->clicks[$player->getLowerCaseName()][0];
        $clicks = $this->clicks[$player->getLowerCaseName()][1];
        if($time !== time()){
            unset($this->clicks[$player->getLowerCaseName()]);
            return 0;
        }
        return $clicks;
    }

    public function addCPS(Player $player): void {
        if(!isset($this->clicks[$player->getLowerCaseName()])){
            $this->clicks[$player->getLowerCaseName()] = [time(), 0];
        }
        $time = $this->clicks[$player->getLowerCaseName()][0];
        $clicks = $this->clicks[$player->getLowerCaseName()][1];
        if($time !== time()){
            $time = time();
            $clicks = 0;
        }
        $clicks++;
        $this->clicks[$player->getLowerCaseName()] = [$time, $clicks];
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event){
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if($packet instanceof InventoryTransactionPacket){
            $transactionType = $packet->transactionType;
            if($transactionType === InventoryTransactionPacket::TYPE_USE_ITEM || $transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY){
                $this->addCPS($player);
            }
        }
    }
}
