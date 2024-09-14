<?php
namespace EntityLite;

abstract class Entity {

    public function toArray(): array {
        return get_object_vars($this); // Converts the properties to an array
    }
}
