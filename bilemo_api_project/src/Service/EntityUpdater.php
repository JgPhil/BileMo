<?php

namespace App\Service;


class EntityUpdater
{
    
    public function formatAndUpdate(object $entity, $data):object
    {
        foreach ($data as $key => $value) {
            if ($key !== "id") {
                if ($key === 'createdAt' || $key ==='releasedAt') {
                    $value = new \DateTime($value);
                }
                $setter = "set" . ucfirst($key);
                $entity->$setter($value);
            }
        }
        return $entity;
    }
}