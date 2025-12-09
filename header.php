<?php 
  if (session_status() == PHP_SESSION_NONE) {
  session_start();
  }
  $username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
<nav class="app-header">
  <div class="nav-left">
    <button class="sidebar-toggle">☰</button>
    <li><a href="library.php" class="nav-link">Home</a></li>
    <?php if($_SESSION['level_access'] == 'admin') {
      echo "<li><a href=\"adminPage.php\" class=\"nav-link\">Admin Page</a></li>";
    };?>
    <li><a href="store.php" class="nav-link">Store</a></li>
  </div>
  <div class="nav-right">
    <!-- Search button triggers modal -->
    <button class="nav-icon" onclick="openSearchModal()">🔍</button>
    <!-- Shopping Cart Icon -->
    <button class="nav-icon cart-icon" onclick="openCart()">
        🛒 <span class="cart-count" id="cartCount">0</span>
    </button>
    <div class="user-menu">
        <span class="username"><?php echo htmlspecialchars($username) ?></span>
        <div class="user-dropdown">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
  </div>
</nav>

<!-- Search Modal -->
<div id="searchModal" class="modal">
    <div class="modal-content search-modal-content">
        <span class="close" onclick="closeSearchModal()">&times;</span>
        <h2>Search Music</h2>
        <input type="text" id="globalSearch" placeholder="Search songs, artists, albums..." 
               onkeyup="performGlobalSearch()" class="search-input">
        <div id="searchResults" class="search-results"></div>
    </div>
</div>

<!-- Cart Modal -->
<div id="cartModal" class="modal">
    <div class="modal-content cart-modal-content">
        <span class="close" onclick="closeCart()">&times;</span>
        <h2>Shopping Cart</h2>
        <div id="cartItems" class="cart-items"></div>
        <div class="cart-summary">
            <p><strong>Total Items:</strong> <span id="totalItems">0</span></p>
            <p><strong>Total Price:</strong> $<span id="totalPrice">0.00</span></p>
        </div>
        <div class="cart-actions">
            <button class="button secondary" onclick="clearCart()">Clear Cart</button>
            <button class="button primary" onclick="proceedToCheckout()">Checkout</button>
        </div>
    </div>
</div>



<style>
li::marker {
  content: "";
}

.nav-left, .nav-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.nav-left {
    flex: 1 1 auto;
    list-style: none;
    padding: 0;
    margin: 0;
}
.nav-left li {
    list-style: none;
}
.nav-right {
    flex: 0 0 auto;
}

@media (max-width: 768px) {
  .nav-left, .nav-right {
    gap: 8px;
  }
  .nav-left {
    font-size: 14px;
  }
}

a {
    text-decoration: none;
}
.user-menu {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.user-menu .user-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #fff;
    min-width: 120px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    border-radius: 6px;
    z-index: 100;
}

.user-menu .user-dropdown a {
    display: block;
    padding: 10px 15px;
    color: #111;
    text-decoration: none;
}

.user-menu .user-dropdown a:hover {
    background-color: #f0f0f0;
}

.user-menu:hover .user-dropdown {
    display: block;
}

/* Cart Icon Styling */
.cart-icon {
    position: relative;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6);
}

.modal-content {
    background-color: #ffffff;
    margin: 5% auto;
    padding: 2rem;
    border-radius: 12px;
    width: 90%;
    max-width: 800px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    animation: modalSlide 0.3s ease;
}

@keyframes modalSlide {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #000;
}

/* Search Styles */
.search-input {
    width: 100%;
    padding: 1rem;
    border: 2px solid #eaeaea;
    border-radius: 8px;
    font-size: 1rem;
    margin-bottom: 1rem;
}

.search-results {
    max-height: 500px;
    overflow-y: auto;
}

.search-result-item {
    padding: 1rem;
    border-bottom: 1px solid #eaeaea;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: background 0.2s;
}

.search-result-item:hover {
    background: #f5f5f5;
}

.search-result-item img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}

.search-result-info {
    flex: 1;
}

.search-result-title {
    font-weight: 600;
    color: #111;
}

.search-result-subtitle {
    font-size: 0.9rem;
    color: #555;
}

/* Cart Styles */
.cart-items {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 1.5rem;
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #eaeaea;
}

.cart-item img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}

.cart-item-info {
    flex: 1;
}

.cart-item-title {
    font-weight: 600;
    color: #111;
}

.cart-item-subtitle {
    font-size: 0.9rem;
    color: #555;
}

.cart-item-price {
    font-weight: 600;
    color: #111;
}

