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

  print "FBCMD Update Utility -- version 0*\n";

// This is very basic for now... needs to be updated.

  $dirFbcmd = getenv('FBCMD');
  if ($dirFbcmd) {
    $dirFbcmd = CleanPath($fbcmdBaseDir);
    print "Found: environment variable FBCMD={$dirFbcmd}\n";
    $installDir = $dirFbcmd;
  } else {
    print "Not found: environment variable FBCMD\n";
    $installDir = "./";
  }
  print "Installing software to: {$installDir}\n";
  
  $dirFbcmdConfig = getenv('FBCMD_CONFIG');
  if ($dirFbcmdConfig) {
    $dirFbcmdConfig = CleanPath($fbcmdBaseDir);
    print "Found: environment variable FBCMD_CONFIG={$dirFbcmdConfig}\n";
    $configDir = $dirFbcmdConfig;
  } else {
    print "Not found: environment variable FBCMD_CONFIG\n";
    $configDir = $installDir;
  }
  
// set some simple defaults:
  $fbcmdPrefs['pic_mkdir_mode'] = 0777;
  $fbcmdPrefs['update_branch'] = 'master';
  
  if (file_exists("{$configDir}prefs.php")) {
    print "Found: preference file: {$configDir}prefs.php\n";
    print "Loading: preferences...\n";
    include("{$fbcmdBaseDir}prefs.php");
  } else {
    print "Not found: preference file (using defaults... OK if new installation)\n";
  }
  
  if (file_exists("{$installDir}fbcmd.php")) {
    print "Found: existing fbcmd: {$installDir}fbcmd.php\n";
    $oldFbcmdFile = @file_get_contents("{$installDir}fbcmd.php");
    preg_match ("/fbcmdVersion\s=\s'([^']+)'/",$oldFbcmdFile,$matches);
    if (isset($matches[1])) {
      print "Current version: {$matches[1]}\n";
    } else {
      print "Non-fatal error: could not determine current version\n";
    }
  } else {
    print "Not found: existing fbcmd.php file (OK if new installation)\n";
  }
  
  $defaultBranch = strtolower($fbcmdPrefs['update_branch']);
  if (isset($argv[1])) {
    $branch = strtolower($argv[1]);
  } else {
    $branch = $defaultBranch;
  }
  if ($defaultBranch != $branch) {
    print "Overriding default or preferred branch: [$defaultBranch] with specified branch: [$branch]\n";
  } else {
    print "Using default or preferred branch: [$branch]\n";
  }
  
  $currentUpdater = $argv[0];
  if (file_exists("{$currentUpdater}")) {
    print "Loading local updater: [{$currentUpdater}]... ";
    $contentsCurrentUpdater = @file_get_contents($currentUpdater);
    if ($contentsCurrentUpdater) {
      print "ok\n";
    } else {
      print "fail! (non-fatal)\n";
    }
  } else {
    print "Non-fatal error: can't locate current program: [{$currentUpdater}]\n";
    $contentsCurrentUpdater = '';
  }
  
  $contentsRemoteUpdater = GetGithub("fbcmd_update.php",false);
  
  $fileDest = "{$installDir}/{$filename}";
  
  if ($contentsCurrentUpdater) {
    if ($contentsCurrentUpdater == $contentsRemoteUpdater) {
      print "Current updater is identical\n";
    } else {
      print "Current updater does not match\n";
      print "Saving: [{$installDir}/fbcmd_update.php]...";
      if (@file_put_contents("{$installDir}/fbcmd_update.php",$contentsRemoteUpdater)) {
        print "ok\n";
        print "Update INCOMPLETE: run {$installDir}/fbcmd_update.php\n";
        exit;
      } else {
        print "fail!\n";
        print "Fatal error: could not save [{$installDir}/fbcmd_update.php]\n";
      }
    }
  } else {
  
  }
 
  
  $fileList = GetGithub("filelist.txt",true);
  
  
  exit;
  
  
  function GetGithub($filename, $save = true) {
    global $branch;
    global $installDir;
    $fileSrc = "http://github.com/dtompkins/fbcmd/raw/{$branch}/{$filename}";
    $fileDest = "{$installDir}/{$filename}";
    print "Downloading: [$fileSrc]... ";
    $fileContents = @file_get_contents($fileSrc);
    if ($fileContents) {
      print "ok\n";
    } else {
      print "fail!\n";
      exit;
    }
    if ($save) {
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

  function CleanPath($curPath)
  {
    if ($curPath == '') {
      return './';
    } else {
      $curPath = str_replace('\\', '/', $curPath);
      if ($curPath[strlen($curPath)-1] != '/') {
        $curPath .= '/';
      }
    }
    return $curPath;
  }
  
  function CheckOutputDir($fileName) {
    if (strrpos($fileName,'/')) {
      $filePath = CleanPath(substr($fileName,0,strrpos($fileName,'/')));
      if (!file_exists($filePath)) {
        if (!mkdir($filePath,$fbcmdPrefs['pic_mkdir_mode'],true)) {
          print "Error: Could Not Create Path: {$filePath}";
        }
      }
    }
  }
  
?>
