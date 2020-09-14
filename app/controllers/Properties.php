<?php
    namespace App\Controllers;
    use App\Libraries\Controller as Controller;
    
    class Properties extends Controller
    {
        public function __construct()
        {
            // Set the sessions for the nav bar
            $this->data['user']                      = $this->userModel->getSingleById($_SESSION['userId']);
            $this->data['user']->adminRights         = $this->adminRoleModel->getRightNamesForRole($this->data['user']->adminRole);
            $this->data['user']->conversationUpdates = $this->conversationModel->countUnreadConversations($_SESSION['userId']);
            $this->data['user']->notifications = $this->notificationModel->getUnreadNotifications($_SESSION['userId']);
        }

        
        /**
         * 
         * 
         * Index
         * @param Int $page
         * @return Void
         * 
         * 
         */
        public function index ( Int $page = 1 ): Void
        {
            $user = &$this->data['user'];

            $user->amountOfProperties = $this->propertyModel->countByUserId( $user->id );

            $limit = 15;
            if( ( $page - 1 ) * $limit > $user->amountOfProperties )
            {
                $page = 1;
            }
            $offset = ( $page - 1 ) * $limit;
            
            $user->properties = $this->propertyModel->offset( $offset )
                                                    ->limit( $limit )
                                                    ->getByUserId( $user->id );
            $propertyCategoryIds = array_column( $user->properties, 'id' );

            $propertyCategories = $this->propertyCategoryModel->getFlaggedUniqueById( $propertyCategoryIds );

            $this->data['propertyCategories'] = $propertyCategories;
            $this->data['page'] = $page;
            $this->data['propertiesPerPage'] = $limit;

            $this->view('properties/index', $this->data);
        }


        /**
         * 
         * 
         * Show
         * @param Int propertyId
         * @return Void
         * 
         * 
         */
        public function show(Int $propertyId): Void
        {
            echo $propertyId;
        }

    }