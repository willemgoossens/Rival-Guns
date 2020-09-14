<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['title']; ?></h1>
        </div>
    </div>

    <?php if(isset($data['lowHealthWarning'])): ?>
        <div class="alert alert-danger">
            You need at least <strong>5</strong> health points.
        </div>
    <?php elseif(isset($data['lowEnergyWarning'])): ?>
        <div class="alert alert-danger">
            You need at least <strong>5</strong> energy points.
        </div>
    <?php else: ?>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Description</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['crimeCategories'] as $crime) : ?>
                    <tr>
                        <td>
                            <strong><?php echo $crime->name; ?></strong>
                            <br />
                            <small class="form-text text-muted"><?php echo $crime->description; ?></small>
                        </td>
                        <td>
                            <a href="<?php echo URLROOT; ?>/crimes/<?php echo $crime->id; ?>">
                                <button type="button" class="btn btn-success">Continue</button>
                            </a>
                        </td>
                    </div>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    
    <?php endif; ?>

<?php require APPROOT . '/views/inc/footer.php'; ?>
