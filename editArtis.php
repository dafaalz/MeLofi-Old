<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level_access'] != 'admin') {
    header("Location: index.php?error=login+required");
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM artis WHERE id_artis=$id";
$result = mysqli_query($connect, $query);
$artis = mysqli_fetch_assoc($result);

include 'header.php';

?>
<div class="flex-wrapper">
<?php   include 'sidebar.php'; ?>
<main class="app-content">
    <div class="edit-container">
        <h2 class="page-title">Edit Artis</h2>
        <form action="edit_artis_proses.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $artis['id_artis'] ?>">

            <div class="form-group">
                <label for="nama_artis">Nama Artis</label>
                <input type="text" id="nama_artis" name="nama_artis" class="form-control" value="<?= htmlspecialchars($artis['nama_artis']) ?>" required>
            </div>

            <div class="form-group">
                <label for="foto_profil">Foto Profil</label>
                <div class="file-input-wrapper">
                    <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
                </div>
                <div class="file-name-display" id="foto_profil-display">No file chosen</div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control"><?= htmlspecialchars($artis['deskripsi']) ?></textarea>
            </div>

            <div class="form-actions">
                <input type="submit" value="Simpan Perubahan" class="btn-primary">
                <a href="manageData.php" class="btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</main>

<script>
document.getElementById('foto_profil').addEventListener('change', function() {
    document.getElementById('foto_profil-display').textContent = this.files[0] ? this.files[0].name : 'No file chosen';
});
</script>

<?php include 'footer.php'; ?>
</div>