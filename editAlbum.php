<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level_access'] != 'admin') {
    header("Location: index.php?error=login+required");
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM album WHERE id_album=$id";
$result = mysqli_query($connect, $query);
$album = mysqli_fetch_assoc($result);

$artis_result = mysqli_query($connect, "SELECT * FROM artis");

include 'header.php';
?>

<div class="flex-wrapper">

<?php include 'sidebar.php'; ?>

<main class="app-content">
    <div class="edit-container">
        <h2 class="page-title">Edit Album</h2>
        <form action="edit_album_proses.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $album['id_album'] ?>">

            <div class="form-group">
                <label for="id_artis">Pilih Artis</label>
                <select id="id_artis" name="id_artis" class="select-box" required>
                    <option value="">-- Pilih Artis --</option>
                    <?php while($ar = mysqli_fetch_assoc($artis_result)) { ?>
                        <option value="<?= $ar['id_artis'] ?>" <?= $ar['id_artis']==$album['id_artis']?'selected':'' ?>>
                            <?= htmlspecialchars($ar['nama_artis']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nama_album">Nama Album</label>
                <input type="text" id="nama_album" name="nama_album" class="form-control" value="<?= htmlspecialchars($album['nama_album']) ?>" required>
            </div>

            <div class="form-group">
                <label for="cover_album">Cover Album</label>
                <div class="file-input-wrapper">
                    <input type="file" id="cover_album" name="cover_album" accept="image/*">
                </div>
                <div class="file-name-display" id="cover_album-display">No file chosen</div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control"><?= htmlspecialchars($album['deskripsi']) ?></textarea>
            </div>

            <div class="form-actions">
                <input type="submit" value="Simpan Perubahan" class="btn-primary">
                <a href="manageData.php" class="btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</main>

<script>
document.getElementById('cover_album').addEventListener('change', function() {
    document.getElementById('cover_album-display').textContent = this.files[0] ? this.files[0].name : 'No file chosen';
});
</script>

<?php include 'footer.php'; ?>
</div>