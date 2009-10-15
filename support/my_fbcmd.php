#!/usr/bin/php
<?php

////////////////////////////////////////////////////////////////////////////////
//     __ _                        _                                          //
//    / _| |                      | |                                         //
//   | |_| |__   ___ _ __ ___   __| |                                         //
//   |  _| '_ \ / __| '_ ` _ \ / _` |                                         //
//   | | | |_) | (__| | | | | | (_| |                                         //
//   |_| |_.__/ \___|_| |_| |_|\__,_|                                         //
//                                                                            //
//   Facebook Command Line Interface Utility                                  //
//   http://facebook.com/fbcmd                                                //
//   http://fbcmd.dtompkins.com                                               //
//   Copyright (c) 2007,2009 Dave Tompkins                                    //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//  see fbcmd.php for copyright information                                   //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

// This is a sample program to show how to create your own COMMAND(S) for FBCMD
// see: http://fbcmd.dtompkins.com/help/how-to/add-command for more info.

// *** WARNING: Do *NOT* modify this file: if you use the fbcmd UPDATE
//              this file will be updated & replaced.  Create your own instead.

// If you have a new command and would like to share it with others, visit:
// http http://fbcmd.dtompkins.com/contribute

////////////////////////////////////////////////////////////////////////////////
// Step One: include the fbcmd_include.php

  require 'fbcmd_include.php';

////////////////////////////////////////////////////////////////////////////////
// Step Two: Run the FbcmdInitInclude() procedure

  FbcmdIncludeInit();

////////////////////////////////////////////////////////////////////////////////
// Step Three: Add any arguments to be appended automatically

  FbcmdIncludeAddArgument('-quiet=0');
  FbcmdIncludeAddArgument('-facebook_debug=0');

////////////////////////////////////////////////////////////////////////////////
// Step Four: List your new commands so that FBCMD will recognize them

  FbcmdIncludeAddCommand('FNAMES','Display all your friend\'s names');
  FbcmdIncludeAddCommand('MYNOTES','Display all of your notes');
  FbcmdIncludeAddCommand('SINGLE','Display all of your single friends');

////////////////////////////////////////////////////////////////////////////////
// Step Five: Include (run) FBCMD

  require '../fbcmd.php';

////////////////////////////////////////////////////////////////////////////////
// Step Six: Add your own commands:

  if ($fbcmdCommand == 'FNAMES') {
    GetFlistIds("=all");
    foreach ($flistMatchArray as $friendId) {
      print ProfileName($friendId) . "\n";
    }
  }

  if ($fbcmdCommand == 'MYNOTES') {
    $fbReturn = $fbObject->api_client->notes_get($fbUser);
    foreach ($fbReturn as $note) {
      Print "{$note['title']}\n\n{$note['content']}\n\n\n";
    }
  }

  if ($fbcmdCommand == 'SINGLE' ) {
    $fql = "SELECT name FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1={$fbUser}) AND relationship_status='single'";
    $fbReturn = $fbObject->api_client->fql_query($fql);
    foreach ($fbReturn as $singleFriend) {
      Print $singleFriend['name'] . "\n";
    }
  }

////////////////////////////////////////////////////////////////////////////////

?>
