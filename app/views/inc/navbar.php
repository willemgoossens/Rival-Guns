<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
  <div class="container">
      <a class="navbar-brand" href="<?php echo URLROOT; ?>"><?php echo SITENAME; ?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/pages/about">About</a>
          </li>
          <?php
            if(! empty($this->data['user']->adminRights)):
          ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Admin
              </a>
              <div class="dropdown-menu" aria-labelledby="adminDropdown">
                <?php
                  if(in_array("EditAdminRoles", $this->data['user']->adminRights)):
                ?>
                  <a class="dropdown-item" href="<?php echo URLROOT; ?>/adminRoles/edit">Edit admin role</a>
                <?php
                  endif;
                  if(in_array("HandleReportedConversations", $this->data['user']->adminRights)):
                ?>
                  <a class="dropdown-item" href="<?php echo URLROOT; ?>/conversationReports">Reported Conversations</a>
                <?php
                  endif;
                ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Something else here</a>
              </div>
            </li>
          <?php
            endif;

            if(isset($_SESSION['userId'])): 
          ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="personalDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Personal
                </a>
                <div class="dropdown-menu" aria-labelledby="personalDropdown">
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/profile">My profile</a>
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/properties">My properties</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="activitiesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Activities
                </a>
                <div class="dropdown-menu" aria-labelledby="activitiesDropdown">
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/crimes">Crimes</a>
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/mafiajobs">Mafia jobs</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="locationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Locations
                </a>
                <div class="dropdown-menu" aria-labelledby="locationDropdown">
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/locations/bank">Bank</a>
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/locations/hospital">Hospital</a>
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/locations/hoovers">Harry's Hoovers</a>
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/locations/realEstate">Real Estate Office</a>
                </div>
              </li>
            <?php
              endif;
            ?>
        </ul>

        <ul class="navbar-nav ml-auto">
          <?php if(isset($this->data['user'])) : ?>
          <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/profile">Welcome <?php echo $data['user']->name; ?></a>
          </li>
          <li class="nav-item dropdown" id="notificationsDropdown" data-url="<?php echo URLROOT; ?>/api/notifications/read">
            <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-exclamation-circle"></i>
              <?php if(count($this->data['user']->notifications) > 0) { echo '<span id="notificationCounter" class="badge badge-pill badge-danger">' . count($this->data['user']->notifications) . '</span>'; } ?>
            </a>
            <div class="dropdown-menu" aria-labelledby="notificationsDropdown">                    
                <?php
                    if(! empty($this->data['user']->notifications)):
                        foreach($this->data['user']->notifications as $notification):
                ?>
                            <a class="notification-item dropdown-item <?php echo $notification->class; ?>" href="<?php echo 'URLROOT' . $notification->link; ?>"><?php echo $notification->body; ?></a>
                <?php
                        endforeach;
                    endif;
                ?>
                <div id="noNotificationsMessage" class="dropdown-item font-italic <?php echo empty($this->data['user']->notifications) ? "":"d-none"; ?>">No new notifications</div>
                <div class="text-center">
                    <a class="font-italic" href="<?php echo URLROOT; ?>/notifications">See all notifications</a>
                </div>

            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/conversations">
              <i class="fa fa-envelope"></i>
              <?php if($this->data['user']->conversationUpdates > 0) { echo '<span class="badge badge-pill badge-danger">' . $this->data['user']->conversationUpdates . '</span>'; } ?>
            </a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/users/editProfile">Edit Profile</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/users/logout">Logout</a>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/users/register">Register</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo URLROOT; ?>/users/login">Login</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
