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

// This is a support file for inclding FBCMD within other programs

  function FbcmdInitInclude() {
    global $argv;
    global $argc;
    global $fbcmd_argv;
    global $fbcmd_argc;

    global $fbcmd_include;
    global $fbcmd_include_newCommands;
    global $fbcmd_include_supressOutput;
    global $fbcmd_include_bypassCommands;

    $fbcmd_argv = $argv;
    $fbcmd_argc = $argc;

    $fbcmd_include = true;

    $fbcmd_include_newCommands = array();
    $fbcmd_include_supressOutput = false;
    $fbcmd_include_bypassCommands = false;
    ob_start();
  }

  function FbcmdAddArgument($p) {
    global $fbcmd_argv;
    global $fbcmd_argc;
    $fbcmd_argv[] = $p;
    $fbcmd_argc++;
  }

  function FbcmdAddCommands() {
    global $fbcmd_include_newCommands;
    $newCommands = func_get_args();
    foreach ($newCommands as $cmd) {
      $fbcmd_include_newCommands[] = $cmd;
    }
  }

?>
