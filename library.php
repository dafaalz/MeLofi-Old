<?php
include 'connect.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=belum+login");
    exit();
}

$user = intval($_SESSION['user_id']);

$query = "SELECT l.id_lagu, l.judul, l.filename, a.id_album, a.nama_album, a.cover_album, r.id_artis, r.nama_artis
          FROM lagu l
          JOIN album a ON l.id_album = a.id_album
          JOIN artis r ON a.id_artis = r.id_artis
          JOIN transaksi t ON l.id_lagu = t.lagu_id
          WHERE t.user_id = $user";

$result = mysqli_query($connect, $query);

$tracks = [];

while($row = mysqli_fetch_assoc($result)) {
    $tracks[] = [
        "judul" => $row["judul"],
        "artis" => $row["nama_artis"],
        "album" => $row["nama_album"],
        "url" => "songs/" . $row["filename"],
        "cover" => $row["cover_album"],
        "id_artis" => $row["id_artis"],
        "id_album" => $row["id_album"]
    ];
}

// Query rekomendasi lagu yang belum dimiliki user
$query_rekomendasi = "SELECT l.id_lagu, l.judul, l.filename, a.id_album, a.nama_album, a.cover_album, r.id_artis, r.nama_artis
                      FROM lagu l
                      JOIN album a ON l.id_album = a.id_album
                      JOIN artis r ON a.id_artis = r.id_artis
                      WHERE l.id_lagu NOT IN (
                          SELECT lagu_id FROM transaksi WHERE user_id = $user
                      )";
$result_rekomendasi = mysqli_query($connect, $query_rekomendasi);
$rekomendasi = [];
while($row = mysqli_fetch_assoc($result_rekomendasi)) {
    $rekomendasi[] = [
        "judul" => $row["judul"],
        "artis" => $row["nama_artis"],
        "album" => $row["nama_album"],
        "cover" => $row["cover_album"],
        "id_artis" => $row["id_artis"],
        "id_album" => $row["id_album"]
    ];
}

