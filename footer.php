<footer class="footer">
  <p>© 2025 Melofi | All Rights Reserved</p>
</footer>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.querySelector(".sidebar-toggle");
  const sidebar = document.querySelector(".sidebar");
  // Find the flex wrapper and main content
  const flexWrapper = document.querySelector(".flex-wrapper");
  const mainContent = document.querySelector("main.app-content");

  if (toggleButton && sidebar && flexWrapper && mainContent) {
    toggleButton.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed");
      flexWrapper.classList.toggle("sidebar-collapsed");
      mainContent.classList.toggle("collapsed");
    });
  }
});
</script>
</body>
</html>