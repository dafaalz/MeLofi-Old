<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['level_access'] != 'admin') {
    header("Location: index.php?error=login+required");
    exit();
}

// Data untuk form tambah
$artis = mysqli_query($connect, "SELECT * FROM artis");

// Data untuk daftar tabel
$artisList = mysqli_query($connect, "SELECT * FROM artis");
$albumList = mysqli_query($connect, "SELECT album.*, artis.nama_artis FROM album JOIN artis ON album.id_artis = artis.id_artis");

include 'header.php';
?>

<style>
    /* --- Styles sama seperti sebelumnya --- */
    .manage-data-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    .form-section {
        background: #ffffff;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .form-section:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .form-section h2 {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #111111;
        border-bottom: 1px solid #eaeaea;
        padding-bottom: 0.75rem;
    }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; color: #555555; font-weight: 600; }
    input[type="text"], select, textarea {
        width: 100%; padding: 0.75rem 1rem; border: 1px solid #eaeaea; border-radius: 10px;
        background-color: #fafafa; color: #111111; outline: none; font-size: 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    input[type="text"]:focus, select:focus, textarea:focus { border-color: #111111; box-shadow: 0 0 0 2px rgba(0,0,0,0.05); }
    textarea { min-height: 100px; resize: vertical; font-family: "Helvetica Neue", sans-serif; }
    .form-actions { display: flex; justify-content: flex-end; margin-top: 1.5rem; gap: 0.5rem; }
    .btn-primary { padding: 0.5rem 1rem; background: #111111; color: #fff; border-radius: 8px; text-decoration: none; }
    .btn-secondary { padding: 0.5rem 1rem; background: #ccc; color: #111; border-radius: 8px; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    table th, table td { border: 1px solid #eaeaea; padding: 0.5rem; text-align: left; }
    table img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
    .table-controls {
        margin-bottom: 10px;
    }
    .pagination {
        margin-top: 10px;
        display: flex;
        gap: 0.5rem;
    }
</style>
<div class="flex-wrapper">
<?php include 'sidebar.php'; ?>

<main class="app-content" style="padding: 20px;">
    <div class="header-content">
        <h1>Kelola Data Musik</h1>
        <div class="header-actions">
            <a href="adminPage.php" class="btn-secondary">Kembali ke Dashboard</a>
            <a href="logout.php" class="btn-secondary">Log Out</a>
        </div>
    </div>

    <div class="manage-data-container">
        <!-- FORM TAMBAH LAGU -->
        <section class="form-section">
            <h2>Tambah Lagu</h2>
            <form action="upload_lagu.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="selectArtis">Pilih Artis</label>
                    <select id="selectArtis" name="id_artis" class="select-box" required>
                        <option value="">-- Pilih Artis --</option>
                        <?php while($ar = mysqli_fetch_assoc($artis)) { ?>
                            <option value="<?= $ar['id_artis'] ?>"><?= htmlspecialchars($ar['nama_artis']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="selectAlbum">Pilih Album</label>
                    <select id="selectAlbum" name="id_album" class="select-box" required>
                        <option value="">-- Pilih Album --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="judul">Judul Lagu</label>
                    <input type="text" id="judul" name="judul" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="filename">File Lagu (.mp3)</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="filename" name="filename" accept="audio/mp3" required>
                    </div>
                    <div class="file-name-display" id="filename-display">No file chosen</div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Tambah Lagu" class="btn-primary">
                </div>
            </form>
        </section>

        <!-- FORM TAMBAH ARTIS -->
        <section class="form-section">
            <h2>Tambah Artis</h2>
            <form action="upload_artis.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_artis">Nama Artis</label>
                    <input type="text" id="nama_artis" name="nama_artis" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="foto_profil">Foto Profil</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
                    </div>
                    <div class="file-name-display" id="foto_profil-display">No file chosen</div>
                </div>
                <div class="form-group">
                    <label for="deskripsi_artis">Deskripsi</label>
                    <textarea id="deskripsi_artis" name="deskripsi" class="form-control"></textarea>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Tambah Artis" class="btn-primary">
                </div>
            </form>
        </section>

        <!-- FORM TAMBAH ALBUM -->
        <section class="form-section">
            <h2>Tambah Album</h2>
            <form action="upload_album.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="id_artis_album">Pilih Artis</label>
                    <select id="id_artis_album" name="id_artis" class="select-box" required>
                        <option value="">-- Pilih Artis --</option>
                        <?php 
                        mysqli_data_seek($artisList, 0);
                        while($ar = mysqli_fetch_assoc($artisList)) { ?>
                            <option value="<?= $ar['id_artis'] ?>"><?= htmlspecialchars($ar['nama_artis']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nama_album">Nama Album</label>
                    <input type="text" id="nama_album" name="nama_album" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cover_album">Cover Album</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="cover_album" name="cover_album" accept="image/*">
                    </div>
                    <div class="file-name-display" id="cover_album-display">No file chosen</div>
                </div>
                <div class="form-group">
                    <label for="deskripsi_album">Deskripsi</label>
                    <textarea id="deskripsi_album" name="deskripsi" class="form-control"></textarea>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Tambah Album" class="btn-primary">
                </div>
            </form>
        </section>

        <!-- DAFTAR ARTIS -->
        <section class="form-section">
            <h2>Daftar Artis</h2>
            <div class="table-controls">
              <input type="text" id="searchArtis" placeholder="Cari artis..." class="form-control" style="margin-bottom:10px;">
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama Artis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        mysqli_data_seek($artisList, 0);
                        while($ar = mysqli_fetch_assoc($artisList)) { ?>
                            <tr>
                                <td><img src="./<?= $ar['foto_profil'] ?>" alt="<?= htmlspecialchars($ar['nama_artis']) ?>"></td>
                                <td><?= htmlspecialchars($ar['nama_artis']) ?></td>
                                <td>
                                    <a href="editArtis.php?id=<?= $ar['id_artis'] ?>" class="btn-primary">Edit</a>
                                    <a href="deleteArtis.php?id=<?= $ar['id_artis'] ?>" class="btn-secondary" onclick="return confirm('Yakin hapus artis ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
              <button id="prevArtis" class="btn-secondary">Prev</button>
              <button id="nextArtis" class="btn-secondary">Next</button>
            </div>
        </section>

        <!-- DAFTAR ALBUM -->
        <section class="form-section">
            <h2>Daftar Album</h2>
            <div class="table-controls">
              <input type="text" id="searchAlbum" placeholder="Cari album..." class="form-control" style="margin-bottom:10px;">
              <select id="filterArtisAlbum" class="form-control" style="margin-bottom:10px;">
                <option value="">-- Filter berdasarkan artis --</option>
                <?php 
                mysqli_data_seek($artisList, 0);
                while($ar = mysqli_fetch_assoc($artisList)) { ?>
                  <option value="<?= htmlspecialchars($ar['nama_artis']) ?>"><?= htmlspecialchars($ar['nama_artis']) ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Nama Album</th>
                            <th>Artis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        mysqli_data_seek($albumList, 0);
                        while($al = mysqli_fetch_assoc($albumList)) { ?>
                            <tr>
                                <td><img src="./<?= $al['cover_album'] ?>" alt="<?= htmlspecialchars($al['nama_album']) ?>"></td>
                                <td><?= htmlspecialchars($al['nama_album']) ?></td>
                                <td><?= htmlspecialchars($al['nama_artis']) ?></td>
                                <td>
                                    <a href="editAlbum.php?id=<?= $al['id_album'] ?>" class="btn-primary">Edit</a>
                                    <a href="deleteAlbum.php?id=<?= $al['id_album'] ?>" class="btn-secondary" onclick="return confirm('Yakin hapus album ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
              <button id="prevAlbum" class="btn-secondary">Prev</button>
              <button id="nextAlbum" class="btn-secondary">Next</button>
            </div>
        </section>
    </div>
</main>

<script>
document.getElementById('selectArtis').addEventListener('change', function() {
    const artisId = this.value;
    const albumSelect = document.getElementById('selectAlbum');
    if (!artisId) {
        albumSelect.innerHTML = '<option value="">-- Pilih Album --</option>';
        return;
    }
    albumSelect.classList.add('loading');
    albumSelect.innerHTML = '<option value="">Loading...</option>';
    fetch('get_album.php?id_artis=' + artisId)
        .then(res => res.json())
        .then(data => {
            albumSelect.innerHTML = '<option value="">-- Pilih Album --</option>';
            data.forEach(album => {
                const opt = document.createElement('option');
                opt.value = album.id_album;
                opt.textContent = album.nama_album;
                albumSelect.appendChild(opt);
            });
            albumSelect.classList.remove('loading');
        });
});

// File input preview
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const display = document.getElementById(this.id + '-display');
        display.textContent = this.files[0] ? this.files[0].name : 'No file chosen';
    });
});

function setupTableSearchPagination(tableSelector, searchInputId, prevBtnId, nextBtnId, rowsPerPage = 5, filterSelector = null) {
  const table = document.querySelector(tableSelector);
  const rows = table.querySelectorAll('tbody tr');
  const searchInput = document.getElementById(searchInputId);
  const prevBtn = document.getElementById(prevBtnId);
  const nextBtn = document.getElementById(nextBtnId);
  const filter = filterSelector ? document.getElementById(filterSelector) : null;
  let currentPage = 1;

  function renderTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const filterTerm = filter ? filter.value.toLowerCase() : "";
    const filteredRows = Array.from(rows).filter(row => {
      const text = row.innerText.toLowerCase();
      const matchesSearch = text.includes(searchTerm);
      const matchesFilter = !filterTerm || text.includes(filterTerm);
      return matchesSearch && matchesFilter;
    });
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach(row => row.style.display = "none");
    filteredRows.slice(start, end).forEach(row => row.style.display = "");

    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;
  }

  searchInput.addEventListener('input', () => { currentPage = 1; renderTable(); });
  if (filter) filter.addEventListener('change', () => { currentPage = 1; renderTable(); });
  prevBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderTable(); }});
  nextBtn.addEventListener('click', () => { currentPage++; renderTable(); });

  renderTable();
}

// Inisialisasi
setupTableSearchPagination('section:nth-of-type(4) table', 'searchArtis', 'prevArtis', 'nextArtis');
setupTableSearchPagination('section:nth-of-type(5) table', 'searchAlbum', 'prevAlbum', 'nextAlbum', 5, 'filterArtisAlbum');
</script>

<?php include 'footer.php'; ?>
</div>