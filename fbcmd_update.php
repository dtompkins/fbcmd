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

  if (isset($argv[0])) {
    $thisProgram = $argv[0];
  } else {
    $thisProgram = "./fbcmd_update.php";
  }
  $thisProgramFolder = dirname($thisProgram);
  
  print "\nFBCMD Update Utility -- version 2.3\n\n";
  print "http://fbcmd.dtompkins.com/update for help\n\n";
  
  print "syntax:    php fbcmd_update.php [branch: master|beta|dev] [folder] [ignore_err]\n";
  print "default:   php fbcmd_update.php master \"{$thisProgramFolder}\" 0\n\n";
  
  $specifiedBranch = '';
  if (isset($argv[1])) {
    $specifiedBranch = strtolower($argv[1]);
    if (($specifiedBranch=='-h')||($specifiedBranch=='help')||($specifiedBranch=='--help')) {
      exit;
    }
  }

  if (isset($argv[2])) {
    $installFolder = $argv[2];
  } else {
    $installFolder = $thisProgramFolder;
  }
  
  $continueOnError = false;
  if (isset($argv[3])) {
    if ($argv[3]) {
      $continueOnError = true;
    }
  }
  
  $fullPath = realpath($installFolder);
  if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    $fullPath = str_replace('/', '\\', $fullPath);
  }  
  print "Installation folder: {$fullPath}\n";  
  $installFolder = CleanPath($installFolder);
  CheckPath($installFolder);
  
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
    print "Loading: preference file: {$fbcmdBaseDir}prefs.php\n";
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
  
  if (!in_array($branch,array('master','beta','dev'))) {
    print "Warning: [$branch] is not one of [master|beta|dev]\n";
  }

  $oldVersion = 'none';
  $newVersion = '';
  
  $mainFile = "{$installFolder}fbcmd.php";
  $newProgram = "{$installFolder}fbcmd_update.php";
  
  if (file_exists($mainFile)) {
    print "Found: existing fbcmd: {$mainFile}\n";
    $oldFileContents = @file_get_contents($mainFile);
    preg_match ("/fbcmdVersion\s=\s'([^']+)'/",$oldFileContents,$matches);
    if (isset($matches[1])) {
      $oldVersion = $matches[1];
    } else {
      print "Non-fatal error: could not determine old version\n";
    }
  }
  
  $contentsCurrentUpdater = '';  
  if (file_exists($thisProgram)) {
    print "Loading current updater: [{$thisProgram}]... ";
    $contentsCurrentUpdater = @file_get_contents($thisProgram);
    if ($contentsCurrentUpdater) {
      print "ok\n";
    } else {
      print "fail! (non-fatal)\n";
    }
  }

  $contentsRemoteUpdater = GetGithub("fbcmd_update.php",false);
  
  if ($contentsCurrentUpdater == $contentsRemoteUpdater) {
    print "Current updater is up to date\n";
  } else {
    print "Current updater does not match (is out of date)\n";
    print "Saving new version: [$newProgram]...";
    if (@file_put_contents("$newProgram",$contentsRemoteUpdater)) {
      print "ok\n";
      if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
        print "Making new updater executable...";
        if (chmod("$newProgram",0755)) {
          print "ok\n";
        } else {
          print "fail (do it maually: chmod +x fbcmd_updater.php)\n";
        }
      }
      if (realpath($thisProgram) == realpath($newProgram)) {
        print "\nUpdate INCOMPLETE: Restart this program (it has updated itself)\n\n";
      } else {
        print "\nUpdate INCOMPLETE: run the NEW updater: [$newProgram]\n\n";
      }
      FatalError();
    } else {
      print "fail!\n";
      print "Fatal error: could not save [$newProgram]\n\n";
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

  $execPath = realpath($mainFile);
  if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    $contentsBatch = "@echo off\n";
    $contentsBatch .= "REM *** {$comment1}\n";
    $contentsBatch .= "REM *** {$comment2}\n";
    $contentsBatch .= "REM *** {$comment3}\n";
    $contentsBatch .= "php \"$execPath\" %*\n";
    $batchName = "{$installFolder}fbcmd.bat";
  } else {
    $contentsBatch = "#! /bin/bash\n";
    $contentsBatch .= "# *** {$comment1}\n";
    $contentsBatch .= "# *** {$comment2}\n";
    $contentsBatch .= "# *** {$comment3}\n";
    $contentsBatch .= "php \"$execPath\" $* -print_wrap_width=$(tput cols)\n";
    $batchName = "{$installFolder}fbcmd";
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
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
      print "(right click) My Computer -> Properties -> Advanced -> Environment Variables\n";
      print "Edit the PATH entry and add: ;{$fullPath}\n";
    } else {
      print "Add the following line to your ~/.bash_profile file:\n";
      print "  PATH=\$PATH:{$fullPath}; export PATH\n";
    }
  }
  
  if (!$isFbcmdSet) {
    if (file_exists("{$fbcmdBaseDir}sessionkeys.txt")) {    
      print "\nNote: fbcmd is storing your key files and preferences in: {$fbcmdBaseDir}\n";
    } else {
      print "\nBy default, fbcmd will store your key files and preferences in: {$fbcmdBaseDir}\n";
      print "You can set the the environment variable FBCMD to change this location\n";
    }
  }

  print "\n";
  
  exit;
  
  function GetGithub($filename, $save = true) {
    global $branch;
    global $installFolder;
    $fileSrc = "http://github.com/dtompkins/fbcmd/raw/{$branch}/{$filename}";
    $fileDest = "{$installFolder}{$filename}";
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
