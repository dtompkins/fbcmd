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
  
////////////////////////////////////////////////////////////////////////////////  

  $isTrace = 0;
  if (isset($argv[3])) {
    if ($argv[3]) {
      $isTrace = 1;
    }
  }
  $isTrace = 1;
  
////////////////////////////////////////////////////////////////////////////////    

  $isContinueOnError = 0;
  if (isset($argv[4])) {
    if ($argv[4]) {
      $isContinueOnError = 1;
    }
  }
  TraceVar('isContinueOnError');
  
////////////////////////////////////////////////////////////////////////////////  

// Note: The Installer version is independent of the fbcmd version

  $fbcmdUpdateVersion = '2.6';
  TraceVar('fbcmdUpdateVersion');
  
////////////////////////////////////////////////////////////////////////////////  
  
  print "\n";
  print "fbcmd update utility [version {$fbcmdUpdateVersion}]\n";
  print "http://fbcmd.dtompkins.com/update\n";
  print "for basic syntax: php fbcmd_update.php help\n\n";  
  
////////////////////////////////////////////////////////////////////////////////  
  
  if (isset($argv[0])) {
    $thisProgram = $argv[0];
  } else {
    $thisProgram = "./fbcmd_update.php";
  }
  $thisProgramFolder = realpath(dirname($thisProgram));  
  TraceVar('thisProgram');
  TraceVar('thisProgramFolder');
  
////////////////////////////////////////////////////////////////////////////////  
  
  if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    $isWindows = true;
  } else {
    $isWindows = false;
  }
  TraceVar('isWindows');
  
////////////////////////////////////////////////////////////////////////////////  
  
  // Set the defaults for fbcmd... these can be overridden in prefs.php
  $fbcmdPrefs['update_branch'] = 'master';
  if ($isWindows) {
    $fbcmdPrefs['install_dir'] = CleanPath($thisProgramFolder);
    $fbcmdPrefs['install_copy_to_path'] = '0';
    $fbcmdPrefs['install_path_dir'] = '';
  } else {
    $fbcmdPrefs['install_dir'] = '/usr/local/lib/fbcmd/';
    $fbcmdPrefs['install_copy_to_path'] = '1';
    $fbcmdPrefs['install_path_dir'] = '/usr/local/bin/';
  }
  $fbcmdPrefs['mkdir_mode'] = 0777;
  $fbcmdPrefs['install_auto_restart'] = '1';
  $defaultInstallDir = $fbcmdPrefs['install_dir'];
  TraceVar('defaultInstallDir');
  
////////////////////////////////////////////////////////////////////////////////  
  
  $envFbcmd = getenv('FBCMD');
  TraceVar('envFbcmd');
  if ($envFbcmd) {
    $fbcmdBaseDir = CleanPath($envFbcmd);  
  } else {
    if ($isWindows) {
      if (getenv('USERPROFILE')) {
        $fbcmdBaseDir = CleanPath(getenv('USERPROFILE')) . 'fbcmd/';
      } else {
        $fbcmdBaseDir = 'c:/fbcmd/';
      }
    } else {
      $fbcmdBaseDir = CleanPath(getenv('HOME')) . '.fbcmd/';
    }
  }
  TraceVar('fbcmdBaseDir');
  
////////////////////////////////////////////////////////////////////////////////  

  $isSavePrefs = false;
  $prefsFile = "{$fbcmdBaseDir}prefs.php";
  if (file_exists($prefsFile)) {
    include($prefsFile);
    $isIncludeFile = true;
  } else {
    $isIncludeFile = false;
  }
  TraceVar('isIncludeFile');  
  
////////////////////////////////////////////////////////////////////////////////  

  $specifiedBranch = '';
  if (isset($argv[1])) {
    $specifiedBranch = strtolower($argv[1]);
  }
  TraceVar('specifiedBranch');
  
