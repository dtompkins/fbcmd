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

  print "\nFBCMD Update Utility -- version 2.2\n\n";
  print "http://fbcmd.dtompkins.com/update for help\n\n";
  
  print "syntax:    php fbcmd_update.php [BRANCH] [DIRECTORY] [CONTINUE_ON_ERR]\n";
  print "default:   php fbcmd_update.php master . 0\n\n";
  
  $specifiedBranch = '';
  if (isset($argv[1])) {
    $specifiedBranch = strtolower($argv[1]);
  }
  
  if (isset($argv[2])) {
    $installDir = CleanPath($argv[2]);
  } else {
    $installDir = CleanPath('.');
  }
  
  $continueOnError = false;
  if (isset($argv[3])) {
    if ($argv[3]) {
      $continueOnError = true;
    }
  }

  CheckPath($installDir);
  $fullPath = realpath($installDir);
  if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    $fullPath = str_replace('/', '\\', $fullPath);
  }  
  print "Installation folder: {$fullPath}\n";  
  
  $fbcmdBaseDir = getenv('FBCMD');
  if ($fbcmdBaseDir) {
    $isFbcmdSet = true;
    print "Found: environment variable FBCMD={$fbcmdBaseDir}\n";
    $fbcmdBaseDir = CleanPath($fbcmdBaseDir);
  } else {
    $isFbcmdSet = false;
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      if (getenv('USERPROFILE')) {
        $fbcmdBaseDir = CleanPath(getenv('USERPROFILE')) . 'fbcmd/';
      } else {
        $fbcmdBaseDir = 'c:/fbcmd/';
      }
    } else {
      $fbcmdBaseDir = CleanPath(getenv('HOME')) . '.fbcmd/';
    }
    print "Not found: environment variable FBCMD (default is {$fbcmdBaseDir})\n";
  }

  // set some simple defaults:
  $fbcmdPrefs['mkdir_mode'] = 0777;
  $fbcmdPrefs['update_branch'] = 'master';
  
  if (file_exists("{$fbcmdBaseDir}prefs.php")) {
    print "Found: preference file: {$fbcmdBaseDir}prefs.php\n";
    print "Loading: preferences...\n";
    include("{$fbcmdBaseDir}prefs.php");
  } else {
    print "Not found: preference file: [{$fbcmdBaseDir}prefs.php] (using defaults)\n";
  }
  
  $defaultBranch = strtolower($fbcmdPrefs['update_branch']);
  if ($specifiedBranch) {
    $branch = $specifiedBranch;
    if ($defaultBranch != $branch) {
      print "Overriding default branch: [$defaultBranch] with [$branch]\n";
    } else {
      print "Specified and default branch are both: [$branch]\n";
    }
  } else {
    $branch = $defaultBranch; 
    print "Using default branch: [$branch]\n";
  }

  $oldVersion = 'none';
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
  
  if ($contentsCurrentUpdater == $contentsRemoteUpdater) {
    print "Current or local updater is identical\n";
  } else {
    print "Current or local updater does not match\n";
    print "Saving new updater: [{$installDir}fbcmd_update.php]...";
    if (@file_put_contents("{$installDir}fbcmd_update.php",$contentsRemoteUpdater)) {
      print "ok\n";
      print "\nUpdate INCOMPLETE: restart with new updater: {$installDir}fbcmd_update.php\n\n";
      FatalError();
    } else {
      print "fail!\n";
      print "Fatal error: could not save [{$installDir}fbcmd_update.php]\n\n";
      FatalError();
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
          $newVersion = '???';
        }
      }      
    }
  }
  
  $comment1 = "This script file was auto-generated by fbcmd_update.php\n";
  $comment2 = "You should add the folder [{$fullPath}] to your PATH";
  $comment3 = "or copy this file to a pathed folder";  

  $execPath = realpath("{$installDir}fbcmd.php");
  if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    $contentsBatch = "@echo off\n";
    $contentsBatch .= "REM *** {$comment1}\n";
    $contentsBatch .= "REM *** {$comment2}\n";
    $contentsBatch .= "REM *** {$comment3}\n";
    $contentsBatch .= "php $execPath %*\n";
    $batchName = "{$installDir}fbcmd.bat";
  } else {
    $contentsBatch = "#! /bin/bash\n";
    $contentsBatch .= "# *** {$comment1}\n";
    $contentsBatch .= "# *** {$comment2}\n";
    $contentsBatch .= "# *** {$comment3}\n";
    $contentsBatch .= "php $execPath $* -print_wrap_width=$(tput cols)\n";
    $batchName = "{$installDir}fbcmd";
  }
  print "Generating script file: [{$batchName}]...";
  if (@file_put_contents("{$batchName}",$contentsBatch)) {
    print "ok\n";
    if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
      print "Making script file executable: [{$batchName}]...";
      if (chmod($batchName,0755)) {
        print "ok\n";
      } else {
        print "fail (do it maually: chmod +x fbcmd)\n";
      }
    }
  } else {
    print "fail!\n";
    FatalError();  
  }
  
  $currentPath = getenv('PATH');  
  if (stripos($currentPath,$fullPath)) {
    print "Found: current path appears to include {$fullPath}\n";
    $showPath = false;
  } else {
    $showPath = true;
  }
  
  print "\nUpdate: COMPLETE!\n\n";
  print "FBCMD Version: [{$oldVersion}] --> [{$newVersion}]\n";
  
  if ($showPath) {
    print "\nNote: Your PATH does not appear to include {$fullPath}\n";
    print "To add {$fullPath} to your path:\n";    
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
      print "(right click) My Computer -> Properties -> Advanced -> Environment Variables\n";
      print "Edit the PATH entry and add: ;{$fullPath}\n";
    } else {
      print "Add the following line to your ~/.bash_profile file:\n";
      print "  PATH=\$PATH:{$fullPath}; export PATH\n";
    }
  }
  
  if (!$isFbcmdSet) {
    print "\nNote: The environment variable FBCMD is not set\n";
    if (file_exists("{$fbcmdBaseDir}sessionkeys.txt")) {    
      print "fbcmd is storing your user files in: {$fbcmdBaseDir}\n";
    } else {
      print "By default, fbcmd will store your user files in: {$fbcmdBaseDir}\n";
    }
  }

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
      FatalError();
    }
    if ($save) {
      CheckPath(dirname($fileDest));
      print "Saving: [{$fileDest}]... ";
      if (@file_put_contents($fileDest,$fileContents)) {
        print "ok\n";
      } else {
        print "fail!\n";
        FatalError();
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
  
  function CheckPath($filePath) {
    global $fbcmdPrefs;
    if (!file_exists($filePath)) {
      print "Creating Directory: [{$filePath}]... ";
      if (mkdir($filePath,$fbcmdPrefs['mkdir_mode'],true)) {
        print "ok\n";
      } else {
        print "fail!\n";
        FatalError();
      }
    }
  }
  
  function FatalError() {
    global $continueOnError;
    if ($continueOnError) {
      print "Ignoring Error...\n";
      return;
    }
    exit;
  }
  
?>
