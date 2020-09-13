<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <h1>Welcome to the real estate office!</h1>
    </div>

    <?php echo flash('realEstate_buy'); ?>

    <p>
        Hi there good sir?
        Are you looking to buy some new estate?
        From houses to warehouses, we have it all!
    </p>

    <p class="font-italic">
        You have <strong>&euro;<?php echo $data['user']->cash; ?></strong> cash and <strong>&euro;<?php echo $data['user']->bank; ?></strong> on your bank account.
    </p>

    <?php
        foreach($data['propertyCategories'] as $categoryId => $category):
    ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo $category->name; ?>
                </h5>
                <p class="card-text font-italic">
                    &euro;<?php echo $category->price; ?>
                </p>

                <form action="<?php echo URLROOT; ?>/locations/realEstate" method="post">
                    <input type="number" name="propertyCategoryId" value="<?php echo $categoryId;?>" class="d-none">
                    <?php 
                        if($category->allowPaymentByCash)
                        {
                            echo '<button type="submit" name="payByCash" class="btn btn-info mr-sm-2 mr-1">Pay by cash</button>';
                        }
                    ?>
                    <button type="submit" name="payByBank" class="btn btn-primary mr-sm-2">Pay by bank</button>

                </form>
            </div>
        </div>
    <?php
        endforeach;
    ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
