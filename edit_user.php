<?php
session_start();
include 'koneksi.php';

// Ambil data user yang akan diedit
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id=$id");
    $user = mysqli_fetch_assoc($query);
    
    if(!$user) {
        $_SESSION['error'] = "User tidak ditemukan";
        header('Location: superadmin.php');
        exit();
    }
} else {
    $_SESSION['error'] = "ID User tidak valid";
    header('Location: superadmin.php');
    exit();
}

// Proses update data
if(isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    
    // Update password hanya jika diisi
    $password_update = '';
    if(!empty($_POST['password'])) {
        $password = passwor($_POST['password'], PASSWORD_DEFAULT);
        $password_update = ", password='$password'";
    }
    
    $sql = "UPDATE user SET username='$username', role='$role' $password_update WHERE user_id=$id";
    
    if(mysqli_query($koneksi, $sql)) {
        $_SESSION['success'] = "Data user berhasil diupdate";
        header('Location: superadmin.php');
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div style="color:red"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
        
        <label>Username:
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </label><br>
        
        <label>Password (kosongkan jika tidak diubah):
            <input type="password" name="password">
        </label><br>
        
        <label>Role:
            <select name="role" required>
                <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
            </select>
        </label><br>
        
        <button type="submit" name="update">Update</button>
        <a href="superadmin.php">Kembali</a>
    </form>
</body>
</html>