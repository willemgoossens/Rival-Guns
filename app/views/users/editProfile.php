<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row">
    <div class="col-md-6 mx-auto pt-5">
      <?php flash('edit_success'); ?>
      <div class="card card-body bg-light">
        <h2>Edit profile</h2>
        <p>You can use the form below to edit your profile data.</p>
        <form action="<?php echo URLROOT; ?>/users/editProfile" method="post">
          <div class="form-group">
            <label for="email">Email: <sup>*</sup></label>
            <input type="email" name="email" class="form-control form-control-lg <?php echo getValidationClass($data['emailError']); ?>" value="<?php echo $data['email']; ?>">
            <span class="invalid-feedback"><?php echo $data['emailError']; ?></span>
          </div>
          <hr>
          <div class="form-group">
            <label for="password">Current Password: <sup>*</sup></label>
            <input type="password" name="password" class="form-control form-control-lg <?php echo getValidationClass($data['passwordError']); ?>" value="<?php echo $data['password']; ?>">
            <span class="invalid-feedback"><?php echo $data['passwordError']; ?></span>
          </div>
          <div class="row">
            <div class="col">
              <input type="submit" name="changeProfile" value="Edit profile" class="btn btn-success btn-block">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 mx-auto">
      <div class="card card-body bg-light mt-5">
        <h2>Change Password</h2>
        <p>You can use the form below to change your profile.</p>
        <form action="<?php echo URLROOT; ?>/users/editProfile" method="post">
          <div class="form-group">
            <label for="newPassword">New Password: <sup>*</sup></label>
            <input type="password" name="newPassword" class="form-control form-control-lg <?php echo getValidationClass($data['newPasswordError']); ?>" value="<?php echo $data['newPassword']; ?>">
            <span class="invalid-feedback"><?php echo $data['newPasswordError']; ?></span>
          </div>
          <div class="form-group">
            <label for="repeatNewPassword">Repeat New Password: <sup>*</sup></label>
            <input type="password" name="repeatNewPassword" class="form-control form-control-lg <?php echo getValidationClass($data['repeatNewPasswordError']); ?>" value="<?php echo $data['repeatNewPassword']; ?>">
            <span class="invalid-feedback"><?php echo $data['repeatNewPasswordError']; ?></span>
          </div>
          <hr>
          <div class="form-group">
            <label for="password2">Current Password: <sup>*</sup></label>
            <input type="password" name="password2" class="form-control form-control-lg <?php echo getValidationClass($data['password2Error']); ?>" value="<?php echo $data['password2']; ?>">
            <span class="invalid-feedback"><?php echo $data['password2Error']; ?></span>
          </div>
          <div class="row">
            <div class="col">
              <input type="submit" name="changePassword" value="Change password" class="btn btn-success btn-block">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
