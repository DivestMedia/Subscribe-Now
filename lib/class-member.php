<?php


class Member{

  public function __CONSTRUCT()
  {

  }

  function wp_get_timezone_string() {

    // if site timezone string exists, return it
    if ( $timezone = get_option( 'timezone_string' ) )
    return $timezone;

    // get UTC offset, if it isn't set then return UTC
    if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
    return 'UTC';

    // adjust UTC offset from hours to seconds
    $utc_offset *= 3600;

    // attempt to guess the timezone string from the UTC offset
    if ( $timezone = timezone_name_from_abbr( '', $utc_offset, 0 ) ) {
      return $timezone;
    }

    // last try, guess timezone string manually
    $is_dst = date( 'I' );

    foreach ( timezone_abbreviations_list() as $abbr ) {
      foreach ( $abbr as $city ) {
        if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset )
        return $city['timezone_id'];
      }
    }

    // fallback to UTC
    return 'UTC';
  }

  public function checkMemberConfirmationLink($member){
    if(!empty($member)){
      $lastupdated = new DateTime(date('Y-m-d H:i:s',strtotime($member->updated_at)));
      $timestampnow = new DateTime(date('Y-m-d H:i:s'));
      $difference = $lastupdated->diff($timestampnow);
      $interval = $difference->h + ($difference->d*24);
      $expiration = get_option('link_expiration') ?: 24;
      if($interval > $expiration){
        return 'expired';
      }else{
        return true;
      }
    }
    return false;
  }

  public function checkEmailExist($email){
    global $wpdb;
    global $table_prefix;
    $table  = $table_prefix . 'subscribenow';

    $email = filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL);

    if(!empty($email)){
      $subscriber = $wpdb->get_row( $wpdb->prepare("SELECT `email`,`status`,`activation_key`,`updated_at` FROM $table WHERE `email` LIKE %s LIMIT 1",$email));

      if(count($subscriber) > 0){
        if($subscriber->status == "0") return false;
        return $subscriber;
      }
    }
    return false;
  }
  public function addMemberInfo($email,$fullname,$nickname,$contact){
    global $wpdb;
    global $table_prefix;
    $table  = $table_prefix . 'subscribenow';

    $email = filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL);
    if(!empty($email)){
      // Check if member exist
      $member = $this->checkEmailExist($email);
      if($member==false) return 'notfound';

      $wpdb->query(sprintf("UPDATE $table SET
        `fullname` = '%s' ,
        `displayname` = '%s' ,
        `contact` = '%s' ,
        `status` = 1,
        `updated_at` = CURRENT_TIMESTAMP
        WHERE
        `email` = '%s' LIMIT 1
        ",$fullname,$nickname,$contact,$email));
        return true;
      }
      return false;
    }

    public function addMemberToList($email,$force){
      global $wpdb;
      global $table_prefix;
      $table  = $table_prefix . 'subscribenow';
      
      $email = filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL);
      if(!empty($email)){
        // Check if member exist
        $isExist = $this->checkEmailExist($email);
        if($isExist){
          if($isExist->status=='2' && !$force) return 'unverified';
          if($isExist->status=='2' && $force){
            $wpdb->query(sprintf("UPDATE $table SET
              `activation_key` = '%s' ,
              `status` = 2,
              `updated_at` = CURRENT_TIMESTAMP
              WHERE
              `email` = '%s' LIMIT 1
              ",md5($isExist->email),$isExist->email));
              return true;
            }
          }
          else if($isExist!=false) return true;
          $wpdb->query("INSERT into $table SET
            `email` = '". $email ."' ,
            `activation_key` = '". md5($email) ."' ,
            `status` = 2,
            `updated_at` = CURRENT_TIMESTAMP
            ");
            return true;
          }
          return false;
        }
      }
