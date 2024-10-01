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

    public function filter(callable $callback = null) {
        $table = $this->tableName;
        $query = 'SELECT * FROM ' . $table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        // Obtener todos los resultados como objetos de la clase de entidad
        $resultados = $stmt->fetchAll(PDO::FETCH_CLASS, $this->entityClass);
        
        // Si se proporciona un callback, filtrar los resultados
        if ($callback) {
            return array_filter($resultados, $callback);
        }

        return $resultados;
    }

    public function findById($id) {
        // Obtener la clave primaria dinamicamente
        $primaryKey = $this->getPrimaryKey();
        
        if (!$primaryKey) {
            throw new Exception('No se ha encontrado ninguna propiedad con la anotación @key en la entidad.');
        }

        $table = $this->tableName;
        $query = 'SELECT * FROM ' . $table . ' WHERE ' . $primaryKey . ' = :id';
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

    public function update($id, $entity, $idFieldName = null) {
        // Si no se proporciona el campo de ID, obtener el nombre dinámicamente
        if ($idFieldName === null) {
            $idFieldName = $this->getPrimaryKey();
        }

        if (!$idFieldName) {
            throw new Exception('No se ha encontrado ninguna propiedad con la anotación @key en la entidad.');
        }

        $table = $this->tableName;
        $fields = '';
        foreach ($entity->toArray() as $key => $value) {
            $fields .= $key . ' = :' . $key . ', ';
        }
        $fields = rtrim($fields, ', ');

        $query = 'UPDATE ' . $table . ' SET ' . $fields . ' WHERE ' . $idFieldName . ' = :id';
        $stmt = $this->db->prepare($query);
        foreach ($entity->toArray() as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }


    public function delete($id) {
        // Obtener la clave primaria dinamicamente
        $primaryKey = $this->getPrimaryKey();

        if (!$primaryKey) {
            throw new Exception('No se ha encontrado ninguna propiedad con la anotación @key en la entidad.');
        }

        $table = $this->tableName;
        $query = 'DELETE FROM ' . $table . ' WHERE ' . $primaryKey . ' = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function getPrimaryKey()
    {
        // Crear una instancia de ReflectionClass para inspeccionar la entidad
        $reflection = new \ReflectionClass($this->entityClass);
        
        // Recorrer todas las propiedades de la clase
        foreach ($reflection->getProperties() as $property) {

            // Obtener los comentarios (docblock) de la propiedad
            $docComment = $property->getDocComment();

            // Verificar si el docblock contiene la anotación @key
            if ($docComment !== false && strpos($docComment, '@key') !== false) {

                // Si se encuentra la anotación, devolver el nombre de la propiedad
                return $property->getName();
            }
        }

        // Si no se encuentra la propiedad con @key, devolver null o un valor por defecto
        return "id";
    }

}