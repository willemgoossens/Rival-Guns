<?php require APPROOT . '/views/inc/header.php'; ?>
    <div class="row mb-3">
        <div class="col-md-6">
        <h1>Notifications</h1>
        </div>
    </div>
        <?php
            if( empty($data['notificationsThisPage'])):
        ?>
            <div class="alert alert-primary">
                You have no notifications.
            </div>
        <?php
            else:
        ?>
                <table class="table table-sm table-hover">
                    <tbody>
                        <?php foreach($data['notificationsThisPage'] as $key => $notification) : ?>                            
                            <tr class="<?php echo isset($notification->readAt) ? '' : $notification->class; ?>">
                                <th scope="row">
                                    <?php echo (($data['page'] - 1) * $data['notificationsPerPage']) + $key + 1; ?>
                                </th>
                                <td>
                                    <a href="<?php echo URLROOT. $notification->link; ?>">
                                        <?php echo $notification->body; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo dateTimeFormat( $notification->createdAt ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        <?php
            endif;
        ?>
    </div>
    <?php echo paginate($data['page'], $data['notificationsPerPage'], $data['totalNotifications'], URLROOT . "/notifications"); ?>

<?php require APPROOT . '/views/inc/footer.php'; ?>