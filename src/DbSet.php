<?php
namespace EntityLite;

class DbSet extends BaseRepository {
    public function __construct($db, $entityClass, $tableName) {
        parent::__construct($db, $entityClass,$tableName);
    }
}