////////////////////////////////////////////////////////////////////////////////  

  if (isset($argv[2])) {
    $fbcmdPrefs['install_dir'] = $argv[2];
    CheckPath($fbcmdPrefs['install_dir']);
    $fbcmdPrefs['install_dir'] = CleanPath(realpath($fbcmdPrefs['install_dir']));
    $isSavePrefs = true;
  } 
  
////////////////////////////////////////////////////////////////////////////////    

  $keywords = array('-h','help','--help','clear','install','remove','script');
  
  $isHelp = false;
  $isKeyword = false;
  if (in_array($specifiedBranch,$keywords)) {
    if (($specifiedBranch=='-h')||($specifiedBranch=='help')||($specifiedBranch=='--help')) {
      $isHelp = true;
    }
    $isKeyword = true;
  }
  TraceVar('isKeyword');
  TraceVar('isHelp');
  
////////////////////////////////////////////////////////////////////////////////  
  
  if ((($specifiedBranch == '')||($isHelp))&&($isIncludeFile == false)) {
    $isFirstInstall = true;
  } else {
    $isFirstInstall = false;
  }
  TraceVar('isFirstInstall');  
  
////////////////////////////////////////////////////////////////////////////////  
  
  if ($isHelp) {
    print "\nphp fbcmd_update.php [branch|keyword] [folder] [trace] [ignore_err]\n\n";
    print "branch:      Software development branch\n";
    print "                 master   stable, not all features available\n";
    print "                 beta     reaonably stable, subject to minor changes\n";
    print "                 dev      latest features, expect changes\n\n";
    print "keyword:     Instead of a branch, you can specify one of:\n";
    print "                 help     display this message\n";
    print "                 install  performa a full install\n";
    print "                 script   regenerate the fbcmd script\n";
    print "                 clear    clear your personal user settings\n";    
    print "                 remove   removes fbcmd from your system\n\n";
    print "folder:      Specify a destination installtion directory\n\n";
    print "trace:       Defaults to 0.  Set to 1 for verbose output\n\n";
    print "ignore_err:  Defaults to 0.  Set to 1 to ignore fatal errors\n\n\n";
  }
  
////////////////////////////////////////////////////////////////////////////////  
  
  if (($isIncludeFile == false)&&($specifiedBranch != 'remove')&&($specifiedBranch != 'clear')) {
    if (!file_exists($fbcmdBaseDir)) {
      if (mkdir($fbcmdBaseDir,0700,true)) {
        Trace("creating directory [{$fbcmdBaseDir}]");
      } else {
        print "Error: cound not create directory: [{$fbcmdBaseDir}]\n";
        FatalError();
      }
    }
    $isSavePrefs = true;
  }
  
////////////////////////////////////////////////////////////////////////////////    

  if ($isSavePrefs) {
    $fileContents = SavePrefsContents();
    if (file_put_contents($prefsFile,$fileContents)) {
      Trace("creating file [{$prefsFile}]");
    } else {
      print "Error: cound not create file: [{$prefsFile}]\n";
      FatalError();
    }
  }
  
////////////////////////////////////////////////////////////////////////////////  
  
  if (($isHelp)||($isFirstInstall)) {
    print "Preference file:                 [{$prefsFile}]\n\n";
    print "Software development branch:     [{$fbcmdPrefs['update_branch']}]\n";
    print "Software library destination:    [{$fbcmdPrefs['install_dir']}]\n";
    print "Copy script to path?:            ";
    if ($fbcmdPrefs['install_copy_to_path']) {
      print "[Yes]\n";
      print "Path location:                   [{$fbcmdPrefs['install_path_dir']}]\n\n";
    } else {
      print "[No]\n";
    }
    print "Auto-restart when necessary:     ";
    if ($fbcmdPrefs['install_auto_restart']) {
      print "[Yes]\n\n";
    } else {
      print "[No]\n\n";
    }
    
    
    if ($isFirstInstall) {
      print "\n\n";
      print "Welcome!  This appears to be the first time running fbcmd_update.\n";
      print "\n";
      print "To change any of the above settings, modify your preferences file\n";
      print "To change your preferences file location, set an FBCMD environment var.\n\n";
      print "Otherwise, The above default settings are fine for most users\n\n";      
      print "To finish the installation, re-execute this command\n";
      if ($isWindows) {
        print "\n   php fbcmd_update.php\n\n";
      } else {
        print "\n   $ sudo php fbcmd_update.php\n\n";
      }
    }
    exit;
  }
  
