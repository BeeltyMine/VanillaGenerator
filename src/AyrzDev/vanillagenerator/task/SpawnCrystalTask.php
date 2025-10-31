<?php

declare(strict_types=1);

namespace AyrzDev\vanillagenerator\task;

use AyrzDev\vanillagenerator\Loader;
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;

class SpawnCrystalTask extends Task{
    private Loader $loader;

    public function __construct(Loader $loader){
        $this->loader = $loader;
    }

    public function onRun(): void{
        $server = \pocketmine\Server::getInstance();
        $positions = $this->loader->drainCrystalQueue();
        foreach($positions as [$worldName, $x, $y, $z, $showBase]){
            $world = $server->getWorldManager()->getWorldByName($worldName);
            if($world === null) continue;
            if(!$world->isInWorld($x, $y, $z)) continue;

            try{
                $loc = \pocketmine\entity\Location::fromObject(new Vector3($x + 0.5, $y, $z + 0.5), $world, 0.0, 0.0);
                $entity = new \pocketmine\entity\object\EndCrystal($loc);
                $entity->setShowBase((bool)$showBase);
                $world->addEntity($entity);
            }catch(\Throwable $_){
                // best-effort - ignore
            }
        }
    }
}
