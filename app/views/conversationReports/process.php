<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row">
        <div class="col-12">
            <a href="<?php echo URLROOT; ?>/conversationReports" class="btn btn-light pull-left"><i class="fa fa-backward"></i> Back</a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['report']->conversation->subject; ?></h1>
        </div>
    </div>
    <div class="mh-vh-70 overflow-auto" id="messages">
        <?php if($data['addButton']): ?>
            <div class="w-100 text-center mb-5 mt-2">
                <button type="button" class="btn btn-info btn-sm" id="loadMessages" data-user-id="<?php echo $_SESSION["userId"]; ?>" data-reported-by-id="<?php echo $data["report"]->reportedById; ?>"
                    data-api-url="<?php echo URLROOT . '/api/conversationReports/getMessages/'; ?>" data-conversation-id="<?php echo $data['report']->conversationId; ?>">
                    Load More Messages
                </button>
            </div>
            <div class="clearfix" id="AddOldMessagesHere"></div>
        <?php endif; ?>
        <?php foreach($data['report']->conversation->messages as $key => $message) : ?>
            <div class="card mb-3 col-10" id="<?php echo (++$key != count($data['report']->conversation->messages)) ?: "lastMessage"; ?>">
                <div class="card-body">
                    <?php echo MarkdownToHTML($message->body); ?>
                    <p class="card-text">
                        <small class="text-muted pull-left"><?php echo $data['report']->conversationPartners[$message->userId]->name; ?></small>
                        <small class="text-muted pull-right"><?php echo dateTimeFormat( $message->createdAt ); ?></small>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="clearfix"></div>
    </div>
    <hr>
    <div class="card card-body bg-light mt-5">
        <p>
            This conversation has been reported for:
            <ul>
                <?php if($data['report']->sexismRacism): ?>
                    <li><strong>Sexism / Racism</strong></li>
                <?php
                    endif;
                    if($data['report']->spam):
                ?>
                    <li><strong>Spam</strong></li>
                <?php
                    endif;
                    if($data['report']->insult):
                ?>
                    <li><strong>Insulting / Offensive content</strong></li>
                <?php
                    endif;
                    if($data['report']->other):
                ?>
                    <li><strong>Other:</strong> <?php echo $data['report']->otherExplanation; ?></li>
                <?php endif; ?>
            </ul>
        </p>
        <?php setQuillFormId('form'); /*('form', ["conversationPartner-justification", "reportedBy-justification", "summary"]);*/ ?>
        <form action="<?php echo URLROOT . '/conversationReports/process/' . $data['report']->id; ?>" id="form" method="post" class="mb-2">
            <?php
                foreach($data['report']->conversationPartners as $id => $partner)
                {
                    $data["punishmentFormPrefix"] = "punishments[" . $id . "]";
                    $data["punishmentFormId"]         = $id;
                    $data["punishmentFormName"] = ucfirst($partner->name);

                    $data["punishmentFormPunishment"] = $data["punishments"][$id]["punishment"] ?? null;
                    $data["punishmentFormJustification"] = $data["punishments"][$id]["justification"] ?? null;
                    $data["punishmentFormJustificationError"] = $data["punishments"][$id]["justificationError"] ?? null;
                    $data["punishmentFormDatePicker"] = $data["punishments"][$id]["datePicker"] ?? null;
                    $data["punishmentFormDatePickerError"] = $data["punishments"][$id]["datePickerError"] ?? null;
                    require APPROOT . '/views/inc/punishmentForm.php';

                    echo "<hr />";
                }
            ?>
            <div class="form-group">
                <?php setQuillAddField("summary"); ?>
                <label for="summary">Your summary of how you handled the situation (This will be messaged to the reporting person, and will be stored in the database): <sup>*</sup></label>
                <div id="summary" class="textarea form-control <?php echo getValidationClass($data['summaryError']); ?>">
                <?php echo isset($data['summary']) ? MarkdownToHTML($data['summary']) : ''; ?></div>
                <span class="invalid-feedback"><?php echo $data['summaryError'] ?? null; ?></span>
            </div>

            <input type="submit" value="Process punishments, classify report" class="btn btn-success">
        </form>
    </div>


<?php require APPROOT . '/views/inc/footer.php'; ?>