////////////////////////////////////////////////////////////////////////////////    

  if (($specifiedBranch == 'clear')||($specifiedBranch == 'remove')) {
    DeleteFileOrDirectory($fbcmdBaseDir);
    if ($specifiedBranch == 'clear') {
      exit;
    }
  }
  
  if ($specifiedBranch == 'remove') {
    if ($fbcmdPrefs['install_copy_to_path']) {
      if ($isWindows) {
        $pathShell = CleanPath($fbcmdPrefs['install_path_dir']) . "fbcmd.bat";
      } else {
        $pathShell = CleanPath($fbcmdPrefs['install_path_dir']) . "fbcmd";
      }
      DeleteFileOrDirectory($pathShell);
    }
    DeleteFileOrDirectory($fbcmdPrefs['install_dir']);
    exit;
  }
  
////////////////////////////////////////////////////////////////////////////////      

  $installFolder = $fbcmdPrefs['install_dir'];
  CheckPath($installFolder);  
  $installFolder = CleanPath(realpath($installFolder));
  $installFolderOS = $installFolder;
  if ($isWindows) {
    $installFolderOS = str_replace('/', '\\', $installFolderOS);
  }
  TraceVar('installFolder');  
  TraceVar('installFolderOS');
  
  $mainFile = "{$installFolder}fbcmd.php";
  $updateFile = "{$installFolder}fbcmd_update.php";  
  if ($isWindows) {
    $scriptName = "fbcmd.bat";
  } else {
    $scriptName = "fbcmd";
  }
  $fullScriptName = "{$installFolder}$scriptName";
  TraceVar('fullScriptName');  
  
////////////////////////////////////////////////////////////////////////////////    
  
  $comment = "This script file was auto-generated by [{$updateFile}]";
  
  if ($isWindows) {
    $contentsBatch = "@echo off\n";
    $contentsBatch .= "REM *** {$comment}\n";
    $contentsBatch .= "php \"$mainFile\" %*\n";
  } else {
    $contentsBatch = "#! /bin/bash\n";
    $contentsBatch .= "# *** {$comment}\n";
    $contentsBatch .= "php \"$mainFile\" $* -col=$(tput cols)\n";
  }
  if (file_put_contents($fullScriptName,$contentsBatch)) {
    Trace ("created script: [{$fullScriptName}]");
    if (!$isWindows) {
      if (chmod($fullScriptName,0777)) {
        Trace ("chmod script: [{$fullScriptName}]");
      } else {
        print "error chmod: [{$fullScriptName}] (non-fatal)\n";
      }
    }
  } else {
    print "Error: cound not create file: [{$fullScriptName}]\n";
    FatalError();
  }
  
////////////////////////////////////////////////////////////////////////////////          
  
  if (($fbcmdPrefs['install_copy_to_path'])&&(($specifiedBranch == 'install')||($specifiedBranch == 'script'))) {
    $fullScriptName = CleanPath($fbcmdPrefs['install_path_dir']) . $scriptName;
    TraceVar('fullScriptName');
    if (file_put_contents($fullScriptName,$contentsBatch)) {
      Trace ("created script: [{$fullScriptName}]");
      if (!isWindows) {
        if (chmod($scriptName,0777)) {
          Trace ("chmod script: [{$fullScriptName}]");
        } else {
          print "error chmod: [{$fullScriptName}] (non-fatal)\n";
        }
      }
    } else {
      print "Error: cound not create file: [{$fullScriptName}]\n";
      FatalError();
    }
  }
  
  if ($specifiedBranch == 'script') {
    exit;
  }
  
