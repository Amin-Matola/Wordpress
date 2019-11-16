<?php
declare(strict_types = 1);
namespace UserManager;

class Users{

  /********************** Singleton PHP Design ******************/
  private static $instance;
  protected $user;
  
  private function __construct(int $user_id){
        if(!empty($user)){
            $this -> user = get_user_by("id", $user);
            }
         else{
            $this -> user = wp_get_current_user();
         }
  
  }

  
  /****************** get the user instance *********************/
  public static function getInstance(int $user_id = null){
      self::$instance     = new self($user_id);
      return self::$instance
      }
  
  
  /******************** Get current user object******************/
  public fucntion get_user(){
        if(empty($this->user))
              return false;
        return $this -> user;
  }
  
  
  /*************** Get all users by a specified role ************/
  public function get_all_users(string $role = ''){
            $specified_users = get_users(array("role" => $role));
            return $specified_users;
  }
  
  
  /************* Get all capabilities concerned with a role *****/
  public function get_role_capabilities(string $role = ""){
          return get_role($role) -> capabilities; 
  }
  
  /******************** Change User Email ***********************/
  public function change_user_email(string $email = ""){
    if(!empty($email)){
          $wp_upd       = wp_update_user(array("ID" => $this->get_user()->ID, "user_email" => $email));
          if(is_wp_error($wp_upd))
             return "Update Error : ".$wp_upd->get_error_message();
           return true;
         }
    return false;
  }
   
  
  /******************** Creating New User **********************/
  public function create_user($name, $email, $password){
    $user_id      = wp_insert_user(
                    array("user_login" => $name, "user_email" => $email, "user_pass" => $password, "display_name" => $name)
                                  );
    if(!is_wp_error($user_id)) return true;
    return false;
    
  }
  
  
  /***************************** Deleting A User *************************/
  public function remove_user(int $user = 0){
      if(!isset($user)) $user = $this -> get_user()->ID;
      $action     = wp_delete_user($user);
      if(is_wp_error($action)) return false;
      return true;
    }
  
  /***************** Give a user some role/s ***************************/
  public function give_user_roles(int $user = 0, array $roles = []){
      if(!isset($user)) $user = $this -> get_user()-> ID;
      else
        $user = new WP_User($user);
      if(is_array($roles)){
          foreach($roles as $role){
              $user -> add_role($role);
          }
      }
  }
  
  
  /************************** Handle Abnormalies *****************************/
  public function __call($func, $args){
      return "The function you called, $func, does not exist in the ".__CLASS__." class."; 
  }
  
  
  public function __callStatic($func, $args){
    return "The call to a static function $func inside ".__CLASS__." is not allowed.";
  }
  
}













?>
