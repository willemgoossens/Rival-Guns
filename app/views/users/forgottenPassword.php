<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card card-body bg-light mt-5">
                <?php
                    flash('forgetPassword');

                    if($data['resetPw']):
                ?>
                    <h2>Reset your password</h2>
                    <p>Now you can reset your password!
                    </p>
                    <form action="<?php echo URLROOT; ?>/users/forgottenPassword/<?php echo $data['userId']; ?>/<?php echo $data['resetKey']; ?>" method="post">
                        <div class="form-group">
                            <label for="password">Password: <sup>*</sup></label>
                            <input type="password" name="password" class="form-control form-control-lg <?php echo getValidationClass($data['passwordError']); ?>" value="<?php echo $data['password']; ?>">
                            <span class="invalid-feedback"><?php echo $data['passwordError']; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Repeat Password: <sup>*</sup></label>
                            <input type="password" name="confirmPassword" class="form-control form-control-lg <<?php echo getValidationClass($data['confirmPasswordError']); ?>" value="<?php echo $data['confirmPassword']; ?>">
                            <span class="invalid-feedback"><?php echo $data['confirmPasswordError']; ?></span>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="submit" value="Reset Password" class="btn btn-success btn-block">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="<?php echo URLROOT; ?>/users/login" class="small">Back to login!</a>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <h2>I forgot my password</h2>
                    <p>Have you forgotten your password?
                        <br/>
                        Fill in the your e-mailadres below. We will send you a link to reset your password.
                    </p>
                    <form action="<?php echo URLROOT; ?>/users/forgottenPassword" method="post">
                        <div class="form-group">
                            <label for="email">E-mail: <sup>*</sup></label>
                            <input type="email" name="email" class="form-control form-control-lg <?php echo getValidationClass("password", $data); ?>" value="<?php echo $data['email']; ?>">
                            <span class="invalid-feedback"><?php echo $data['emailError']; ?></span>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="submit" value="Send new password" class="btn btn-success btn-block">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <a href="<?php echo URLROOT; ?>/users/login" class="small">Back to login!</a>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
