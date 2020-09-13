<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <h1>My properties!</h1>
    </div>

    <?php
        if( empty($data['user']->properties)):      
    ?>
        <div class="alert alert-primary">
            You don't have any properties.
        </div>
    <?php
        else:
            foreach($data['user']->properties as $property):
    ?>
                <a href="<?php echo URLROOT . '/properties/visit/' . $property->id; ?>">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $data['propertyCategories'][$property->propertyCategoryId]->name . ' (' . $property->id . ')'; ?>
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
