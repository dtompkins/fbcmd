<?php

  $phpFile = @file_get_contents("../../fbcmd_update.php");
  preg_match("/fbcmdUpdateVersion\s=\s'([^']+)'/",$phpFile,$matches);
  $ver = preg_replace('/\./','-',$matches[1]);
  $exec = "copy Install-fbcmd.msi Install-fbcmd-{$ver}.msi /y";
  print "$exec\n";
  exec($exec);

?>
