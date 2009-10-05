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

// This is a Utility to update your version of fbcmd

  print "\nFBCMD Update Utility -- version 2.1\n\n";
  print "http://fbcmd.dtompkins.com/update for help\n\n";
  
  print "syntax:    php fbcmd_update.php DIRECTORY [BRANCH]\n";
  print "default:   php fbcmd_update.php . master\n\n";

  if (isset($argv[1])) {
    $installDir = CleanPath($argv[1]);
  } else {
    $installDir = CleanPath('.');
  }
  print "Updating files in: {$installDir}\n";

  $dirFbcmd = getenv('FBCMD');
  if ($dirFbcmd) {
    $dirFbcmd = CleanPath($dirFbcmd);
    print "Found: environment variable FBCMD={$dirFbcmd}\n";
  } else {
    print "Not found: environment variable FBCMD\n";
    $dirFbcmd = CleanPath('.');
  }
  
  // set some simple defaults:
  $fbcmdPrefs['mkdir_mode'] = 0777;
  $fbcmdPrefs['update_branch'] = 'master';
  
  if (file_exists("{$dirFbcmd}prefs.php")) {
    print "Found: preference file: {$dirFbcmd}prefs.php\n";
    print "Loading: preferences...\n";
    include("{$dirFbcmd}prefs.php");
  } else {
    print "Not found: preference file: [{$dirFbcmd}prefs.php] (using defaults)\n";
  }
  $defaultBranch = strtolower($fbcmdPrefs['update_branch']);
  if (isset($argv[2])) {
    $branch = strtolower($argv[2]);
  } else {
    $branch = $defaultBranch;
  }
  if ($defaultBranch != $branch) {
    print "Overriding default branch: [$defaultBranch] with [$branch]\n";
  } else {
    print "Using default branch: [$branch]\n";
  }

  $oldVersion = '';
  $newVersion = '';
  if (file_exists("{$installDir}fbcmd.php")) {
    print "Found: existing fbcmd: {$installDir}fbcmd.php\n";
    $oldFbcmdFile = @file_get_contents("{$installDir}fbcmd.php");
    preg_match ("/fbcmdVersion\s=\s'([^']+)'/",$oldFbcmdFile,$matches);
    if (isset($matches[1])) {
      $oldVersion = $matches[1];
    } else {
      print "Non-fatal error: could not determine old version\n";
    }
  } else {
    print "Not found: existing fbcmd.php file (OK if new installation)\n";
  }
  
  $currentUpdater = $argv[0];
  $contentsCurrentUpdater = '';  
  if (file_exists("{$currentUpdater}")) {
    print "Loading current updater: [{$currentUpdater}]... ";
    $contentsCurrentUpdater = @file_get_contents($currentUpdater);
    if ($contentsCurrentUpdater) {
      print "ok\n";
    } else {
      print "fail! (non-fatal)\n";
    }
  } else {
    print "Non-fatal error: can't locate current updater: [{$currentUpdater}]\n";
    $localUpdater = "{$installDir}fbcmd_update.php";
    if (file_exists("{$localUpdater}")) {
      print "Loading local updater: [{$localUpdater}]... ";
      $contentsCurrentUpdater = @file_get_contents($localUpdater);
      if ($contentsCurrentUpdater) {
        print "ok\n";
      } else {
        print "fail! (non-fatal)\n";
      }
    } else {
      print "Non-fatal error: can't locate local updater: [{$currentUpdater}]\n";
    }
  }
  
  $contentsRemoteUpdater = GetGithub("fbcmd_update.php",false);
  
  if ($contentsCurrentUpdater) {
    if ($contentsCurrentUpdater == $contentsRemoteUpdater) {
      print "Current or local updater is identical\n";
    } else {
      print "Current or local updater does not match\n";
      CheckPath("{$installDir}fbcmd_update.php");
      print "Saving new updater: [{$installDir}fbcmd_update.php]...";
      if (@file_put_contents("{$installDir}fbcmd_update.php",$contentsRemoteUpdater)) {
        print "ok\n";
        print "\nUpdate INCOMPLETE: run new updater: {$installDir}fbcmd_update.php\n\n";
        if (!isset($argv[3])) {
          exit;
        }
      } else {
        print "fail!\n";
        print "Fatal error: could not save [{$installDir}fbcmd_update.php]\n\n";
        if (!isset($argv[3])) {
          exit;
        }
      }
    }
  } else {
    CheckPath("{$installDir}fbcmd_update.php");  
    print "Saving new updater: [{$installDir}fbcmd_update.php]...";
    if (@file_put_contents("{$installDir}fbcmd_update.php",$contentsRemoteUpdater)) {
      print "ok\n";
    } else {
      print "fail!\n";
      print "Fatal error: could not save [{$installDir}fbcmd_update.php]\n";
      exit;
    }
  }
  
  $fileList = GetGithub("filelist.txt");
  $files = explode("\n",$fileList);
  
  foreach ($files as $f) {
    $g = preg_replace('/\s*\#.*$/','',$f);
    if ($g) {
      $contents = GetGithub($g);
      if ($g == 'fbcmd.php') {
        preg_match("/fbcmdVersion\s=\s'([^']+)'/",$contents,$matches);
        if (isset($matches[1])) {
          $newVersion = $matches[1];
        } else {
          print "Non-fatal error: could not determine new version\n";
        }
      }      
    }
  }
  
  print "\nUpdate: COMPLETE!\n\n";
  print "FBCMD Version: [{$oldVersion}] --> [{$newVersion}]\n\n";

  $comment1 = "This script file was auto-generated";
  $comment2 = "You should either add this folder to the path";
  $comment3 = "or copy this file to a pathed folder";
  
  $execPath = realpath("{$installDir}fbcmd.php");
  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $contentsBatch = "@echo off\n";
    $contentsBatch .= "REM *** {$comment1} by fbcmd_update.php\n";
    $contentsBatch .= "REM *** {$comment2}\n";
    $contentsBatch .= "REM *** {$comment3}\n";
    $contentsBatch .= "php $execPath %*\n";
    $batchName = "{$installDir}fbcmd.bat";
  } else {
    $contentsBatch = "#! /bin/bash\n";
    $contentsBatch .= "# *** {$comment1} by fbcmd_update.php\n";
    $contentsBatch .= "# *** {$comment2}\n";
    $contentsBatch .= "# *** {$comment3}\n";
    $contentsBatch .= "php $execPath $* -print_wrap_width=$(tput cols)\n";
    $batchName = "{$installDir}fbcmd";
  }
  print "Generating script file: [{$batchName}]...";
  if (@file_put_contents("{$batchName}",$contentsBatch)) {
    print "ok\n";
  } else {
    print "fail!\n";
    exit;
  }
  print "\nScript File: $comment1\n";
  print "$comment2\n";
  print "$comment3\n";
  
  print "\n";
  
  exit;
  
  function GetGithub($filename, $save = true) {
    global $branch;
    global $installDir;
    $fileSrc = "http://github.com/dtompkins/fbcmd/raw/{$branch}/{$filename}";
    $fileDest = "{$installDir}{$filename}";
    print "Downloading: [$fileSrc]... ";
    $fileContents = @file_get_contents($fileSrc);
    if ($fileContents) {
      print "ok\n";
    } else {
      print "fail!\n";
      exit;
    }
    if ($save) {
      CheckPath($fileDest);
      print "Saving: [{$fileDest}]... ";
      if (@file_put_contents($fileDest,$fileContents)) {
        print "ok\n";
      } else {
        print "fail!\n";
        exit;
      }
    }
    return $fileContents;
  }

  function CleanPath($curPath) {
    $path = $curPath;
    if ($path == '') {
      $path = './';
    }
    $path = str_replace('\\', '/', $path);
    if ($path[strlen($path)-1] != '/') {
      $path .= '/';
    }
    return $path;
  }
  
  function CheckPath($fileName) {
    global $fbcmdPrefs;
    $filePath = dirname($fileName);
    if (!file_exists($filePath)) {
      print "Creating Directory: [{$filePath}]... ";
      if (mkdir($filePath,$fbcmdPrefs['mkdir_mode'],true)) {
        print "ok\n";
      } else {
        print "fail!\n";
        exit;
      }
    }
  }
  
?>
