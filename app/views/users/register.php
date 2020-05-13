<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card card-body bg-light mt-5">
        <h2>Create An Account</h2>
        <p>Please fill out this form to register with us</p>
        <form action="<?php echo URLROOT; ?>/users/register" method="post">
          <div class="form-group">
            <label for="name">Name: <sup>*</sup></label>
            <input type="text" name="name" class="form-control form-control-lg <?php echo getValidationClass($data['nameError']); ?>" value="<?php echo $data['name']; ?>">
            <span class="invalid-feedback"><?php echo $data['nameError']; ?></span>
          </div>
          <div class="form-group">
            <label for="email">E-mail: <sup>*</sup></label>
            <input type="email" name="email" class="form-control form-control-lg <?php echo getValidationClass($data['emailError']); ?>" value="<?php echo $data['email']; ?>">
            <span class="invalid-feedback"><?php echo $data['emailError']; ?></span>
          </div>
          <div class="form-group">
            <label for="password">Password: <sup>*</sup></label>
            <input type="password" name="password" class="form-control form-control-lg <?php echo getValidationClass($data['passwordError']); ?>" value="<?php echo $data['password']; ?>">
            <span class="invalid-feedback"><?php echo $data['passwordError']; ?></span>
          </div>
          <div class="form-group">
            <label for="confirmPassword">Confirm Password: <sup>*</sup></label>
            <input type="password" name="confirmPassword" class="form-control form-control-lg <?php echo getValidationClass($data['confirmPasswordError']); ?>" value="<?php echo $data['confirmPassword']; ?>">
            <span class="invalid-feedback"><?php echo $data['confirmPasswordError']; ?></span>
          </div>

          <div class="row">
            <div class="col">
              <input type="submit" value="Register" class="btn btn-success btn-block">
            </div>
            <div class="col">
              <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-light btn-block">Have an account? Login</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
