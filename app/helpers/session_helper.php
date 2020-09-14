<?php
  session_start();

  /**
   * 
   * 
   * Flash
   * @param String name [Flash name]
   * @param String message [Optional - message]
   * @param String class [Optional - flash class]
   * @return Void
   * 
   * 
   */
  function flash(String $name, String $message = '', String $class = 'alert alert-success'): Void
  {
      if(
          ! empty($message) 
          && empty($_SESSION[$name])
      ) {
          if(! empty($_SESSION[$name]))
          {
              unset($_SESSION[$name]);
          }

          if(! empty($_SESSION[$name. '_class']))
          {
              unset($_SESSION[$name. '_class']);
          }

          $_SESSION[$name] = $message;
          $_SESSION[$name. '_class'] = $class;
      } 
      elseif(
          empty($message) 
          && ! empty($_SESSION[$name])
      ) {
          $class = ! empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : '';

          echo '<div class="'.$class.'" id="msg-flash">'.$_SESSION[$name].'</div>';

          unset($_SESSION[$name]);
          unset($_SESSION[$name. '_class']);
      }
  }
