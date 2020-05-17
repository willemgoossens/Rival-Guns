<?php require APPROOT . '/views/inc/header.php'; ?>
  <ul class="nav nav-fill nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link <?php if(!isset($_SESSION['tab2_error']) && !isset($_SESSION['tab2_success'])) echo "active"; ?>" aria-controls="editAdmins"
        <?php if(!isset($_SESSION['tab2_error']) && !isset($_SESSION['tab2_success'])) echo "aria-selected=\"true\""; else echo "aria-selected=\"false\"";?> data-toggle="tab" role="tab" href="#editAdmins">Add & Edit Admins</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php if(isset($_SESSION['tab2_error']) || isset($_SESSION['tab2_success'])) echo "active"; ?>" aria-controls="changeRights"
      <?php if(isset($_SESSION['tab2_error']) || isset($_SESSION['tab2_success'])) echo "aria-selected=\"true\""; else echo "aria-selected=\"false\"";?> data-toggle="tab" role="tab" href="#changeRights">Change Admin Roles</a>
    </li>
  </ul>

  <!-- Content of the tabs -->
  <div class="tab-content">

    <!-- The edit admins tab -->
    <div id="editAdmins" class="tab-pane <?php if(!isset($_SESSION['tab2_error']) && !isset($_SESSION['tab2_success'])) echo "show active"; ?>" role="tabpanel">
      <!-- Change or remove existing Admin Roles -->
      <div class="row mt-4">
        <div class="col-md-12 mx-auto">
          <?php echo flash('tab1_success'); ?>
          <?php echo flash('tab1_error'); ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">User</th>
                <th scope="col">Role</th>
                <th scope="col">Handle</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($data['admins'] as $key => $admin): ?>
                <form method="post" action="<?php echo URLROOT; ?>/adminRoles/edit" id="form-<?php echo $admin->name; ?>">
                  <tr>
                    <th scope="row"><?php echo $key + 1; ?></th>
                    <td><span style="color: <?php echo $data['adminRoles'][$admin->adminRole]->colorCode; ?>"><?php echo $admin->name; ?></span></td>
                    <td>
                      <input type="text" name="adminId" value="<?php echo $admin->id; ?>" hidden>
                      <select class="custom-select custom-select-sm" id="selector-<?php echo $admin->name; ?>" name="changeToRole">
                        <option value="0">Remove Admin Rights</option>
                        <?php foreach($data['adminRoles'] as $id => $adminRole): ?>
                          <option value="<?php echo $id; ?>" <?php if($id == $admin->adminRole) echo "selected"; ?>><?php echo $adminRole->name; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td>
                      <button type="button" class="btn btn-success btn-sm changeAdmins" data-toggle="modal" data-target="#changeModal"
                              data-admin-name="<?php echo $admin->name; ?>"
                              data-admin-old-role="<?php echo $data['adminRoles'][$admin->adminRole]->name; ?>">
                        Change admin rights
                      </button>
                    </td>
                  </tr>
                </form>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Change or remove existing Admin Roles -->

      <hr>

      <!-- Add an Admin Role -->
      <div class="row">
        <div class="card col-12">
          <div class="card-body">
            <h5 class="card-title">
              Make Someone Admin :D
            </h5>
            <p class="card-text">
              Here you can make some new an admin!
            </p>
            <form method="post" class="form-inline" action="<?php echo URLROOT; ?>/adminRoles/edit">
              <input type="text" class="form-control mb-2 mr-sm-2"  placeholder="Enter Username" name="username" required>

              <select class="form-control mb-2 mr-sm-2" name="adminRole" required>
                <option disabled selected value> -- select an option -- </option>
                <?php foreach($data['adminRoles'] as $id => $adminRole): ?>
                  <option value="<?php echo $id; ?>"><?php echo $adminRole->name; ?></option>
                <?php endforeach; ?>
              </select>
              <input type="submit" name="addAdmin" value="Make Admin" class="btn btn-success mb-2">
            </form>
          </div>
        </div>
      </div>
      <!-- Add an Admin Role -->


      <!-- Change Admin Roles Modal -->
      <div class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="changeModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Confirmation</h5>
                </div>
                <div class="modal-body">
                  Are you sure you want to change the admin rights of <span id="changeTargetName" class="font-weight-bold"></span> from <span id="oldFunction" class="font-italic"></span> to <span id="newFunction" class="font-italic"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-ok" id="continueChangeAdminRoles">Continue</button>
                </div>
            </div>
        </div>
      </div>
      <!-- Change Admin Roles Modal -->
    </div>
    <!-- The edit Admins tab -->




    <!-- Change the admin rights -->
    <div class="tab-pane <?php if(isset($_SESSION['tab2_error']) || isset($_SESSION['tab2_success'])) echo "show active"; ?>" id="changeRights" role="tabpanel">
      <div class="row mt-4">
        <div class="col-md-12 mx-auto">
          <?php echo flash('tab2_success'); ?>
          <?php echo flash('tab2_error'); ?>
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                What do you want to do?
              </h5>
              <p class="card-text">
                By selecting an option underneath, you can add a new admin role or you can edit an existing role.
                For each role, you can choose a name and a color in which the admin's names will be shown. In the table below, you can select the rights that are assigned to this role.
              </p>
              <select class="form-control" id="selectRole">
                <option selected value="0">Create a new admin role</option>
                <?php foreach($data['adminRoles'] as $id => $adminRole): ?>
                  <option value="<?php echo $id; ?>" data-colorCode="<?php echo $adminRole->colorCode;?>" data-rights='<?php echo $adminRole->rights; ?>' data-name="<?php echo $adminRole->name; ?>">Edit <?php echo $adminRole->name; ?></option>
                <?php endforeach; ?>
              </select>

              <form method="post" id="editRoleForm" class="form-inline mt-5" action="<?php echo URLROOT; ?>/adminRoles/edit">
                <input type="number" name="roleId" value="0" hidden>
                <input type="text" class="form-control mb-2 mr-sm-2" placeholder="Enter Role name" name="roleName" required>
                <input type="color" class="form-control mb-2 mr-sm-2 col-md-2" name="colorCode" value="#000000" required>
                <input type="submit" name="createRole" id="createRoleBtn" value="Create Role" class="btn btn-success mb-2 mr-2">

                <table class="table table-striped mt-2">
                  <thead>
                    <tr>
                      <th scope="col"></th>
                      <th scope="col">Right</th>
                      <th scope="col">Description</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($data['adminRights'] as $key => $adminRight): ?>
                      <tr>
                        <td><input type="checkbox" class="form-control" name="adminRight-<?php echo $key; ?>"></td>
                        <td><?php echo $adminRight->name; ?></td>
                        <td><?php echo $adminRight->description; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </form>
            </div>
          </div> <!-- End Card -->
        </div>
      </div> <!-- End row -->

      <!-- Change Admin Roles Modal -->
      <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Confirmation</h5>
                </div>
                <div class="modal-body">
                  Are you sure you want to delete the role <span id="deleteRoleName" class="font-weight-bold"></span>?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-success btn-ok" id="continueDeleteAdminRole">Continue</button>
                </div>
            </div>
        </div>
      </div>
      <!-- Change Admin Roles Modal -->

    </div> <!-- End tab -->
  </div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
