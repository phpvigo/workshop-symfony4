<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 13/06/2018
 * Time: 13:39
 */

namespace App\Entity\Traits;

use Ramsey\Uuid\Uuid;

trait Uuidable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
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