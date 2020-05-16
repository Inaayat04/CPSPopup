<?php

namespace Inaayat\CPSPopup;

use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use Inaayat\CPSPopup\Main;

class CPSTask extends Task{

    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(int $tick):void{
        foreach($this->plugin->getServer()->getOnlinePlayers() as $players){
            $cpspopup = $this->plugin->config->get("CPSPopup");
            $cpspopup = str_replace("{cps}", $this->plugin->getCPS($players), $cpspopup);
            $cpspopup = str_replace("&", "§", $cpspopup);
            $players->setScoreTag($cpspopup);
        }
    }
}
