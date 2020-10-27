<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <h1>Welcome to the Harry's Hoovers!</h1>
    </div>

    <?php echo flash('hoovers_work'); ?>
    <?php echo flash('hoovers_launder'); ?>

    <p>
        My name is Harry and I sell the best vacuum cleaners in town. Are you looking to make some money? No problemo, you can work for me to sell some vacuums!
        Or are you looking for someone to put some cash on your bankaccount? We'll quickly setup a fake contract to make that happen! But of course, everything comes at a price.
    </p>

    <div class="card mb-3">
        <div class="card-body">
            <p class="font-italic">
                You'll be trying to sell vacuum cleaners for 15 minutes. 
                Per sold vacuum, you earn a commission of $40 on your bank account. Selling the vacuums costs 20 energy. 
                While selling them, you can still play. 
                When you get hospitalized or arrested, your contract will be stopped without payment.
            </p>

            <form action="<?php echo URLROOT; ?>/locations/hoovers" method="post">
                <?php 
                    if( $data['existingJob'] )
                    {
                        echo '<div class="alert alert-info">You\'re working until ' . dateTimeFormat( $data['existingJob']->workingUntil ) . '</div>';
                    }
                    else
                    {
                        $disabled = $data['user']->energy < 20 ? "disabled" : "";
                        echo '<button type="submit" name="work" class="btn btn-primary mr-sm-2"' . $disabled . '>Work for 15 minutes</button>';
                    }
                ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="font-italic">
                Harry happily helps you to launder your illegal crime money onto your bank account.
                However, he'll take cut of 20%!
            </p>

            <p class="font-italic">
                You have &euro;<?php echo floor( $data['user']->cash ); ?> available.
            </p>

            <form action="<?php echo URLROOT; ?>/locations/hoovers" method="post">
                <label class="sr-only" for="amount">Amount</label>
                <div class="input-group mr-sm-2 mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">&euro;</div>
                    </div>
                    <input type="text" id="amount" name="amount" class="form-control <?php echo getValidationClass($data['amountError']); ?>" placeholder="Amount" value="<?php echo $data['amount'] ?? ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['amountError']; ?></span>
                </div>
                <div class="form-row mb-2">
                    <button type="submit" name="launder" class="btn btn-primary mr-sm-2">Launder this money</button>
                </div>
            </form>
        </div>
    </div>
    
<?php require APPROOT . '/views/inc/footer.php'; ?>
