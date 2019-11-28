<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 13/06/2018
 * Time: 13:39
 */

namespace App\Entity\Traits;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

trait Uuidable
{
    /**
     * @Groups({"read"})
     */
    private $id;

    private function generateId()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId() : Uuid
    {
        return $this->id;
    }
}
