<?php

namespace Inaayat\CPSPopup;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use Inaayat\CPSPopup\Main;
use pocketmine\Player;

class CPSListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onDamage(EntityDamageByEntityEvent $event){
        $damager = $event->getDamager();
        if($damager instanceof Player){
            $cpspopup = $this->plugin->config->get("CPSPopup");
            $cpspopup = str_replace("{cps}", $this->plugin->getCPS($damager), $cpspopup);
            $cpspopup = str_replace("&", "§", $cpspopup);
            $damager->sendPopup($cpspopup);
        }
    }
}
