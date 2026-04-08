<?php
abstract class User {
    protected $nama; 
    protected $id;

    public function __construct($nama, $id) {
        $this->nama = $nama;
        $this->id = $id;
    }

    abstract public function getRole();
    
    public function getNama() {
        return $this->nama;
    }
}