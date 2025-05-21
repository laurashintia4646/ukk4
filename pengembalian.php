<?php
// Koneksi ke database
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$db = "perpustaka";

$conn = mysqli_connect($host, $user, '', $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$message = "";

// Proses form ketika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dengan aman
    $nama_buku = htmlspecialchars($_POST['nama_buku'] ?? '');
    $tanggal = $_POST['tanggal'] ?? '';
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 0;
    $alasan = htmlspecialchars($_POST['alasan'] ?? '');

    // Validasi lengkap
    if (!empty($nama_buku) && !empty($tanggal) && $jumlah > 0 && !empty($alasan)) {
        // Gunakan prepared statement untuk keamanan maksimal
        $stmt = mysqli_prepare($conn, "INSERT INTO peminjaman (nama_buku, tanggal, jumlah, alasan) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssis", $nama_buku, $tanggal, $jumlah, $alasan);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Peminjaman berhasil disimpan!";
            } else {
                $message = "Error: Gagal menyimpan data";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    } else {
        $message = "Harap isi semua data dengan benar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pinjaman Buku</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        form { display: grid; gap: 15px; }
        input, textarea { width: 100%; padding: 8px; }
        input[type="submit"] { background: #007bff; color: white; border: none; padding: 10px; cursor: pointer; }
        input[type="submit"]:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>Form Pinjaman Buku</h2>
    
    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, 'berhasil') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="nama_buku">Nama buku:</label>
            <input type="text" id="nama_buku" name="nama_buku" required>
        </div>
        
        <div>
            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" required>
        </div>
        
        <div>
            <label for="jumlah">Jumlah:</label>
            <input type="number" id="jumlah" name="jumlah" min="1" required>
        </div>
        
        <div>
            <label for="alasan">Alasan:</label>
            <textarea id="alasan" name="alasan" rows="4" required></textarea>
        </div>
        
        <div>
            <input type="submit" value="Simpan Pinjaman">
        </div>
    </form>
</body>
</html>