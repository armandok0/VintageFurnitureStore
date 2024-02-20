function loadHeaderAndFooter() {
  // Load the header
  fetch('/Kostas_Armando_E20073/my_files/repeated/header.php')
    .then(response => response.text())
    .then(data => {
      const headerPlaceholder = document.getElementById('header-placeholder');
      headerPlaceholder.innerHTML = data;
    });

  // Load the footer
  fetch('/Kostas_Armando_E20073/my_files/repeated/footer.php')
    .then(response => response.text())
    .then(data => {
      const footerPlaceholder = document.getElementById('footer-placeholder');
      footerPlaceholder.innerHTML = data;
    });
}

loadHeaderAndFooter();