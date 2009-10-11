<?php

//
// If you want to add information to your INFO tab, use this file as a template
//
// try fbcmd LOADINFO sample-loadinfo-text.php
//
// After you use LOADINFO for the first time, you have to authorize FBCMD to display your info. see:
// http://fbcmd.dtompkins.com/commands/loadinfo
// for more info
//

$fbCmdInfo = array (
  'title' => 'FBCMD Sample Header (Text Mode)',
  'type' => '1',
  'info_fields' =>
  array (
    0 =>
    array (
      'field' => 'Topic One',
      'items' =>
      array (
        0 =>
        array (
          'label' => 'Item One',
          'link' => 'http://www.link.one',
        ),
        1 =>
        array (
          'label' => 'Item Two',
          'link' => 'http://www.link.two',
        ),
      ),
    ),
    1 =>
    array (
      'field' => 'Second Topic',
      'items' =>
      array (
        0 =>
        array (
          'label' => 'Item Three',
          'link' => 'http://www.link.three',
        ),
      ),
    ),
  ),
);
?>