////////////////////////////////////////////////////////////////////////////////        
  
  $defaultBranch = strtolower($fbcmdPrefs['update_branch']);
  $branch = $defaultBranch;
  if (($specifiedBranch)&&($specifiedBranch != 'install')) {
    $branch = $specifiedBranch;
    Trace("overriding default branch: [{$defaultBranch}]");
  }
  TraceVar('defaultBranch');
  TraceVar('branch');
  
////////////////////////////////////////////////////////////////////////////////          

  print "...";
  $contentsRemoteUpdater = GetGithub("fbcmd_update.php",false);
  preg_match ("/fbcmdUpdateVersion\s=\s'([^']+)'/",$contentsRemoteUpdater,$matches);
  $newUpdateVersion = 0;
  if (isset($matches[1])) {
    $newUpdateVersion = $matches[1];
  }
  TraceVar('newUpdateVersion');
  
////////////////////////////////////////////////////////////////////////////////          
  
  if (($newUpdateVersion > $fbcmdUpdateVersion)||(!file_exists($updateFile))) {
    if (file_put_contents($updateFile,$contentsRemoteUpdater)) {
      Trace("creating [{$updateFile}]");
      if ($newUpdateVersion > $fbcmdUpdateVersion) {
        if ($fbcmdPrefs['install_auto_restart']) {
          print "\nNewer update software downloaded [{$fbcmdUpdateVersion}] -> [{$newUpdateVersion}]\n";
          print "\nattempting to restart...\n";
          $execString = "php {$updateFile} \"{$specifiedBranch}\" \"{$installFolder}\" $isTrace $isContinueOnError";
          exec ($execString);
          exit;
        } else {
          if (realpath($thisProgram) == realpath($updateFile)) {
            print "\nUpdate INCOMPLETE: Restart this program (it has updated itself)\n\n";
          } else {
            print "\nUpdate INCOMPLETE: run the NEW updater: [$updateFile]\n";
            print "(you might want to remove this old one to avoid confusion)\n\n";
          }
          FatalError();
        }
      }
    } else {
      print "Fatal error: could not save [$updateFile]\n\n";
      FatalError();
    }
  }
  
////////////////////////////////////////////////////////////////////////////////          
  
  $oldMainVersion = 'none';
  $newMainVersion = '';
  if (file_exists($mainFile)) {
    $oldFileContents = @file_get_contents($mainFile);
    preg_match ("/fbcmdVersion\s=\s'([^']+)'/",$oldFileContents,$matches);
    if (isset($matches[1])) {
      $oldMainVersion = $matches[1];
    } else {
      $oldMainVersion = '???';
    }
  }
  TraceVar('oldMainVersion');
  
////////////////////////////////////////////////////////////////////////////////          

  print ".";
  $fileList = GetGithub("filelist.txt");
  $files = explode("\n",$fileList);
  foreach ($files as $f) {
    $g = preg_replace('/\s*\#.*$/','',$f);
    if ($g) {
      print ".";
      $contents = GetGithub($g);
      if ($g == 'fbcmd.php') {
        preg_match("/fbcmdVersion\s=\s'([^']+)'/",$contents,$matches);
        if (isset($matches[1])) {
          $newMainVersion = $matches[1];
        } else {
          print "Non-fatal error: could not determine new version\n";
          $newMainVersion = '???';
        }
      }      
    }
  }
  print "\n";  
  
