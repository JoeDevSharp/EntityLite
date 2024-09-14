<?php
namespace EntityLite;

use PDO;

abstract class BaseRepository {
    protected $db;
    protected $entityClass;
    protected $tableName;

    public function __construct($db, $entityClass, $tableName) {
        $this->db = $db;
        $this->entityClass = $entityClass;
        $this->tableName = $tableName;
    }

    public function findAll() {
        $table = $this->tableName;
        $query = 'SELECT * FROM ' . $table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->entityClass);
    }

    public function findById($id) {
        $table = $this->tableName;
        $query = 'SELECT * FROM ' . $table . ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchObject(get_class($this->entityClass));
    }

    public function insert($entity) {
        $table = $this->tableName;
        $fields = implode(',', array_keys($entity->toArray()));
        $placeholders = ':' . implode(', :', array_keys($entity->toArray()));
        $query = 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $placeholders . ')';
        $stmt = $this->db->prepare($query);
        foreach ($entity->toArray() as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        return $stmt->execute();
    }

    public function update($id, $entity) {
        $table = $this->tableName;
        $fields = '';
        foreach ($entity->toArray() as $key => $value) {
            $fields .= $key . ' = :' . $key . ', ';
        }
        $fields = rtrim($fields, ', ');

        $query = 'UPDATE ' . $table . ' SET ' . $fields . ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        foreach ($entity->toArray() as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $table = $this->tableName;
        $query = 'DELETE FROM ' . $table . ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

}
