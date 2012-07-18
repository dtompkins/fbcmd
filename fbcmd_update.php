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
//   Copyright (c) 2007,2010 Dave Tompkins                                    //
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

  $fbcmdUpdateVersion = '3.0';
  TraceVar('fbcmdUpdateVersion');

////////////////////////////////////////////////////////////////////////////////

  print "\n";
  print "fbcmd update utility [version {$fbcmdUpdateVersion}]\n";
  print "http://fbcmd.dtompkins.com/update\n\n";

////////////////////////////////////////////////////////////////////////////////

  if (!extension_loaded('openssl')) {
    print "Warning: openssl exenstion is not installed.\n";
    exit;
  }

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
  $fbcmdPrefs['install_script_name'] = 'fbcmd';
  if ($isWindows) {
    $fbcmdPrefs['install_lib_dir'] = CleanPath($thisProgramFolder);
    $fbcmdPrefs['install_copy_to_bin'] = '0';
    $fbcmdPrefs['install_bin_dir'] = 'c:\\windows';
  } else {
    $fbcmdPrefs['install_lib_dir'] = '/usr/local/lib/fbcmd/';
    $fbcmdPrefs['install_copy_to_bin'] = '1';
    $fbcmdPrefs['install_bin_dir'] = '/usr/local/bin/';
  }
  $fbcmdPrefs['install_lib_mkdir_mode'] = '0777';
  $fbcmdPrefs['install_data_mkdir_mode'] = '0700';
  $fbcmdPrefs['install_auto_restart'] = '1';
  $defaultLibDir = $fbcmdPrefs['install_lib_dir'];
  $fbcmdPrefs['mkdir_mode'] = '0777';
  TraceVar('defaultLibDir');

////////////////////////////////////////////////////////////////////////////////

  $isSudo = false;
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
      if (getenv('SUDO_USER')) {
        $isSudo = true;
        $sudoUser = getenv('SUDO_USER');
        $fbcmdBaseDir = '~' . getenv('SUDO_USER') . '/.fbcmd/';
      } else {
        $fbcmdBaseDir = CleanPath(getenv('HOME')) . '.fbcmd/';
      }
    }
  }
  TraceVar('fbcmdBaseDir');
  TraceVar('isSudo');

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
    if ($argv[2]) {
      $fbcmdPrefs['install_lib_dir'] = $argv[2];
      MakeLibDir();
      $fbcmdPrefs['install_lib_dir'] = CleanPath(realpath($fbcmdPrefs['install_lib_dir']));
      $isSavePrefs = true;
    }
  }

////////////////////////////////////////////////////////////////////////////////

  $keywords = array('-h','help','--help','clear','install','remove','script','sudo');

  $isHelp = false;
  $isKeyword = false;
  if (in_array($specifiedBranch,$keywords)) {
    if (($specifiedBranch=='-h')||($specifiedBranch=='help')||($specifiedBranch=='--help')) {
      $isHelp = true;
    }
    $isKeyword = true;
    if ($specifiedBranch=='sudo') {
      $isSudo = true;
    }
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
    print "\nParameters:  [branch|keyword] [folder] [trace] [ignore_err]\n\n";
    print "branch:      Software development branch\n";
    print "                 master   stable, not all features available\n";
    print "                 beta     reaonably stable, subject to minor changes\n";
    print "                 dev      latest features (experimental)\n\n";
    print "keyword:     Instead of a branch, you can specify one of:\n";
    print "                 clear    clear your personal user settings\n";
    print "                 help     display this message\n";
    print "                 install  install with default branch\n";
    print "                 remove   removes fbcmd from your system\n";
    print "                 script   generate the fbcmd script only\n";
    print "                 sudo     (Mac/Linux) create lib_dir & script only\n\n";
    print "folder:      Specify a destination installation directory (0 for default)\n\n";
    print "trace:       Defaults to 0.  Set to 1 for verbose output\n\n";
    print "ignore_err:  Defaults to 0.  Set to 1 to ignore fatal errors\n\n\n";
  }

////////////////////////////////////////////////////////////////////////////////

  if (($isIncludeFile == false)&&($specifiedBranch != 'remove')&&($specifiedBranch != 'clear')&&($isSudo == false)) {
    if (!file_exists($fbcmdBaseDir)) {
      if (mkdir($fbcmdBaseDir,octdec($fbcmdPrefs['install_data_mkdir_mode']),true)) {
        Trace("creating directory [{$fbcmdBaseDir}]");
      } else {
        print "Error: cound not create directory: [{$fbcmdBaseDir}]\n";
        FatalError();
      }
      if (!$isWindows) {
        if (chmod($fbcmdBaseDir,octdec($fbcmdPrefs['install_data_mkdir_mode']))) {
          Trace("chmod [{$fbcmdBaseDir}]");
        } else {
          Trace("Error: chmod [{$fbcmdBaseDir}] (non-fatal)");
        }
      }
    }
    $isSavePrefs = true;
  }

