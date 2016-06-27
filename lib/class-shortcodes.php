<?php

function subscribenow_form(){

    if(!empty($_GET['confirm']) && !empty($_GET['email'])){
        require_once(SUBSCRIBE_NOW_PLUGIN_DIR . 'lib/class-member.php');
        $member = new Member();
        if(md5($_GET['email'])===$_GET['confirm'] && $member->checkEmailExist($_GET['email'])){
            wp_enqueue_script('subscribenow-verify-ajax');
            include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-verify.php';
        }else{
            include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form-email-doesnot-exist.php';
        }
    }else{
        wp_enqueue_script('subscribenow-ajax');
        include SUBSCRIBE_NOW_PLUGIN_DIR . 'templates/shortcode-subscribe-form.php';
    }
}

add_shortcode('subscribenow-form', 'subscribenow_form');
