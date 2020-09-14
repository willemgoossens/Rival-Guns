<?php require APPROOT . '/views/inc/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/conversations" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>New Conversation</h2>
        <?php setQuillFormId('form'); setQuillAddField('body'); ?>
        <form action="<?php echo URLROOT; ?>/conversations/add" id="form" method="post">
            <div class="form-group">
                <label for="to">To: <sup>*</sup></label>
                <input type="text" name="to[0]" class="form-control form-control-lg <?php echo getValidationClass($data['toError']); ?>" value="<?php echo $data['to']; ?>">
                <span class="invalid-feedback"><?php echo $data['toError']; ?></span>
            </div>
            <div class="form-group">
                <label for="subject">Subject: <sup>*</sup></label>
                <input type="text" name="subject" class="form-control form-control-lg <?php echo getValidationClass($data['subjectError']); ?>" value="<?php echo $data['subject']; ?>">
                <span class="invalid-feedback"><?php echo $data['subjectError']; ?></span>
            </div>
            <div class="form-group">
                <label for="body">Body: <sup>*</sup></label>
                <div id="body" name="textarea" class="textarea form-control form-control-lg <?php echo getValidationClass($data['bodyError']); ?>"><?php echo MarkdownToHTML($data['body']); ?></div>
                <span class="invalid-feedback"><?php echo $data['bodyError']; ?></span>
            </div>
            <br/>
            <input type="submit" value="Submit" class="btn btn-success">
        </form>
    </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
