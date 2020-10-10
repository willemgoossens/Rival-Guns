<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-sm-4">
            <div class="card mb-4">
                <div class="card-header">
                    Personal
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    Name:
                                </th>
                                <td>
                                    <?php echo $data['user']->name; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Energy:
                                </th>
                                <td>
                                    <?php echo $data['user']->energy; ?>/100
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Health:
                                </th>
                                <td>
                                    <?php echo $data['user']->health; ?>/100
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Wanted level:
                                </th>
                                <td>
                                    <?php echo $this->data['user']->stars . '/' . GAME_MAX_STARS; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Finances
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    Cash:
                                </th>
                                <td>
                                    &euro;<?php echo floor( $data['user']->cash ); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Bank:
                                </th>
                                <td>
                                    &euro;<?php echo floor( $data['user']->bank ); ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Total:
                                </th>
                                <td>
                                    &euro;<?php echo floor( $data['user']->bank ) + floor( $data['user']->cash ); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    Skills
                    <small>(With - Without bonus)</small>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    Agility skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->agilitySkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->agilitySkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Boxing skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->boxingSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->boxingSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Burglary skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->burglarySkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->burglarySkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Car theft skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->carTheftSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->carTheftSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Charisma:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->charismaSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->charismaSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Driving skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->drivingSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->drivingSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Endurance skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->enduranceSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->enduranceSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Pistol skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->pistolSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->pistolSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Rifle skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->rifleSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->rifleSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Robbing skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->robbingSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->robbingSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Stealing skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->stealingSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->stealingSkills; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    Strength skills:
                                </th>
                                <td class="text-success">
                                    <?php echo $data['user']->bonusesIncluded->strengthSkills; ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo $data['user']->strengthSkills; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    Equipped
                </div>
                <div class="card-body">
                    <?php
                        if( empty($data['user']->wearables)
                            ||!in_array(1, array_column($data['user']->wearables, 'equipped')) ):
                    ?>
                            <div class="alert alert-light">
                              Absolutely fucking nothing!
                            </div>
                    <?php
                        else:
                    ?>
                    <table class="table table-sm table-hover">
                        <tbody>
                            <?php
                                foreach($data['user']->wearables as $wearable):
                                    if($wearable->equipped):
                            ?>
                                        <tr>
                                            <th scope="row">
                                                <?php echo ucfirst($data['wearableCategories'][$wearable->wearableCategoryId]->equippedAs); ?>:
                                            </th>
                                            <td>
                                                <?php echo $data['wearableCategories'][$wearable->wearableCategoryId]->name; ?>
                                            </td>
                                        </tr>
                            <?php
                                    endif;
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                    <?php
                        endif;
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php foreach($data['posts'] as $post) : ?>
            <div class="card card-body mb-3">
                <h4 class="card-title"><?php echo $post->title; ?></h4>
                <p class="card-text"><?php echo (strlen($post->body) > 200) ? closeTags(substr(MarkdownToHTML($post->body), 0, 200)) . '...' : MarkdownToHTML($post->body); ?></p>
                <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $post->id; ?>" class="btn btn-dark">More</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row justify-content-center">    
        <a href="<?php echo URLROOT; ?>/posts">More News</a>
    </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
