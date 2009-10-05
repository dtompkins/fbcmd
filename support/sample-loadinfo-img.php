<?php

// see the sample-loadinfo-text.php for more information
//
// note that the 'type' => 5 specifies an 'Image' list

$fbCmdInfo = array (
  'title' => 'FBCMD Sample Header (Image Mode)',
  'type' => '5',
  'info_fields' =>
  array (
    0 =>
    array (
      'field' => 'Favorite Websites',
      'items' =>
      array (
        0 =>
        array (
          'label' => 'Google',
          'description' => 'Google tries to do no evil.',
          'link' => 'http://www.google.com',
          'image' => 'http://www.google.com/intl/en_ALL/images/logo.gif',
        ),
        1 =>
        array (
          'label' => 'IMDb',
          'description' => 'Endless Procrastination',
          'link' => 'http://www.imdb.com',
          'image' => 'http://i.media-imdb.com/images/nb15/logo2.gif',
        ),
      ),
    ),
  ),
);
?>
