<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <h1>My properties!</h1>
    </div>
    
    <?php
        flash('launderSuccess');
    ?>

    <div class="card mb-3">
        <div class="card-body">
            <div class="card-text mb-3">
                <p>
                    On this page you can launder your cash money to your bankaccount. The more legal companies you have, the more you can launder.
                    Be careful though, the more you launder, the higher the chance you get arrested. 
                    The tax services can arrest you for laundering until 2 weeks after the facts!
                    The maximum laundering amount mentioned below is an estimation of how much can launder, but don't take it for granted! You might get caught for a lower amount.               
                </p>
                <p>
                    When you get caught for laundering, you will be send to jail and receive a fine. If you can't pay the fine the police my confiscate some of your properties.
                    If one of those properties happens to contain an illegal business, you're screwed!
                </p>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <strong>Cash:</strong> &euro;<?php echo floor( $data['user']->cash ); ?>
                </div>
                <div class="col">
                    <strong>Bank:</strong> &euro;<?php echo floor( $data['user']->bank ); ?>
                </div>
                <div class="col">
                    <strong>Laundered today:</strong> &euro;<?php echo $data['launderedAmount'] . '/' . $data['maxLaunderingAmount']; ?>
                </div>
            </div>

            <form action="<?php echo URLROOT; ?>/properties/" method="post">
                <label class="sr-only" for="amountToLaunder">Amount</label>
                <div class="input-group mr-sm-2 mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">&euro;</div>
                    </div>
                    <input type="text" 
                        id="amountToLaunder" 
                        name="amountToLaunder" 
                        class="form-control <?php echo getValidationClass($data['amountToLaunderError']); ?>" 
                        placeholder="Amount" 
                        value="<?php echo $data['amountToLaunder'] ?? ''; ?>">

                    <span class="invalid-feedback"><?php echo $data['amountToLaunderError']; ?></span>
                </div>
                <div class="form-row mb-2">
                    <button type="submit" name="submit" class="btn btn-primary mr-sm-2" <?php echo $data['maxLaunderingAmount'] <= 0 ? 'disabled' : ''; ?>>Launder</button>
                </div>
            </form>        
        </div>
    </div>

    <?php
        if( empty($data['user']->properties)):      
    ?>
        <div class="alert alert-primary mt-2">
            You don't have any properties.
        </div>
    <?php
        else:
            foreach($data['user']->properties as $property):
    ?>
                <a href="<?php echo URLROOT . "/properties\/" . $property->id; ?>">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $data['propertyCategories'][$property->propertyCategoryId]->name . ' (#' . $property->id . ')'; ?>
                            </h5>
                        </div>
                    </div>
                </a>
    <?php
            endforeach;
            echo paginate($data['page'], $data['propertiesPerPage'], $data['user']->amountOfProperties, URLROOT . "/properties");      
        endif;
    ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
