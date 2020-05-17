<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <h1>Welcome to the Hospital!</h1>
    </div>

    <p>
        Hi there,
    </p>
    <p>
        My name is doctor Jones and I will be taking care of you in case something happens. 
        When you run out of health points, you will end up here to recover for some time.
        During this time, you can't play.
    </p>
    <p>
        Recently, we've also started our "Poor people program". This means that as long as you are homeless, you can have a rest here.
    </p>
    <p class="font-italic">
        As long as you don't own any properties, you can rest here for 5 minutes. During this time, you won't be able to play, but you will gain 10 health and energy points.
    </p>

    <div class="row mb-2">
        <div class="col">
            <strong>Health:</strong> &euro;<?php echo $data['user']->health; ?>
        </div>
        <div class="col">
            <strong>Energy:</strong> &euro;<?php echo $data['user']->energy; ?>
        </div>
    </div>

    <form action="<?php echo URLROOT; ?>/locations/hospital" method="post">
        <button type="submit" name="rest" class="btn btn-primary mr-sm-2" <?php echo $data['userHasProperties'] ? "disabled" : ""; ?>>Rest for 5 minutes</button>
    </form>
<?php require APPROOT . '/views/inc/footer.php'; ?>
