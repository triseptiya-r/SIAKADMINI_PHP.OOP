<?php
require_once 'User.php';
require_once 'Interface.php';

class Mahasiswa extends User implements CetakLaporan {
    private $nilai; 
    private $mataKuliah;

    public function setDataAkademik($matkul, $nilai) {
        $this->mataKuliah = $matkul;
        $this->nilai = $nilai;
    }

    public function hitungIPK() {
        // Logika manajemen nilai ke IPK (skala 4.0)
        return number_format(($this->nilai / 100) * 4, 1);
    }

    public function getMatkul() {
        return $this->mataKuliah;
    }

    public function getRole() {
        return "Mahasiswa";
    }

    public function cetak() {
        return "Mencetak KHS untuk Mahasiswa: " . $this->nama;
    }
}