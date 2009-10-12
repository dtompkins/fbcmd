<?php

  $phpFile = @file_get_contents("../../fbcmd.php");
  preg_match("/fbcmdVersion\s=\s'([^']+)'/",$phpFile,$matches);
  $ver = preg_replace('/\./','-',$matches[1]);
  $exec = "copy fbcmd.zip fbcmd-{$ver}.zip /y";
  print "$exec\n";
  exec($exec);

?>
