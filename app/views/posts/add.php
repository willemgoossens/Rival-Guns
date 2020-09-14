<?php require APPROOT . '/views/inc/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/posts" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Add Post</h2>
        <p>Create a post with this form.</p>
        <?php setQuillFormId('form'); setQuillAddField('body'); ?>
        <form action="<?php echo URLROOT; ?>/posts/add" id="form" method="post">
            <div class="form-group">
                <label for="title">Title <sup>*</sup></label>
                <input type="text" name="title" class="form-control form-control-lg <?php echo getValidationClass($data['titleError']); ?>" value="<?php echo $data['title']; ?>">
                <span class="invalid-feedback"><?php echo $data['titleError']; ?></span>
            </div>
            <div class="form-group">
                <label for="body">Body: <sup>*</sup></label>
                <div id="body" name="textarea" class="textarea form-control form-control-lg <?php echo getValidationClass($data['bodyError']); ?>"><?php echo $data['body']; ?></div>
                <span class="invalid-feedback"><?php echo $data['bodyError']; ?></span>
            </div>
            <br/>
            <input type="submit" value="Submit" class="btn btn-success">
        </form>
    </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
