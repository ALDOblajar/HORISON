<?php
// config/database.php
$host = "localhost";
$user = "root";
$pass = "S2#HKJHGG!89H!JOHH";
$db   = "db_horison"; // Sesuai dengan database kamu

// Membuat koneksi database
$conn = mysqli_connect($host, $user, $pass, $db);

// Memastikan koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
