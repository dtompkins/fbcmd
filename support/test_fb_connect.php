#!/usr/bin/php
<?php

  $GLOBALS['facebook_config']['debug'] = false;
  
  set_include_path (get_include_path() . PATH_SEPARATOR . '../');
  
  try {
    if(!include_once('facebook/facebookapi_php5_restlib.php')) throw new Exception('');
    if(!include_once('facebook/facebook.php')) throw new Exception('');
    if(!include_once('facebook/facebook_desktop.php')) throw new Exception('');
  } catch (Exception $e) {
    print 'Missing Facebook API files: can\'t find facebook*.php in ' . get_include_path();
    print "\n";    
    exit;
  
  }
  try {
    $fbObject = new FacebookDesktop('d96ea311638cf65f04b33c87eacf371e','88af69b7ab8d437bff783328781be79b');
    $fbObject->api_client->session_key = 'a3808f378339d63cb6577fb3-100000121930079';
    $fbObject->secret = '29111dbad8e37f819dd1c83a21cb0b98';
    $fbObject->api_client->secret = '29111dbad8e37f819dd1c83a21cb0b98';
    $fbUser = $fbObject->api_client->users_getLoggedInUser();
    if ($fbUser == '100000121930079') {
      print "Successfully connected to Facebook as Test User!";
    } else {
      print "Unexpected error: Facebook returned username as: [$fbUser]";
    }
  } catch (Exception $e) {
    print "Exception! [" . $e->getCode() . "] " . $e->getMessage();
  }
  print "\n";
?>