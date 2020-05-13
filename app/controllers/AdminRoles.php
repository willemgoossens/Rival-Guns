<?php
  class AdminRoles extends Controller
  {
    public function __construct()
    {
      $this->defaultMethod = "edit";

      $this->userModel = $this->model('User');
      $this->adminRightModel = $this->model('AdminRight');
      $this->adminRoleModel = $this->model('AdminRole');
      $this->conversationModel = $this->model('Conversation');

      // Set the sessions for the nav bar
      $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
      $this->data['user']->adminRights         = $this->adminRoleModel->getRightsForInterface($this->data['user']->adminRole);
      $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
    }

    /*******************************
    *
    *
    * This Page allows users to Edit the existing admin roles, and they can assign the roles to new people
    *
    *
    *******************************/
    public function edit()
    {
      if(! in_array("EditAdminRoles", $this->data['user']->adminRights))
      {
        redirect('posts');
      }

      // In this variable the data is stored
      $this->data['JSFile'] = 'localFiles';
      $this->data['adminRights'] = $this->adminRightModel->get(true);

      // IF THERE IS A POST METHOD
      if($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);




        //Change admin's role
        if(isset($_POST['changeAdmin']))
        {
          if($_POST['changeToRole'] == 0)
          {
            $_POST['changeToRole'] = NULL;
          }

          $targetUser = $this->userModel->getSingleById($_POST['adminId'], 'id', 'name');

          if($targetUser
            && $this->userModel->updateById($_POST['adminId'], ['adminRole' => $_POST['changeToRole']]))
          {
            flash('tab1_success', 'You have changed the role of <strong>' . ucfirst($targetUser->name) . '</strong>!');
          }
          else
          {
            die('Something went wrong');
          }
        }
        ////////

        // Create new admin
        if(isset($_POST['addAdmin']))
        {
          $targetUser = $this->userModel->getSingleByName($_POST['username'], 'id');
          // Check if this user exists
          if(!$targetUser)
          {
            flash('tab1_error', '<strong>' . ucfirst($_POST['username']) . '</strong> is not a valid username.', 'alert alert-danger');
          }
          elseif(isset($targetUser->adminRole))
          {
            flash('tab1_error', '<strong>' . ucfirst($_POST['username']) . '</strong> already has an admin role.', 'alert alert-danger');
          }
          else
          {
            if($this->userModel->updateById($targetUser->id, ['adminrole' => $_POST['adminRole']]))
            {
              flash('tab1_success', '<strong>' . ucfirst($_POST['username']) . '</strong> has gotten a new admin role.');
            }
            else
            {
              die('Something went wrong!');
            }
          }
        }
        ////////

        // Delete role
        // We need to do some extra checks because the deleteRole button is not submitted (due to the confirmation modal)
        if(isset($_POST['deleteRole']))
        {
          $adminRole = $this->adminRoleModel->getSingleById($_POST['roleId']);

          if($this->userModel->existsByAdminRole($_POST['roleId']))
          {
            flash('tab2_error', 'There are still admins with this role. Their permissions have to be removed before the role can be removed.', 'alert alert-danger');
          }
          else
          {
            if($this->adminRoleModel->deleteById($_POST['roleId']))
            {
              flash('tab2_success', 'The role of <strong>' . $adminRole->name . '</strong> has been deleted!');
            }
            else
            {
              die('Something went wrong!');
            }
          }
        }
        ////////

        // Edit role
        if(isset($_POST['editRole']))
        {
          $nameTaken = $this->adminRoleModel->getByNameAndNotId($_POST['roleName'], $_POST['roleId']);

          if(empty($_POST['roleName']))
          {
            flash('tab2_error', 'A name for this role is required!', 'alert alert-danger');
          }
          elseif(empty($_POST['colorCode']))
          {
            flash('tab2_error', 'A color for this role is required!', 'alert alert-danger');
          }
          elseif($nameTaken)
          {
            flash('tab2_error', 'The name of <strong>' . $_POST['roleName'] . '</strong> is already in use!', 'alert alert-danger');
          }
          else
          {
            // Get which rights have been checked
            $addedRights = [];
            foreach($this->data['adminRights'] as $key => $adminRight)
            {
              if(isset($_POST['adminRight-' . $key]))
                array_push($addedRights, $key);
            }

            $updateArray = [
              'name' => $_POST['roleName'],
              'colorCode' => $_POST['colorCode']
            ];
            // For the association between rights and role
            // We delete all associations for this specific role and reinstall them again
            if($this->adminRoleModel->updateById($_POST['roleId'], $updateArray)
                && $this->adminRoleModel->updateRightsForRole($_POST['roleId'], $addedRights)
            ){
              // Since the right-role connection is very simple, we will just delete all connections and insert them again
              flash('tab2_success', 'The role of <strong>' . $_POST['roleName'] . '</strong> has been edited!');
            }
            else
            {
              die('Something went wrong!');
            }
          }
        }
        ///////

        // Create Role
        if(isset($_POST['createRole']))
        {
          $adminRole = $this->adminRoleModel->getByName($_POST['roleName']);

          if(empty($_POST['roleName']))
          {
            flash('tab2_error', 'A name for this role is required!', 'alert alert-danger');
          }
          elseif(empty($_POST['colorCode']))
          {
            flash('tab2_error', 'A color for this role is required!', 'alert alert-danger');
          }
          elseif($adminRole)
          {
            flash('tab2_error', 'The name of <strong>' . $_POST['roleName'] . '</strong> is already in use!', 'alert alert-danger');
          }else{
            // Get which rights have been checked
            $addedRights = [];
            foreach($this->data['adminRights'] as $key => $adminRight)
            {
              if(isset($_POST['adminRight-' . $key]))
                array_push($addedRights, $key);
            }

            $insertArray = [
              "name" => $_POST['roleName'],
              "colorCode" => $_POST['colorCode']
            ];
            $lastId = $this->adminRoleModel->insert($insertArray, true);

            if($lastId && $this->adminRightModel->createAdminRightsForRole($lastId, $addedRights))
            {
              // Since the right-role connection is very simple, we will just delete all connections and insert them again
              flash('tab2_success', 'The role of <strong>' . $_POST['roleName'] . '</strong> has been create!');
            }else
            {
              die('Something went wrong!');
            }
          }
        }
        ////////

      }
      // END POST METHOD

      // Get the existing Admin Roles
      $adminRoles = $this->adminRoleModel->get();
      // We need to encode the rights to JSON
      foreach($adminRoles as $adminRole)
      {
        $rightsForRole = $this->adminRoleModel->getManyToManyIds("adminRights", $adminRole->id);
        $adminRights = $this->adminRightModel->getById($rightsForRole);
        $adminRole->rights = json_encode($adminRights);

        $this->data['adminRoles'][$adminRole->id] = $adminRole;
      }

      $this->data['admins'] = $this->userModel->getByNotAdminRole(NULL);

      $this->view('adminRoles/edit', $this->data);
    }

  }
