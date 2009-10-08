#!/usr/bin/php
<?php

  $phpFile = @file_get_contents("../../fbcmd_update.php");
  preg_match("/fbcmdUpdateVersion\s=\s'([^']+)'/",$phpFile,$matches);
  $ver = preg_replace('/\./','-',$matches[1]);
  $exec = "rename fbcmd.msi Install-fbcmd-{$ver}.msi";
  print "$exec\n";
  exec($exec);

?>
