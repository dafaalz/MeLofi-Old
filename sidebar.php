<file name=3 path=/Applications/MAMP/htdocs/Project TA/sidebar.php><aside class="sidebar">
    <div class="sidebar-brand">
      <span>Melo-Fi</span>
    </div>
    <ul class="sidebar-menu">
      <li><a href="library.php" class="nav-link">Home</a></li>
      <?php if($_SESSION['level_access'] == 'admin') {
      echo "<li><a href=\"adminPage.php\" class=\"nav-link\">Admin Page</a></li>";
      };?>
      <li><a href="store.php" class="nav-link">Store</a></li>
    </ul>
  </aside></file>