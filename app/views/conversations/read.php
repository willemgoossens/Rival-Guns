<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row">
        <div class="col-12">
            <a href="<?php echo URLROOT; ?>/conversations" class="btn btn-light pull-left"><i class="fa fa-backward"></i> Back</a>
            <button type="button" class="btn btn-outline-danger btn-sm pull-right" data-toggle="modal" data-target="#reportModal">
                Report Conversation
            </button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['conversationData']->subject; ?></h1>
        </div>
    </div>
    <div class="mh-vh-70 overflow-auto" id="messages">
        <?php if($data['addButton']): ?>
            <div class="w-100 text-center mb-5 mt-2">
                <button type="button" class="btn btn-info btn-sm" id="loadMessages" data-user-id="<?php echo $_SESSION["userId"]; ?>" data-api-url="<?php echo URLROOT . '/api/conversations/getMessages'; ?>" data-conversation-id="<?php echo $data['conversationId']; ?>">
                    Load More Messages
                </button>
            </div>
            <div class="clearfix" id="AddOldMessagesHere"></div>
        <?php endif; ?>
        <?php foreach($data['messagesData'] as $message) : ?>
            <?php if($message->userId != $_SESSION['userId']) : ?>
                <div class="card mb-3 col-10 <?php if($message->unread) echo "border-info"; ?>">
                    <div class="card-body <?php if($message->unread) echo "text-info"; ?>">
                            <?php echo MarkdownToHTML($message->body); ?>
                            <p class="card-text">
                                    <small class="text-muted pull-left">
                                            <?php 
                                                    if( isset($data['conversationData']->noReplySender) && isset($message->name) )
                                                    {
                                                            echo $message->name . ' - On behalf of ' . $data['conversationData']->noReplySender;
                                                    }
                                                    elseif( isset($message->name) )
                                                    {
                                                            echo $message->name;
                                                    }
                                                    elseif( isset($data['conversationData']->noReplySender) )
                                                    {
                                                            echo 'On behalf of ' . $data['conversationData']->noReplySender;
                                                    }
                                            ?>
                                    </small>
                                    <small class="text-muted pull-right"><?php echo $message->createdAt; ?></small>
                            </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-3 col-10 pull-right">
                    <div class="card-body">
                        <?php echo MarkdownToHTML($message->body); ?>
                        <p class="card-text">
                            <small class="text-muted pull-right"><?php echo $message->createdAt; ?></small>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="clearfix"></div>
    </div>
    <hr>
    <?php if(!$data['conversationData']->noReply): ?>
        <?php setQuillFormId('form'); setQuillAddField('body'); ?>
        <form action="<?php echo URLROOT; ?>/conversations/read/<?php echo $data['conversationId']; ?>" id="form" method="post" class="mb-2">
            <div class="form-group">
                <label for="body">Answer: <sup>*</sup></label>
                <div id="body" name="textarea" rows="3" class="textarea form-control form-control-lg <?php echo getValidationClass($data['bodyError']); ?>"><?php echo MarkdownToHTML($data['body']); ?></div>
                <span class="invalid-feedback"><?php echo $data['bodyError']; ?></span>
            </div>
            <br/>
            <input type="submit" value="Submit" class="btn btn-success">
        </form>
    <?php else: ?>
        <div class="alert alert-primary">
            You can't reply to this conversation.
        </div>
    <?php endif; ?>

    <!-- Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Do you want to report this conversation?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <?php
                    if(!$data['conversationData']->reported):
                ?>
                    <form role="form" method="POST" action="<?php echo URLROOT; ?>/conversationReports/create/<?php echo $data['conversationId']; ?>">
                        <div class="modal-body">
                            This conversation can be reported to the moderators for different reasons.
                            <br/>
                            <br/>
                            Please select your reasons for reporting below:
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="sexismRacism" name="sexismRacism">
                                    <label class="form-check-label" for="sexist-racist">This conversation contains <strong>sexist or racist content</strong>.</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="spam" name="spam">
                                    <label class="form-check-label" for="spam">This conversation contains <strong>spam</strong>.</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="insult" name="insult">
                                    <label class="form-check-label" for="insult">This conversation contains <strong>insulting or offending contain</strong>.</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="other" name="other">
                                    <label class="form-check-label" for="other">Other reasons, namely:</label>
                                    <textarea class="form-control" id="otherExplanation" name="otherExplanation" rows="3" disabled></textarea>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <input type="submit" class="btn btn-primary" value="Report" id="reportConversation" disabled>
                        </div>
                    </form>
                <?php
                    else:
                ?>
                    <div class="modal-body">
                        <strong>Woopsie!</strong>
                        <br/>
                        Apparently you have already reported this conversation. You'll have to wait until it has been handled by the admins.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                <?php
                    endif;
                ?>
            </div>
        </div>
    </div>
    <!-- Modal -->

<?php require APPROOT . '/views/inc/footer.php'; ?>
