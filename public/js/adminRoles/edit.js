$(document).ready(function(){

  $('#changeModal').on('shown.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var admin_name = button.data('admin-name'); // Extract info from data-* attributes
    var admin_old_role = button.data('admin-old-role'); // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this);
    modal.find('#changeTargetName').text(admin_name);
    modal.find('#oldFunction').text(admin_old_role);
    modal.find('#newFunction').text($("#selector-" + admin_name).children('option:selected').text());

    $('#continueChangeAdminRoles').on('click', function(){
      $('#form-' + admin_name).append('<input type="text" name="changeAdmin" value=1 class="d-none" />');
      $('#form-' + admin_name).submit();
    });
  });


  $('#deleteModal').on('shown.bs.modal', function () {
    $("#deleteRoleName").text($("#selectRole").children("option:selected").data('name'));

    $('#continueDeleteAdminRole').on('click', function(){
      $('#editRoleForm').append('<input type="text" name="deleteRole" value=1 class="d-none" />');
      $('#editRoleForm').submit();
    });
  });


  var deleteRoleBtn = '<button type="button" class="btn btn-danger mr-2 mb-2" data-toggle="modal" id="deleteRoleBtn" data-target="#deleteModal">Delete Role</button>';
  var editRoleBtn = '<input type="submit" name="editRole" id="editRoleBtn" value="Edit Role" class="btn btn-success mr-2 mb-2">';
  var createRoleBtn = '<input type="submit" name="createRole" id="createRoleBtn" value="Create Role" class="btn btn-success mr-2 mb-2">';

  $("#selectRole").on("change", function(){
    $("#deleteRoleBtn").remove();
    $("#editRoleBtn").remove();
    $("#createRoleBtn").remove();
    $(":checkbox").prop("checked", false);

    if($(this).children("option:selected").val() == 0){
      $("[name='colorCode']").after(createRoleBtn);
      $("[name='roleId']").val(0);
      $("[name='roleName']").val("");
      $("[name='colorCode']").val("#000000");
    }else{
      $("[name='colorCode']").after(deleteRoleBtn);
      $("[name='colorCode']").after(editRoleBtn);
      var selectedOption = $(this).children("option:selected");
      $("[name='roleId']").val(selectedOption.val());
      $("[name='roleName']").val(selectedOption.data('name'));
      $("[name='colorCode']").val(selectedOption.data('colorcode'));
      console.log(selectedOption.data('rights'));
      selectedOption.data('rights').forEach((item) => {
        $(":checkbox[name='adminRight-" + item.id + "']").prop("checked", true);
      });
    }
  });

});
