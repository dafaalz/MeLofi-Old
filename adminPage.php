<?php include 'connect.php';
session_start();
if (!isset($_SESSION['username']) OR ($_SESSION['level_access'] != 'admin')) {
    header("Location: index.php?error=login+required");
    exit();
}

// Pagination settings
$items_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Filter and search
$search = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
$artist_filter = isset($_GET['artist']) ? intval($_GET['artist']) : 0;

// Build query with filters
$where_clauses = [];

if ($search != '') {
    $where_clauses[] = "(l.judul LIKE '%$search%' OR ar.nama_artis LIKE '%$search%' OR a.nama_album LIKE '%$search%')";
}

if ($artist_filter > 0) {
    $where_clauses[] = "ar.id_artis = $artist_filter";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Count total items
$count_query = "SELECT COUNT(*) as total 
                FROM lagu l
                JOIN album a ON l.id_album = a.id_album
                JOIN artis ar ON a.id_artis = ar.id_artis 
                $where_sql";
$count_result = mysqli_query($connect, $count_query);
$total_items = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

// Get songs with pagination
$query = "SELECT l.id_lagu, l.judul, l.filename, a.id_album, a.nama_album, ar.id_artis, ar.nama_artis, a.cover_album
          FROM lagu l
          JOIN album a ON l.id_album = a.id_album
          JOIN artis ar ON a.id_artis = ar.id_artis
          $where_sql
          ORDER BY l.id_lagu DESC
          LIMIT $items_per_page OFFSET $offset";
$result = mysqli_query($connect, $query);

// Get artists for filter
$artists_query = "SELECT id_artis, nama_artis FROM artis ORDER BY nama_artis";
$artists = mysqli_query($connect, $artists_query);

include 'header.php';

?>
<div class="flex-wrapper">
    <?php include 'sidebar.php'; ?>
<main class="app-content" style="padding: 20px;">
    <h1 id="content-heading">Admin Dashboard</h1>
    
    <div class="admin-actions">
        <a href="manageData.php" class="button primary">Manage Data (Artists, Albums, Songs)</a>
        <a href="logout.php" class="button secondary">Log Out</a>
    </div>

    <!-- Filter and Search Section -->
    <div class="filter-section">
        <form method="GET" action="adminPage.php" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" placeholder="Search songs, artists, albums..." 
                       value="<?php echo htmlspecialchars($search); ?>" class="filter-input">
            </div>
            
            <div class="filter-group">
                <select name="artist" class="filter-select">
                    <option value="0">All Artists</option>
                    <?php while($artist = mysqli_fetch_assoc($artists)) { ?>
                        <option value="<?php echo $artist['id_artis']; ?>" 
                                <?php echo ($artist_filter == $artist['id_artis']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($artist['nama_artis']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="button primary">Apply</button>
                <a href="adminPage.php" class="button secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Results info -->
    <div class="results-info">
        <p>Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $items_per_page, $total_items); ?> of <?php echo $total_items; ?> songs</p>
    </div>

    <section id="songtables">
        <h2>Song List</h2>
        
        <!-- Table View -->
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Album</th>
                        <th>Preview</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='6' style='text-align:center;'>No songs found.</td></tr>";
                    }
                    
                    while($row = mysqli_fetch_assoc($result)) {
                        $judul = htmlspecialchars($row['judul']);
                        $artisId = $row['id_artis'];
                        $artis = htmlspecialchars($row['nama_artis']);
                        $albumId = $row['id_album'];
                        $album = htmlspecialchars($row['nama_album']);
                        $cover = htmlspecialchars($row['cover_album']);
                        $filename = htmlspecialchars($row['filename']);
                        $laguId = $row['id_lagu'];
                        $audioId = "audio_" . $laguId;
                        
                        echo "<tr>";
                        echo "<td><img src='$cover' alt='$album' style='width:50px; height:50px; object-fit:cover; border-radius:6px;'></td>";
                        echo "<td><strong>$judul</strong></td>";
                        echo "<td><a href='artisDetail.php?id=$artisId'>$artis</a></td>";
                        echo "<td><a href='albumDetail.php?id=$albumId'>$album</a></td>";
                        echo "<td>";
                        echo "<audio id='$audioId' src='songs/$filename' style='display:none;'></audio>";
                        echo "<button class='button primary btn-sm' onclick=\"playPauseTrack('$audioId', this)\">Play</button>";
                        echo "</td>";
                        echo "<td>";
                        echo "<a class='button secondary btn-sm' href='edit.php?id=$laguId'>Edit</a> ";
                        echo "<a class='button danger btn-sm' href='delete.php?id=$laguId' onclick=\"return confirm('Delete this song?');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&artist=<?php echo $artist_filter; ?>" 
               class="button secondary">Previous</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $current_page): ?>
                <span class="button primary current-page"><?php echo $i; ?></span>
            <?php elseif ($i == 1 || $i == $total_pages || abs($i - $current_page) <= 2): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&artist=<?php echo $artist_filter; ?>" 
                   class="button secondary"><?php echo $i; ?></a>
            <?php elseif (abs($i - $current_page) == 3): ?>
                <span class="button secondary disabled">...</span>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&artist=<?php echo $artist_filter; ?>" 
               class="button secondary">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>

<style>
.admin-actions {
    display: flex;
    gap: 1rem;
    margin: 1.5rem 0;
}

.filter-section {
    background: #ffffff;
    border: 1px solid #eaeaea;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filter-form {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 1rem;
    align-items: center;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #eaeaea;
    border-radius: 8px;
    font-size: 0.95rem;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

.results-info {
    margin-bottom: 1.5rem;
    color: #555;
    text-align: center;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.data-table thead {
    background: #f5f5f5;
}

.data-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #111;
    border-bottom: 2px solid #eaeaea;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #eaeaea;
    color: #333;
}

.data-table tr:hover {
    background: #fafafa;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.pagination .button {
    min-width: 40px;
    text-align: center;
}

.pagination .current-page {
    background: #111111;
    color: #ffffff;
}

.pagination .disabled {
    cursor: default;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .data-table {
        font-size: 0.85rem;
    }
    
    .data-table th,
    .data-table td {
        padding: 0.5rem;
    }
}
</style>

<script>
function playPauseTrack(audioId, button) {
    const audio = document.getElementById(audioId);

    document.querySelectorAll('audio').forEach(a => {
        if (a !== audio) {
            a.pause();
            a.currentTime = 0;
            const otherBtn = document.querySelector(`button[onclick*='${a.id}']`);
            if (otherBtn) otherBtn.textContent = "Play";
        }
    });

    if(audio.paused) {
        audio.play();
        button.textContent = 'Pause';
    } else {
        audio.pause();
        button.textContent = 'Play';
    }
}
</script>

<?php include 'footer.php'; ?>
</div>
