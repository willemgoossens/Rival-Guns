<?php require APPROOT . '/views/inc/header.php'; ?>
  <div class="row">
    <div class="col-md-12 d-flex">
    <h1><?php if($data['classified']) echo "Classified "; ?>Conversation Reports</h1>
      <?php if($data['classified']): ?>
        <a href="<?php echo URLROOT; ?>/ConversationReports/1" class="ml-auto">
          <button type="button" class="btn btn-outline-primary btn-sm">
            Unclassified Messages
          </button>
        </a>
      <?php else: ?>
        <a href="<?php echo URLROOT; ?>/ConversationReports/1/classified" class="ml-auto">
          <button type="button" class="btn btn-outline-primary btn-sm">
            Classified Messages
          </button>
        </a>
      <?php endif; ?>
    </div>
  </div>
  <!-- Change or remove existing Admin Roles -->
  <div class="row mt-4">
    <div class="col-md-12 mx-auto">
      <?php echo flash('conversationReport_success'); ?>
      <?php echo flash('conversationReport_conversationPartnerInfo'); ?>
      <?php echo flash('conversationReport_reporterInfo'); ?>

      <?php if(!empty($data['conversationReports'])): ?>
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Conversation Subject</th>
              <th scope="col">Reported By</th>
              <th scope="col"><?php if($data['classified']) echo "Handled By"; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($data['conversationReports'] as $report): ?>
              <tr>
                <td scope="row"><?php echo $report->conversation->subject; ?></td>
                <td><?php echo ucfirst($report->reportedBy->name); ?></td>
                <td>
                  <?php if(!$data['classified']): ?>
                    <a href="<?php echo URLROOT . '/ConversationReports/process/' . $report->id; ?>">
                      <button type="button" class="btn btn-primary btn-sm">
                        Process Report
                      </button>
                    </a>
                  <?php
                    else:
                      echo ucfirst($report->handledBy->name);
                    endif;
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="alert alert-primary">
          There are no <?php if($data['classified']) echo "classified"; ?> conversation reports.
        </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- Change or remove existing Admin Roles -->
  <?php echo paginate($data['page'], $data['reportsPerPage'], $data['totalReports'], URLROOT . "/conversationReports"); ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