////////////////////////////////////////////////////////////////////////////////          
  
  print "\nUpdate: COMPLETE!\n\n";
  print "fbcmd version: [{$oldMainVersion}] --> [{$newMainVersion}]\n";
  
  if (!$fbcmdPrefs['install_copy_to_path']) {
    if (stripos(getenv('PATH'),substr($installFolderOS,0,strlen($installFolderOS)-1)) === false) {
      print "\nNote: Your PATH does not appear to include {$installFolderOS}\n";
      if ($isWindows) {
        print "(right click) My Computer -> Properties -> Advanced -> Environment Variables\n";
        print "Edit the PATH entry and add: ;{$installFolderOS}\n";
      } else {
        print "Add the following line to your ~/.bash_profile file:\n";
        print "  PATH=\$PATH:{$installFolderOS}; export PATH\n";
      }
    }
  }
  
  if (realpath($thisProgram) != realpath($updateFile)) {
    print "\nNote: fbcmd_update.php is now at [{$updateFile}]\n";
    print "so you can remove the old one at [{$thisProgram}]\n\n";
  }  

  print "\n";
  
  exit;
  
  function DeleteFileOrDirectory($dir) { # snagged from http://ca3.php.net/rmdir
    Trace('deleting [{$dir}]');
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) {
      if (unlink($dir)) {
        return true;
      } else {
        print "Could Not Delete File: [{$dir}]\n";
        FatalError();      
      }
    }
    foreach (scandir($dir) as $item) {
      if ($item == '.' || $item == '..') continue;
      if (!DeleteFileOrDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
    }
    if (rmdir($dir)) {
      return true;
    } else {
      print "Could Not Delete Directory: [{$dir}]\n";
      FatalError();      
    }
  }
  
  function GetGithub($filename, $save = true) {
    global $branch;
    global $installFolder;
    $fileSrc = "http://github.com/dtompkins/fbcmd/raw/{$branch}/{$filename}";
    $fileDest = "{$installFolder}{$filename}";
    $fileContents = @file_get_contents($fileSrc);
    if ($fileContents) {
      Trace("downloading: [$fileSrc}]");
    } else {
      print "Could not download: [{$fileSrc}]\n";
      FatalError();
    }
    if ($save) {
      CheckPath(dirname($fileDest));
      if (@file_put_contents($fileDest,$fileContents)) {
        Trace("saving: [{$fileDest}]");
      } else {
        print "Could not save: [{$fileDest}]\n";
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
      if (mkdir($filePath,$fbcmdPrefs['mkdir_mode'],true)) {
        Trace("creating [{$filePath}]");
      } else {
        print "Error creating [{$filePath}]\n";
        FatalError();
      }
    }
  }
  
  function FatalError() {
    global $isContinueOnError;
    if ($isContinueOnError) {
      print "Ignoring Error...\n";
      return;
    }
    exit;
  }

  function PrintPref($key) {
    global $fbcmdPrefs;
    if ($key == 'mkdir_mode') {
      print str_pad($key,25,' ') . '[' . decoct($fbcmdPrefs[$key]) . "]\n";
    } else {
      print str_pad($key,25,' ') . '[' . $fbcmdPrefs[$key] . "]\n";
    }
  }
  
  function Trace($line) {
    global $isTrace;
    if ($isTrace) {
      print "$line\n";
    }
  }
  
  function TraceVar($varName) {
    Trace("$varName = [" . $GLOBALS[$varName] . "]");
  }
  
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // Copied Directly from fbcmd.php

  function SavePrefsContents() {
    global $fbcmdPrefs;
    if (isset($fbcmdPrefs['savepref_include_files'])) {
      $includeFiles = $fbcmdPrefs['savepref_include_files'];
    } else {
      $includeFiles = false;
    }
    $fileContents = "<?php\n";
    foreach ($fbcmdPrefs as $switchKey => $switchValue) {
      if ($switchKey != 'prefs') {
        if (($includeFiles)||(($switchKey != 'keyfile')&&($switchKey != 'postfile')&&($switchKey != 'mailfile'))) {
          if ($switchKey == 'mkdir_mode') {
            $fileContents .= "  \$fbcmdPrefs['{$switchKey}'] = 0" . decoct($switchValue) . ";\n";
          } else {
            $fileContents .= "  \$fbcmdPrefs['{$switchKey}'] = " . var_export($switchValue,true) . ";\n";        
          }
        }
      }
    }
    $fileContents .= "?>\n";
    return $fileContents;
  }
  
?>
