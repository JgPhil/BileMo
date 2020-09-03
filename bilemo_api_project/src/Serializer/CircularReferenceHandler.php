<?php

namespace App\Serializer;


class CircularReferenceHandler
{

    public function __invoke($object) {

        $object->getId();
    }

}