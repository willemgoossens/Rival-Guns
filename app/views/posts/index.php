<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1>News Posts</h1>
        </div>
        <?php if($data['canAdd']): ?>
            <div class="col-md-6">
                <a href="<?php echo URLROOT; ?>/posts/add" class="btn btn-primary pull-right">
                    <i class"fa fa-pencil"></i> Add Post
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php echo flash('post_message'); ?>

    <?php foreach($data['posts'] as $post) : ?>
        <div class="card card-body mb-3">
            <h4 class="card-title"><?php echo $post->title; ?></h4>
            <p class="card-text"><?php echo (strlen($post->body) > 200) ? closeTags(substr(MarkdownToHTML($post->body), 0, 200)) . '...' : MarkdownToHTML($post->body); ?></p>
            <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $post->id; ?>" class="btn btn-dark">More</a>
        </div>
    <?php endforeach; ?>

    <?php echo paginate($data['page'], $data['casesPerPage'], $data['totalCases'], URLROOT . "/posts"); ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