////////////////////////////////////////////////////////////////////////////////

  if (($isSavePrefs)&&($isSudo == false)) {
    $fileContents = SavePrefsContents();
    if (file_put_contents($prefsFile,$fileContents)) {
      Trace("creating file [{$prefsFile}]");
    } else {
      print "Error: cound not create file: [{$prefsFile}] (non-fatal)\n";
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if (($isHelp)||($isFirstInstall)) {
    print "Preference file:                 [{$prefsFile}]\n\n";
    print "Software development branch:     [{$fbcmdPrefs['update_branch']}]\n";
    print "Software library destination:    [{$fbcmdPrefs['install_lib_dir']}]\n";
    print "Copy script to bin dir?:         ";
    if ($fbcmdPrefs['install_copy_to_bin']) {
      print "[Yes]\n";
      print "Bin dir location:                [{$fbcmdPrefs['install_bin_dir']}]\n";
    } else {
      print "[No]\n";
    }
    print "Script name:                     [{$fbcmdPrefs['install_script_name']}]\n";
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
      print "To finish the installation:\n\n";
      if ($isWindows) {
        print "   php fbcmd_update.php\n\n";
      } else {
        print "   $ sudo php fbcmd_update.php sudo\n";
        print "   $ php fbcmd_update.php\n\n";
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
    if ($fbcmdPrefs['install_copy_to_bin']) {
      if ($isWindows) {
        $pathShell = CleanPath($fbcmdPrefs['install_bin_dir']) . $fbcmdPrefs['install_script_name'] . '.bat';
      } else {
        $pathShell = CleanPath($fbcmdPrefs['install_bin_dir']) . $fbcmdPrefs['install_script_name'];
      }
      DeleteFileOrDirectory($pathShell);
    }
    DeleteFileOrDirectory($fbcmdPrefs['install_lib_dir']);
    exit;
  }

////////////////////////////////////////////////////////////////////////////////

  $installLibDir = $fbcmdPrefs['install_lib_dir'];
  MakeLibDir();
  $installLibDir = CleanPath(realpath($installLibDir));
  $installLibDirOS = $installLibDir;
  if ($isWindows) {
    $installLibDirOS = str_replace('/', '\\', $installLibDirOS);
  }
  TraceVar('installLibDir');
  TraceVar('installLibDirOS');

  $mainFile = "{$installLibDir}fbcmd.php";
  $updateFile = "{$installLibDir}fbcmd_update.php";
  if ($isWindows) {
    $scriptName = $fbcmdPrefs['install_script_name'] . '.bat';
  } else {
    $scriptName = $fbcmdPrefs['install_script_name'];
  }
  $fullScriptName = "{$installLibDir}$scriptName";
  $fullBinScript = CleanPath($fbcmdPrefs['install_bin_dir']) . $scriptName;
  TraceVar('fullScriptName');
  TraceVar('fullBinScript');

////////////////////////////////////////////////////////////////////////////////

  $comment = "This script file was auto-generated by [{$updateFile}]";

  if ($isWindows) {
    $contentsBatch = "@echo off\n";
    $contentsBatch .= "REM *** {$comment}\n";
    $contentsBatch .= "php \"$mainFile\" %*\n";
  } else {
    $contentsBatch = "#! /bin/bash\n";
    $contentsBatch .= "# *** {$comment}\n";
    $contentsBatch .= "php \"$mainFile\" \"$@\" -col=$(tput cols)\n";
  }

  $isMakeScript = false;
  if (!file_exists($fullBinScript)) {
    $isMakeScript = true;
  }
  if ($specifiedBranch == 'script') {
    $isMakeScript = true;
  }
  if ($isSudo) {
    $isMakeScript = false;
  }
  TraceVar('isMakeScript');

  if ($isMakeScript) {
    if (file_put_contents($fullScriptName,$contentsBatch)) {
      Trace ("created script: [{$fullScriptName}]");
      if (!$isWindows) {
        if (chmod($fullScriptName,octdec($fbcmdPrefs['install_lib_mkdir_mode']))) {
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

////////////////////////////////////////////////////////////////////////////////

  $isCopyToBin = false;
  if ($fbcmdPrefs['install_copy_to_bin']) {
    if (!file_exists($fullBinScript)) {
      $isCopyToBin = true;
    }
    if (($specifiedBranch == 'script')||($isSudo == true)) {
      $isCopyToBin = true;
    }
  }
  TraceVar('isCopyToBin');

  if ($isCopyToBin) {
    CheckPath($fbcmdPrefs['install_bin_dir']);
    if (file_put_contents($fullBinScript,$contentsBatch)) {
      Trace ("created script: [{$fullBinScript}]");
      if (!$isWindows) {
        if (isset($sudoUser)) {
          if (chown($fullBinScript,$sudoUser)) {
            Trace ("chown script: [{$fullBinScript}] [{$sudoUser}]");
          } else {
            print "error chown: [{$fullBinScript}] [{$sudoUser}] (non-fatal)\n";
          }
        }
        if (chmod($fullBinScript,octdec($fbcmdPrefs['install_lib_mkdir_mode']))) {
          Trace ("chmod script: [{$fullBinScript}]");
        } else {
          print "error chmod: [{$fullBinScript}] (non-fatal)\n";
        }
      }
    } else {
      print "Error: cound not create file: [{$fullBinScript}]\n";
      FatalError();
    }
  }

  if (($specifiedBranch == 'script')||($specifiedBranch == 'sudo')) {
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
          $execString = "php \"{$updateFile}\" \"{$specifiedBranch}\" \"{$installLibDir}\" $isTrace $isContinueOnError";
          passthru($execString);
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

  if ($specifiedBranch != 'install') {
    if (!$fbcmdPrefs['install_copy_to_bin']) {
      if (stripos(getenv('PATH'),substr($installLibDirOS,0,strlen($installLibDirOS)-1)) === false) {
        print "\nNote: Your PATH does not appear to include {$installLibDirOS}\n";
        if ($isWindows) {
          print "(right click) My Computer -> Properties -> Advanced -> Environment Variables\n";
          print "Edit the PATH entry and add: ;{$installLibDirOS}\n";
        } else {
          print "Add the following line to your ~/.bash_profile file:\n";
          print "  PATH=\$PATH:{$installLibDirOS}; export PATH\n";
        }
      }
    }
  }

  if (realpath($thisProgram) != realpath($updateFile)) {
    print "\nNote: fbcmd_update.php is now at [{$updateFile}]\n";
    print "so you can remove the old one at [" . realpath($thisProgram) . "]\n\n";
  }

  if ($oldMainVersion == 'none') {
    print "\ntype " . $fbcmdPrefs['install_script_name'] . " to begin\n\n";
  }

  print "\n";

  exit;

  function DeleteFileOrDirectory($dir) { # snagged from http://ca3.php.net/rmdir
    Trace("deleting [{$dir}]");
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
    global $installLibDir;
    $fileSrc = "https://raw.github.com/dtompkins/fbcmd/{$branch}/{$filename}";
    $fileDest = "{$installLibDir}{$filename}";
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
      if (mkdir($filePath,octdec($fbcmdPrefs['mkdir_mode']),true)) {
        Trace("creating [{$filePath}]");
      } else {
        print "Error creating [{$filePath}]\n";
        FatalError();
      }
    }
  }

  function MakeLibDir() {
    global $fbcmdPrefs;
    global $isWindows;
    global $isSudo;
    global $sudoUser;

    $dir = $fbcmdPrefs['install_lib_dir'];

    if (!file_exists($dir)) {
      if (mkdir($dir,octdec($fbcmdPrefs['install_lib_mkdir_mode']),true)) {
        Trace("creating directory [{$dir}]");
      } else {
        print "Error: cound not create directory: [{$dir}]\n";
        FatalError();
      }
    }
    if (!$isWindows) {
      if ($isSudo) {
        if (isset($sudoUser)) {
          if (chown($dir,$sudoUser)) {
            Trace ("chown [{$dir}] [{$sudoUser}]");
          } else {
            print "error chown: [{$dir}] [{$sudoUser}] (non-fatal)\n";
          }
        }
        if (chmod($dir,octdec($fbcmdPrefs['install_lib_mkdir_mode']))) {
          Trace ("chmod [{$dir}]");
        } else {
          print "error chmod: [{$dir}] (non-fatal)\n";
        }
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
    print str_pad($key,25,' ') . '[' . $fbcmdPrefs[$key] . "]\n";
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
    $fileContents = "<?php\n";
    foreach ($fbcmdPrefs as $switchKey => $switchValue) {
      if ($switchKey != 'prefs') {
        $fileContents .= "  \$fbcmdPrefs['{$switchKey}'] = " . var_export($switchValue,true) . ";\n";
      }
    }
    $fileContents .= "?>\n";
    return $fileContents;
  }

?>
