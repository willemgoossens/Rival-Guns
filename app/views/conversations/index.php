<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row mb-3">
    <div class="col-md-6">
      <h1>Conversations</h1>
    </div>
    <div class="col-md-6">
      <a href="<?php echo URLROOT; ?>/conversations/add" class="btn btn-primary pull-right">
        <i class"fa fa-pencil"></i> New conversation
      </a>
    </div>
  </div>
  <?php echo flash('conversation_message'); ?>
  <?php if(empty($data['conversations'])) : ?>
    <div class="alert alert-primary">
      You have no conversations.
    </div>
  <?php else: foreach($data['conversations'] as $conversation) : ?>
    <a href="<?php echo URLROOT . "/conversations/read/" . $conversation->id; ?>" class=" card-link">
      <div class="card mb-3 <?php if($conversation->unread) echo "border-info"; ?>">
        <div class="card-body <?php if($conversation->unread) echo "text-info"; ?>">
          <h5 class="card-title"><?php echo $conversation->subject; ?></h5>
          <p class="card-text">
            <?php if(isset($conversation->lastMessage->senderName)): ?>
              <small class="text-muted pull-left"><?php echo $conversation->lastMessage->senderName; ?></small>
            <?php endif; ?>
            <small class="text-muted pull-right"><?php echo $conversation->lastMessage->createdAt; ?></small>
          </p>
        </div>
      </div>
    </a>
  <?php endforeach; endif; ?>

  <?php echo paginate($data['page'], $data['casesPerPage'], $data['totalCases'], URLROOT . "/conversations"); ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
