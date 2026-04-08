<?php
require_once 'User.php';
require_once 'Interface.php';

class Dosen extends User implements CetakLaporan {
    public function getRole() {
        return "Dosen Pengampu";
    }
    public function cetak() {
        return "Mencetak Laporan Nilai Dosen: " . $this->nama;
    }
}