.cart-summary {
    background: #f5f5f5;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.cart-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}
</style>

<script>
// Initialize cart from localStorage
let cart = JSON.parse(localStorage.getItem('cart')) || [];
updateCartCount();

function openSearchModal() {
    document.getElementById('searchModal').style.display = 'block';
    document.getElementById('globalSearch').focus();
}

function closeSearchModal() {
    document.getElementById('searchModal').style.display = 'none';
}

function performGlobalSearch() {
    const query = document.getElementById('globalSearch').value;
    
    if (query.length < 2) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }
    
    fetch(`search.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => console.error('Search error:', error));
}

function displaySearchResults(data) {
    const resultsDiv = document.getElementById('searchResults');
    
    if (!data.songs && !data.artists && !data.albums) {
        resultsDiv.innerHTML = '<p style="text-align:center; color:#888;">No results found</p>';
        return;
    }
    
    let html = '';
    
    // Display Songs
    if (data.songs && data.songs.length > 0) {
        html += '<h3 style="margin-top:1rem;">Songs</h3>';
        data.songs.forEach(song => {
            html += `
                <div class="search-result-item">
                    <img src="${song.cover}" alt="${song.judul}">
                    <div class="search-result-info">
                        <div class="search-result-title">${song.judul}</div>
                        <div class="search-result-subtitle">${song.artis} • ${song.album}</div>
                    </div>
                    <button class="button primary" onclick="viewSong(${song.id_lagu})">View</button>
                </div>
            `;
        });
    }
    
    // Display Artists
    if (data.artists && data.artists.length > 0) {
        html += '<h3 style="margin-top:1rem;">Artists</h3>';
        data.artists.forEach(artist => {
            html += `
                <div class="search-result-item">
                    <img src="${artist.foto_profil}" alt="${artist.nama_artis}">
                    <div class="search-result-info">
                        <div class="search-result-title">${artist.nama_artis}</div>
                        <div class="search-result-subtitle">Artist</div>
                    </div>
                    <button class="button primary" onclick="window.location.href='artisDetail.php?id=${artist.id_artis}'">View</button>
                </div>
            `;
        });
    }
    
    // Display Albums
    if (data.albums && data.albums.length > 0) {
        html += '<h3 style="margin-top:1rem;">Albums</h3>';
        data.albums.forEach(album => {
            html += `
                <div class="search-result-item">
                    <img src="${album.cover_album}" alt="${album.nama_album}">
                    <div class="search-result-info">
                        <div class="search-result-title">${album.nama_album}</div>
                        <div class="search-result-subtitle">${album.artis}</div>
                    </div>
                    <button class="button primary" onclick="window.location.href='albumDetail.php?id=${album.id_album}'">View</button>
                </div>
            `;
        });
    }
    
    resultsDiv.innerHTML = html;
}

function viewSong(songId) {
    window.location.href = `store.php#song-${songId}`;
    closeSearchModal();
}

// Cart Functions
function openCart() {
    updateCartDisplay();
    document.getElementById('cartModal').style.display = 'block';
}

function closeCart() {
    document.getElementById('cartModal').style.display = 'none';
}

function addToCart(songId, title, artist, price, cover) {
    const item = {
        id: songId,
        title: title,
        artist: artist,
        price: price,
        cover: cover
    };
    
    // Check if already in cart
    if (cart.find(i => i.id === songId)) {
        alert('This song is already in your cart!');
        return;
    }
    
    cart.push(item);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    alert('Added to cart!');
}

function removeFromCart(songId) {
    cart = cart.filter(item => item.id !== songId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    updateCartDisplay();
}

function clearCart() {
    if (confirm('Clear all items from cart?')) {
        cart = [];
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        updateCartDisplay();
    }
}

function updateCartCount() {
    document.getElementById('cartCount').textContent = cart.length;
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p style="text-align:center; color:#888;">Your cart is empty</p>';
        document.getElementById('totalItems').textContent = '0';
        document.getElementById('totalPrice').textContent = '0.00';
        return;
    }
    
    let html = '';
    let total = 0;
    
    cart.forEach(item => {
        total += item.price;
        html += `
            <div class="cart-item">
                <img src="${item.cover}" alt="${item.title}">
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.title}</div>
                    <div class="cart-item-subtitle">${item.artist}</div>
                </div>
                <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                <button class="button danger" onclick="removeFromCart(${item.id})">Remove</button>
            </div>
        `;
    });
    
    cartItemsDiv.innerHTML = html;
    document.getElementById('totalItems').textContent = cart.length;
    document.getElementById('totalPrice').textContent = total.toFixed(2);
}

function proceedToCheckout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }
    
    // Convert cart to form data and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'checkout.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'cart_data';
    input.value = JSON.stringify(cart);
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

// Close modals when clicking outside
window.onclick = function(event) {
    const searchModal = document.getElementById('searchModal');
    const cartModal = document.getElementById('cartModal');
    
    if (event.target == searchModal) {
        closeSearchModal();
    }
    if (event.target == cartModal) {
        closeCart();
    }
}
</script>