include 'header.php';
?>
<div class="flex-wrapper">
<?php include 'sidebar.php'; ?>
<main class="app-content">
    <div id="container">
        <div id="header">
            <h2>Library</h2>
        </div>

        <div class="parent">
            <div class="div1">
                <div class="player">
                    <div class="player-main">
                        <div class="player-left">
                            <div class="track-art"></div>
                            <div class="track-details">
                                <div class="track-name">Judul Lagu</div>
                                <div class="track-artist">Artis</div>
                            </div>
                        </div>

                        <div class="player-center">
                            <div class="controls">
                                <button class="button" onclick="prevClick()">⏮</button>
                                <button class="button primary" id="playPause" onclick="playPauseTrack()">▶️</button>
                                <button class="button" onclick="nextTrack()">⏭</button>
                            </div>
                            <div class="sliders">
                                <input class="slider" type="range" id="seek_slider" min="0" max="100" value="0">
                                <input class="slider" type="range" id="vol_slider" min="0" max="100" value="100">
                            </div>
                            <div class="time-display">
                                <span id="current_time">0:00</span> / <span id="total_duration">0:00</span>
                            </div>
                            <div class="secondary-controls">
                                <button class="button" onclick="seek(-15)">-15s</button>
                                <button class="button" onclick="seek(15)">+15s</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="div2">
                <div class="queue-container">
                    <div class="queue-header">
                        <h3>Queue</h3>
                        <div class="queue-actions">
                            <button class="button" onclick="shuffleQueue()">🔀 Shuffle</button>
                            <button class="button" onclick="clearQueue()">🗑 Clear</button>
                        </div>
                    </div>
                    <div id="queue">
                        <ul id="queueList"></ul>
                    </div>
                </div>
            </div>

            <div class="div3">
                <h2>Daftar Lagu</h2>
                <div class="song-cards-container">
                <?php
                $i = 0;
                foreach ($tracks as $row) {
                ?>
                    <div class="song-card">
                        <img class="album-cover" src="<?php echo htmlspecialchars($row['cover']); ?>" alt="Cover">
                        <div class="song-info">
                            <p><strong>Judul:</strong> <?php echo htmlspecialchars($row['judul']); ?></p>
                            <p><strong>Artis:</strong> <a href="artisDetail.php?id=<?= $row['id_artis'] ?>"><?= htmlspecialchars($row['artis']) ?></a></p>
                            <p><strong>Album:</strong> <a href="albumDetail.php?id=<?= $row['id_album'] ?>"><?= htmlspecialchars($row['album']) ?></a></p>
                        </div>
                        <div class="song-actions">
                            <button class="button" onclick="loadTrack(<?php echo $i; ?>); audio.play()">Play</button>
                            <button class="button" onclick="addToQueue(<?php echo $i; ?>); audio.play()">Add To Queue</button>
                        </div>
                    </div>
                <?php
                    $i++;
                }
                ?>
                </div>

                <hr class="divider">
                <h2 class="section-heading">Rekomendasi Lagu</h2>
                <div class="song-cards-container">
                    <?php
                    if (count($rekomendasi) > 0) {
                        foreach ($rekomendasi as $r) {
                            echo '<div class="song-card rekomendasi">';
                            echo '<img class="album-cover" src="' . htmlspecialchars($r['cover']) . '" alt="Cover">';
                            echo '<div class="song-info">';
                            echo '<p><strong>Judul:</strong> ' . htmlspecialchars($r['judul']) . '</p>';
                            echo '<p><strong>Artis:</strong> <a href="artisDetail.php?id=' . $r['id_artis'] . '">' . htmlspecialchars($r['artis']) . '</a></p>';
                            echo '<p><strong>Album:</strong> <a href="albumDetail.php?id=' . $r['id_album'] . '">' . htmlspecialchars($r['album']) . '</a></p>';
                            echo '</div>';
                            echo '<div class="song-actions">';
                            echo '<button class="button primary" onclick="window.location.href=\'store.php\'">Lihat di Store</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Tidak ada rekomendasi lagu saat ini.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="button primary" onclick="window.location.href='store.php'">Beli Lagu</button>
            <button class="button primary" onclick="window.location.href='logout.php'">Log Out</button>
        </div>
    </div>

    <script>
        const trackList = <?php echo json_encode($tracks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        const audio = new Audio();
        let trackIndex = 0;
        let queue = [];

        function loadTrack(i) {
            if (!trackList[i]) return;
            audio.src = trackList[i].url;
            document.querySelector(".track-name").textContent = trackList[i].judul;
            document.querySelector(".track-artist").textContent = trackList[i].artis;

            const trackArtDiv = document.querySelector(".track-art");
            trackArtDiv.style.backgroundImage = `url('${trackList[i].cover}')`;
            trackArtDiv.style.backgroundPosition = 'center';
            trackArtDiv.style.backgroundSize = 'cover';
        }

        function playPauseTrack() {
            if (audio.src === "") loadTrack(trackIndex);
            if (audio.paused) {
                document.getElementById("playPause").innerHTML="⏸️";
                audio.play();
            } else {
                document.getElementById("playPause").innerHTML="▶️";
                audio.pause();
            }
        }

        function nextTrack() {
            if (queue.length > 0) {
                const next = queue.shift();
                trackIndex = next;
                updateQueueDisplay();
                loadTrack(trackIndex);
                audio.play();
            } else {
                trackIndex = (trackIndex + 1) % trackList.length;
                loadTrack(trackIndex);
                audio.play();
            }
        }

        function prevClick() {
            trackIndex = (trackIndex - 1 + trackList.length) % trackList.length;
            loadTrack(trackIndex);
            audio.play();
        }

        function addToQueue(i) {
            queue.push(i);
            updateQueueDisplay();
        }

        function shuffleQueue() {
            for (let i = queue.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [queue[i], queue[j]] = [queue[j], queue[i]];
            }
            updateQueueDisplay();
        }

        function clearQueue() {
            queue = [];
            updateQueueDisplay();
        }

        function updateQueueDisplay() {
            const queueList = document.getElementById("queueList");
            queueList.innerHTML = "";
            queue.forEach((i, index) => {
                const li = document.createElement("li");
                li.innerHTML = `
                    <span>${trackList[i].judul} - ${trackList[i].artis}</span>
                    <div class="queue-item-actions">
                        <button class="button" onclick="playFromQueue(${index})">▶️</button>
                        <button class="button" onclick="removeFromQueue(${index})">❌</button>
                    </div>
                `;
                queueList.appendChild(li);
            });
        }

        function playFromQueue(index) {
            if (queue[index] !== undefined) {
                // Remove all items before the selected one
                queue = queue.slice(index);
                nextTrack();
            }
        }

        function removeFromQueue(index) {
            if (queue[index] !== undefined) {
                queue.splice(index, 1);
                updateQueueDisplay();
            }
        }

        window.onload = () => {
            if (trackList.length > 0) loadTrack(trackIndex);

            const seekSlider = document.getElementById("seek_slider");
            const volSlider = document.getElementById("vol_slider");

            audio.addEventListener("timeupdate", () => {
                if (!isNaN(audio.duration)) {
                    seekSlider.value = (audio.currentTime / audio.duration) * 100;
                }
            });

            seekSlider.addEventListener("input", () => {
                if (!isNaN(audio.duration)) {
                    audio.currentTime = (seekSlider.value / 100) * audio.duration;
                }
            });

            volSlider.value = 100;
            audio.volume = 1;
            volSlider.addEventListener("input", () => {
                const fraction = volSlider.value / 100;
                audio.volume = Math.pow(fraction, 2); // logarithmic perceptual scaling
            });

            audio.addEventListener("ended", nextTrack);
        };

        function seek(seconds) {
            audio.currentTime += seconds;
        }

        audio.addEventListener('timeupdate', function() {
            document.getElementById('current_time').textContent = formatTime(audio.currentTime);
            if (!isNaN(audio.duration)) {
                document.getElementById('total_duration').textContent = formatTime(audio.duration);
            }
        });

        function formatTime(seconds) {
            let min = Math.floor(seconds / 60);
            let sec = Math.floor(seconds % 60);
            return min + ":" + (sec < 10 ? "0" : "") + sec;
        }
    </script>
</main>
<?php include 'footer.php'; ?>
</div>