#!/usr/bin/php
<?php

  $output = '';
  
  PrintOut("\n");
  PrintOut("FBCMD :: Testing misc. PHP stuff\n\n");
  PrintOut("Number of paramters = [" . ($argc-1) . "]\n");
  for ($i=1; $i < $argc; $i++) {
    PrintOut("[{$i}] [{$argv[$i]}]\n");
  }
  print "\n";
  PrintOut("PHP version = [" . phpversion() . "]\n");
  PrintOut("PHP_OS = [" . PHP_OS . "]\n");
  PrintOut("\n");
  PrintOut("php.ini : memory_limit = [" . ini_get('memory_limit') . "]\n");
  PrintOut("php.ini : allow_url_fopen = [" . ini_get('allow_url_fopen') . "]\n");
  PrintOut("\n");
  PrintOut("openssl extension loaded = [" . extension_loaded('openssl') . "]\n");
  PrintOut("\n");
  PrintOut("curl extension loaded = [" . extension_loaded('curl') . "]\n");
  PrintOut("\n");
  PrintOut("json extension loaded = [" . extension_loaded('json') . "]\n");
  PrintOut("\n");
  PrintOut("FBCMD environment variable = [" . getenv('FBCMD') . "]\n");
  PrintOut("\n");
  PrintOut("current path = [" . getcwd() . "]\n");  
  PrintOut("current script: [{$argv[0]}]\n");
  PrintOut("\n");
  print "Testing writing to current path: [test_php_output.txt]...";
  if (file_put_contents('test_php_output.txt',$output)) {
    print "ok\n";
  } else {
    print "fail\n";
  }
  print "Testing reading from current path: [test_php_output.txt]...";
  $testInput = file_get_contents('test_php_output.txt');
  if ($testInput) {
    print "ok...";
    if ($testInput == $output) {
      print "validated\n";
    } else {
      print "discrepency between intput & output\n";
    }
  } else {
    print "fail";
  }
  print ("\n");
  $remoteFile = "https://github.com/dtompkins/fbcmd/raw/master/fbcmd.php";
  print "Testing downloading from online: [{$remoteFile}]...";
  $phpFile = file_get_contents($remoteFile);
  if ($phpFile) {
    print "ok\n";
    preg_match ("/fbcmdVersion\s=\s'([^']+)'/",$phpFile,$matches);
    if (isset($matches[1])) {
      $version = $matches[1];
    } else {
      $version = 'err';
    }
    print "Online version of FBCMD master branch: {$version}\n";
  } else {
    print "fail\n";
  }
  print ("\n");
  
  
  exit;
  
  function PrintOut($lin) {
    global $output;
    $output .= $lin;
    print $lin;    
  }

?>
