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
      
}













?>
