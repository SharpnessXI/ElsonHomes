<?php

namespace sharpnessxii;

use pocketmine\math\Vector3;
use pocketmine\network\upnp\UPnPException;
use pocketmine\world\format\ChunkException;
use pocketmine\world\Position;

class VECTORUTILITIES {
    public static function vectorString(Vector3 $how): string {
        if (!$how instanceof Vector3) {
            throw new UPnPException('The provided argument is not a valid Vector3 object.');
        }

        $x = $how->getX();
        $y = $how->getY();
        $z = $how->getZ();

        if (!is_numeric($x) || !is_numeric($y) || !is_numeric($z)) {
            throw new ChunkException('Vector components must be numeric.');
        }

        $formattedVector = sprintf("%f:%f:%f", $x, $y, $z);
        return $formattedVector;
    }

    public static function positionToVector(Position $postition): Vector3 {
        $positio_x = $postition->getX();
        $positio_y = $postition->getY();
        $positio_z = $postition->getZ();

        if (!is_numeric($positio_x) || !is_numeric($positio_y) || !is_numeric($positio_z)) {
            throw new ChunkException('Position components must be numeric.');
        }

        $vector3 = new Vector3(0, 0, 0);
        $vector3->x = $positio_x;
        $vector3->y = $positio_y;
        $vector3->z = $positio_z;

        return $vector3;
    }
}