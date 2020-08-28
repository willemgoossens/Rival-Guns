<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <h1>Welcome to the Bank!</h1>
    </div>

    <?php
        flash('bank_action');
    ?>
    <p>
        Here you can withdraw money from your bank account or you can make a deposit.
        There's no limit on withdrawing money. 
        But be careful! You can merely deposit <strong>&euro;<?php echo GAME_MAX_DEPOSIT; ?> per day</strong>. 
        You wouldn't want the tax office and the police to find out about your extra activities, right?
        You'll have to look for other ways to stash the green paper.
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col">
                    <strong>Cash:</strong> &euro;<?php echo $data['user']->cash; ?>
                </div>
                <div class="col">
                    <strong>Bank:</strong> &euro;<?php echo $data['user']->bank; ?>
                </div>
                <div class="col">
                    <strong>Deposited today:</strong> &euro;<?php echo $data['user']->depositedToday . '/' . GAME_MAX_DEPOSIT; ?>
                </div>
            </div>

            <form action="<?php echo URLROOT; ?>/locations/bank" method="post">
                <label class="sr-only" for="amount">Amount</label>
                <div class="input-group mr-sm-2 mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">&euro;</div>
                    </div>
                    <input type="text" id="amount" name="amount" class="form-control <?php echo getValidationClass($data['amountError']); ?>" placeholder="Amount" value="<?php echo $data['amount'] ?? ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['amountError']; ?></span>
                </div>
                <div class="form-row mb-2">
                    <button type="submit" name="deposit" class="btn btn-primary mr-sm-2">Deposit</button>
                    <button type="submit" name="withdraw" class="btn btn-secondary mr-sm-2">Withdraw</button>
                </div>
            </form>        
        </div>
    </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
