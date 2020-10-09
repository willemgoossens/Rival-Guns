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
        You have <strong>&euro;<?php echo floor( $data['user']->cash ); ?></strong> cash and <strong>&euro;<?php echo floor( $data['user']->bank ); ?></strong> on your bank account.
    </p>

    <?php
        foreach($data['propertyCategories'] as $propertyCategoryId => $propertyCategory):
    ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo $propertyCategory->name; ?>
                </h5>
                <p class="card-text font-italic">
                    &euro;<?php echo $propertyCategory->price; ?>
                </p>

                Can serve as:
                <ul class="list-group mb-1">
                    <?php foreach($propertyCategory->businessCategoryIds as $businessCategoryId): ?>
                        <?php $businessCategory = $data['businessCategories'][$businessCategoryId]; ?>
                        <li class="list-group-item" data-toggle="tooltip">
                            <?php echo $businessCategory->name; ?>&nbsp;
                            <?php if($businessCategory->name != 'House'): ?>
                                <small>
                                    <i>
                                        <?php echo isset($businessCategory->profitPerDay) ? 'Income: &euro;' . $businessCategory->profitPerDay * $propertyCategory->generationBonus : ''; ?>
                                        <?php echo isset($businessCategory->launderingAmountPerDay) ? ' Max. advised laundering amount : &euro;' . $businessCategory->launderingAmountPerDay * $propertyCategory->generationBonus : ''; ?>
                                    </i>
                                </small>
                            <?php else: ?>
                                <small>
                                    <i>
                                        <?php echo ($propertyCategory->generationBonus != 1) ? 'Health & Energy restoration bonus: ' . ( ($propertyCategory->generationBonus - 1) * 100 ) . '%' : ''; ?>
                                    </i>
                                </small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <form action="<?php echo URLROOT; ?>/locations/realEstate" method="post">
                    <input type="number" name="propertyCategoryId" value="<?php echo $propertyCategoryId;?>" class="d-none">
                    <?php 
                        if($propertyCategory->allowPaymentByCash)
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
