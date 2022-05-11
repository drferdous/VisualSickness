<div class="well card-footer"></div>
</body>

  <!-- Jquery script -->
  <script src="assets/jquery.min.js"></script>
  <script src="assets/bootstrap.min.js"></script>
  <script src="assets/jquery.dataTables.min.js"></script>
  <script src="assets/dataTables.bootstrap4.min.js"></script>
  <script>
      $(document).ready(function () {
          $("#flash-msg").delay(7000).fadeOut("slow");
          $('#example').DataTable();
      });
      function updateTable() {
          $("#example").DataTable().destroy();
          $("#example").DataTable();
      }
  </script>
</html>