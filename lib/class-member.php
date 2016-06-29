<?php


class Member{

  public function __CONSTRUCT()
  {

  }

  public function checkEmailExist($email){
    global $wpdb;
    $email = filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL);
    if(!empty($email)){
      $subscriber = $wpdb->get_row( $wpdb->prepare("SELECT `email`,`status`,`activation_key` FROM {$wpdb->prefix}subscribers WHERE `email` LIKE %s LIMIT 1",$email));
      if(count($subscriber) > 0){
        if($subscriber->status === "2")
        return 'unverified';
        return true;
      }
    }
    return false;
  }
  public function addMemberInfo($email,$fullname,$nickname,$contact){
    global $wpdb;
    $email = filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL);
    if(!empty($email)){
      // Check if member exist
      if(!$this->checkEmailExist($email)) return 'notfound';
      $wpdb->query(sprintf("UPDATE {$wpdb->prefix}subscribers SET
        `fullname` = '%s' ,
        `displayname` = '%s' ,
        `contact` = '%s' ,
        `status` = 1
        WHERE
        `email` = '%s' LIMIT 1
        ",$fullname,$nickname,$contact,$email));
        return true;
      }
      return false;
    }

    public function addMemberToList($email){
      global $wpdb;
      $email = filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL);
      if(!empty($email)){
        // Check if member exist
        $isExist = $this->checkEmailExist($email);
        if($isExist!=false) return $isExist;
        
        $wpdb->query("INSERT into {$wpdb->prefix}subscribers SET
          `email` = '". $email ."' ,
          `activation_key` = '". md5($email) ."' ,
          `status` = 2
          ");
          return true;
        }
        return false;
      }
    }
