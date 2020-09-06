<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="jumbotron jumbotron-flud text-center">
    <div class="container">
      <h1 class="display-3">You have been hospitalized!</h1>
      <p class="lead" id="counter" data-interval="<?php echo $data['interval']; ?>" data-redirect-url="<?php echo URLROOT . "/profile"; ?>"></p>
    </div>
  </div> 
<?php require APPROOT . '/views/inc/footer.php'; ?>
