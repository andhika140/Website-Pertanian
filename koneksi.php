<?php
$host = "localhost";
$user = "root";       // Default user XAMPP
$pass = "";           // Default password XAMPP (kosong)
$db   = "bertan_gis"; // Nama database yang dibuat di phpMyAdmin

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Periksa apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// =================================================================
// 1. SINKRONISASI ZONA WAKTU (PHP & MySQL ke WIB)
// =================================================================
// Mengatur PHP agar menggunakan waktu Asia/Jakarta (WIB)
date_default_timezone_set('Asia/Jakarta');

// Mengatur MySQL agar sinkron dengan waktu UTC+7 (WIB)
mysqli_query($koneksi, "SET time_zone = '+07:00'");

// =================================================================
// 2. RESET OTOMATIS KE 'BELUM' JIKA SUDAH BERGANTI HARI
// =================================================================
// Perintah ini juga akan dieksekusi dengan zona waktu WIB yang baru
$query_reset = "UPDATE tabel_lahan 
                SET status_terakhir = 'belum', 
                    keterangan_info = 'Belum ada tindakan hari ini' 
                WHERE DATE(waktu_terakhir_update) < CURDATE()";

mysqli_query($koneksi, $query_reset);
?>