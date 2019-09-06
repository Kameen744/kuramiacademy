<?php
class Database {
    //check db connection
    public $isConn;
    public $datab;
    //connetion properties
    public $host = 'localhost';
    public $user = 'root';
    public $password = '';
    public $dbname = 'reportsheet';

    // Connect to db
    public function __construct ($options = []) {
        try {
            $this->datab = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8","{$this->user}","{$this->password}",$options);
            $this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->isConn = TRUE;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
    // Disconnect to db
    public function disconnect () {
        $this->datab = NULL;
        $this->isConn = FALSE;
    }
    // Get row
    public function getRow ($query, $params = []) {
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception ($e->getMessage());
        }
    }
    // Get rows
    public function getRows ($query, $params = []) {
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
    // Insert row
    public function insertRow ($query, $params = []) {
        try {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return TRUE;
        } catch (PDOException $e) {
            throw new Exception ($e->getMessage());
        }
    }
    // Update row
    public function updateRow ($query, $params = []) {
        $this->insertRow($query, $params);
    }
    // Delete row
    public function deleteRow ($query, $params = []) {
        $this->insertRow($query, $params);
    }

    public function validation($data){
		$data = trim($data);
		$data = stripcslashes($data);
		$data = htmlspecialchars($data);
		return $data;
    }
}