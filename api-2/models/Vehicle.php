<?php

class Vehicle {
    private $conn;
    private $table_name = "vehicles";

    public $id;
    public $code;
    public $brand;
    public $model;
    public $observations;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET code=:code, brand=:brand, model=:model, observations=:observations";

        $stmt = $this->conn->prepare($query);

        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->brand = htmlspecialchars(strip_tags($this->brand));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->observations = htmlspecialchars(strip_tags($this->observations ?? ''));

        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":brand", $this->brand);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":observations", $this->observations);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function exists($code, $brand, $model) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE code = :code AND brand = :brand AND model = :model LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":code", $code);
        $stmt->bindParam(":brand", $brand);
        $stmt->bindParam(":model", $model);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}

