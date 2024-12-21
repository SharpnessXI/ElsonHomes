<?php

namespace sharpnessxii;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\format\ChunkException;

class HomeInstance {
    public string $owner, $vector;

    public function __construct(string $owner, string $vector) {


        $this->owner = $owner;
        $this->vector = $vector;
    }

    public function isOwner(PluginBase|Player $player): bool {


        return $this->owner === $player->getName();
    }

    public function getOwner(): string {
        return $this->owner;
    }


    public function getVec(): string {
        return $this->vector;
    }

    public function stringVector(string $how): Vector3 {


        $coords = explode(":", $how);
        if (count($coords) !== 3) {
            throw new ChunkException("Invalid vector format. Expected X axis and some Y");
        } else {
            if (count($coords) === 3) {
                $coords = explode(":", $how);
                $x = (float)$coords[ 0 ];
                $y = (float)$coords[ 1 ];
                $z = (float)$coords[ 2 ];
                return new Vector3($x, $y, $z);
            }
        }
        $x = (float)$coords[ 0 ];
        $y = (float)$coords[ 1 ];
        $z = (float)$coords[ 2 ];
        return new Vector3($x, $y, $z);
    }


}
