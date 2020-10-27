<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <h1><?php echo $data['title']; ?></h1>
        </div>
    </div>

    <div class="row">
        <?php foreach($data['storyline'] as $part): ?>
            <div class="col-12 alert alert-<?php echo $part["class"]; ?>"><?php echo $part["story"]; ?></div>
        <?php 
            endforeach; 
            if($data['arrested']):
        ?>
            <div class="col-12 alert alert-danger text-center">
                You've been arrested until <strong><?php echo dateTimeFormat( $data["user"]->prisonReleaseDate ); ?></strong>
                <ul class="list-group mt-3">
                    <li class="list-group-item text-dark font-weight-bold">On the grounds of:</li>
                    <?php foreach($data["sentences"] as $key => $sentence): ?>
                        <?php if( $key == 0 ): ?>
                            <?php foreach( $sentence->criminalRecords as $arrestedFor => $amount ): ?>
                                <li class="list-group-item"><?php echo $amount . 'x ' . ucfirst($arrestedFor); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">A Previous Sentence</li>
                        <?php endif; ?>    
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php
            endif;
        ?>
    </div>
    <hr/>
    <div class="row">
        <?php if(! empty($data['userRewards'])): ?>
            <div class="col-md-3">
                <ul class="list-group text-center list-group-flush">
                    <li class="list-group-item"><strong>User Rewards</strong></li>
                    <?php foreach($data['userRewards'] as $key => $reward): ?>
                        <li class="list-group-item <?php echo $reward < 0 ? "text-danger" : "text-success"; ?>">
                            <?php echo $key == "cash" ? "$" . $reward : $reward . " " . $key . " points"; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php
            endif;
            if(! empty($data['crimeRecords'])):
        ?>
                <div class="col-md-3">
                    <ul class="list-group text-center list-group-flush">
                        <li class="list-group-item"><strong>Criminal Record</strong></li>
                        <?php foreach($data['crimeRecords'] as $record): ?>
                            <li class="list-group-item">
                                + <?php echo ucfirst($record); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
        <?php
            endif;
            if(! empty($data['addedItems'])):
        ?>
                <div class="col-md-3">
                    <ul class="list-group text-center list-group-flush">
                        <li class="list-group-item"><strong>Gained Items</strong></li>
                        <li class="list-group-item">
                            test
                        </li>
                    </ul>
                </div>
        <?php
            endif;
        ?>
    </div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
