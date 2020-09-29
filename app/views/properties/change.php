<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <h1><?php echo $data['propertyCategory']->name . " - #" . $data['property']->id; ?></h1>
    </div>
    
    <?php echo flash('propertyChange'); ?>

    <p>
        Here you can change the function of your property.
        Pick one of the categories below.
    </p>

    <?php foreach( $data['businessCategories'] as $businessCategoryId => $businessCategory ): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo $businessCategory->name; ?>
                </h5>

                <div class="row mb-3">
                    <div class="col">
                        <strong>Installation Costs <?php echo isset($businessCategory->isLegal) ? '(Bank)' : '(Cash)'; ?>:</strong> &euro;<?php echo $businessCategory->installationCosts; ?>
                    </div>
                    <div class="col">
                        <strong>Installation time:</strong> &euro;<?php echo $businessCategory->installationTime; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <?php if( ! empty($businessCategory->profitPerDay) ): ?>
                        <div class="col">
                            <strong>Estimated profit per day:</strong> &euro;<?php echo $businessCategory->profitPerDay; ?>
                        </div>
                    <?php endif; ?>
                    <?php if($businessCategory->isLegal): ?>                        
                        <?php if( ! empty($businessCategory->launderingAmountPerDay) ): ?>
                            <div class="col">
                                <strong>Estimated laundering per day:</strong> &euro;<?php echo $businessCategory->launderingAmountPerDay; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="col font-italic">
                            This business is illegal and might be seized by the police.
                        </div>
                    <?php endif; ?>
                </div>

                <?php if($businessCategoryId == $data['property']->businessCategoryId): ?>
                    <div class="alert alert-info">
                        You're property is currently a <?php echo $businessCategory->name; ?>
                    </div>
                <?php else: ?>
                    <form action="<?php echo URLROOT; ?>/properties/change/<?php echo $data['property']->id; ?> " method="post">
                        <div class="form-row mb-2">
                            <input type="number" name="businessCategoryId" value="<?php echo $businessCategoryId; ?>" class="d-none">
                            <button type="submit" name="Change" class="btn btn-primary mr-sm-2">Change</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
