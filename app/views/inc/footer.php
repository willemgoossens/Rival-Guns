</div>

<!-- Footer -->
<footer class="footer font-small special-color-dark pt-4">
  <!-- Copyright -->
  <div class="footer-copyright text-center py-3">© <?php echo date("Y"); ?> Copyright:
    <a href="<?php echo COPYRIGHT_URL; ?>"> <?php echo COPYRIGHT; ?></a>
  </div>
  <!-- Copyright -->
</footer>
<!-- Footer -->

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<?php
  if(isset($data['CDNFiles'])) {
    foreach($data['CDNFiles'] as $cdn) {
      if($cdn == 'TempusDominus') {
        echo "<script type=\"text/javascript\" src=\"https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js\"></script>";
        echo "<script type=\"text/javascript\" src=\"https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js\"></script>";
      }
    }
  }
  
  if(file_exists(ROOT . '/public/js/' .  $view . '.js'))
  {
      echo "<script src=\"" . URLROOT . "/js/" . $view . ".js\"></script>";
  }

  runQuillEditor();
?>
</body>
</html>
