<?php require APPROOT . '/views/inc/header.php'; ?>

    <div class="row mb-3">
        <h1><?php echo $data['propertyCategory']->name . " - #" . $data['property']->id; ?></h1>
    </div>

    <?php echo flash('propertyChange'); ?>

    <div class="card mb-3">
        <div class="card-body">
            <?php if( empty($data['businessCategory']) ): ?>
                <p>
                    Your property currently doesn't have a function. <a href="<?php echo URLROOT; ?>/properties/change/<?php echo $data['property']->id; ?>">Click here to change it.</a>
                </p>
            <?php elseif( $data['property']->underConstruction ): ?>
                <p>
                    A <?php echo lcfirst( $data['businessCategory']->name ); ?> is currently being installed in your <?php echo lcfirst( $data['propertyCategory']->name ); ?>.                  
                    (<span id="counter" data-interval="<?php echo $data['property']->underConstructionSeconds; ?>" data-redirect-url="<?php echo URLROOT . "/" . $data['property']->id; ?>"></span>)
                </p>
                <div class="progress">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                        data-valuenow="<?php echo $data['property']->underConstructionSeconds; ?>" 
                        data-valuemin="0" 
                        data-valuemax="<?php echo $data['businessCategory']->installationTime; ?>"></div>
                </div>
            <?php elseif( $data['businessCategory']->name === 'House' ): ?>
                <p>
                    Your <?php echo lcfirst( $data['propertyCategory']->name ); ?> is currently being used as your house. 
                    <a href="<?php echo URLROOT; ?>/properties/change/<?php echo $data['property']->id; ?>">Click here to change it.</a>
                </p>
                <p>
                    <?php echo $data['propertyCategory']->generationBonus == 1 ? '' : 'This house gives you a health and energy restoration bonus of ' . ( $data['propertyCategory']->generationBonus * 100 - 100) . '%' ; ?>
                </p>
            <?php else: ?>
                <p>
                    Your <?php echo lcfirst( $data['propertyCategory']->name ); ?> is currently being used as a <?php echo lcfirst( $data['businessCategory']->name ); ?>. 
                    <a href="<?php echo URLROOT; ?>/properties/change/<?php echo $data['property']->id; ?>">Click here to change it.</a>
                </p>
                <p>
                    Your <?php echo lcfirst( $data['businessCategory']->name ); ?> produces about <strong>&euro;<?php echo floor( $data['businessCategory']->profitPerDay * $data['propertyCategory']->generationBonus ); ?></strong> per day.
                    It allows you to launder about <strong>&euro;<?php echo floor( $data['businessCategory']->launderingAmountPerDay * $data['propertyCategory']->generationBonus ); ?></strong> per day.
                    In total, it has produced &euro;<?php echo $data['property']->totalProfit; ?>. 
                </p>
            <?php endif; ?>
        </div>
    </div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
