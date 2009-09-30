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
//  This program is free software: you can redistribute it and/or modify      //
//  it under the terms of the GNU General Public License as published by      //
//  the Free Software Foundation, either version 3 of the License, or         //
//  (at your option) any later version.                                       //
//                                                                            //
//  This program is distributed in the hope that it will be useful,           //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of            //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             //
//  GNU General Public License for more details.                              //
//                                                                            //
//  You should have received a copy of the GNU General Public License         //
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.     //
//                                                                            //
//  see facebook.php, JSON.php & JSON-LICENSE for additional information      //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Having said all that, the author would love you to send him:             //
//   Suggestions,  Modifications and Improvements for re-distribution.        //
//                                                                            //
//   email: mail+fbcmd [@at@] dtompkins.com                                   //
//                                                                            //
//   See http://fbcmd.dtompkins.com/history/ for a revision history.          //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Disclaimer: This is my first (and currently only) PHP applicaiton,       //
//               so my apologies if I don't follow PHP best practices.        //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

  $fbcmdVersion = '1.00-beta2';

////////////////////////////////////////////////////////////////////////////////

  // you can include fbcmd.php from another program
  // see sample-mycmd.php for more information

  if (isset($fbcmd_include)) {
    ob_end_clean(); // this avoids displaying the #!/usr/bin/php when included
    if ($fbcmd_include_supressOutput) {
      ob_start();
    }
  } else {
    $fbcmd_argv = $argv;
    $fbcmd_argc = $argc;
  }

////////////////////////////////////////////////////////////////////////////////

  // set the default arguments to be empty

  $fbcmdCommand = '';
  $fbcmdParams = Array();
  $fbcmdPrefs = Array();

////////////////////////////////////////////////////////////////////////////////

  // You can set an environment variable FBCMD to specify the location of
  // your sessionkeys.txt file, prefs.php, posts.txt file...
  // it defaults to c:\fbcmd\ (windows) or ~/fbcmd/ (linux/other)

  if ($fbcmdBaseDir = getenv('FBCMD')) {
    $fbcmdBaseDir = CleanPath($fbcmdBaseDir);
  } else {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $fbcmdBaseDir = 'c:/fbcmd/';
    } else {
      $fbcmdBaseDir = CleanPath(getenv('HOME')) . 'fbcmd/';
    }
  }
  set_include_path (get_include_path() . PATH_SEPARATOR . $fbcmdBaseDir);

////////////////////////////////////////////////////////////////////////////////

  // This section sets your preferences

  // see http://fbcmd.dtompkins.com/preferences for more information

  // STEP ONE: System Defaults

  // Do NOT change these System Default preference values here:
  // Modify the prefs.php file instead

  // File locations
  $fbcmdPrefs['keyfile'] = "{$fbcmdBaseDir}sessionkeys.txt";
  $fbcmdPrefs['prefs'] = ''; // (none)
  $fbcmdPrefs['postfile'] = "{$fbcmdBaseDir}postdata.txt";
  $fbcmdPrefs['stream_save'] = '1';

  // FBCMD Application Specific
  $fbcmdPrefs['appkey'] = 'd96ea311638cf65f04b33c87eacf371e';
  $fbcmdPrefs['appsecret'] = '88af69b7ab8d437bff783328781be79b';
  $fbcmdPrefs['feed_template'] = '60736970450';

  // Prefix Codes is FLISTs:
  $fbcmdPrefs['prefix_friendlist'] = '_';
  $fbcmdPrefs['prefix_group'] = '~';
  $fbcmdPrefs['prefix_page'] = '+';
  $fbcmdPrefs['prefix_username'] = '@';
  $fbcmdPrefs['prefix_filter'] = '#';

  // Output Formatting
  $fbcmdPrefs['quiet'] = '0';
  $fbcmdPrefs['print_blanks'] = '0';
  $fbcmdPrefs['print_header'] = '1';
  $fbcmdPrefs['hide_dup_rows'] = '0';
  $fbcmdPrefs['show_id'] = '0';
  $fbcmdPrefs['trace'] = '0';
  $fbcmdPrefs['facebook_debug'] = false;

  // CSV Format
  $fbcmdPrefs['print_csv'] = '0';
  $fbcmdPrefs['csv_separator'] = ',';
  $fbcmdPrefs['csv_bookend'] = '"';
  $fbcmdPrefs['csv_escaped_bookend'] = '""';
  $fbcmdPrefs['csv_force_bookends'] = '0';

  // More Output Formatting
  $fbcmdPrefs['event_dateformat'] = 'D M d H:i';
  $fbcmdPrefs['status_show_date'] = '0';
  $fbcmdPrefs['status_dateformat'] = 'D M d H:i';

  $fbcmdPrefs['stream_show_date'] = '0';
  $fbcmdPrefs['stream_dateformat'] = 'D H:i';
  $fbcmdPrefs['stream_show_postid'] = '0';
  $fbcmdPrefs['stream_show_appdata'] = '0';
  $fbcmdPrefs['stream_show_attachments'] = '0';
  $fbcmdPrefs['stream_show_likes'] = '1';
  $fbcmdPrefs['stream_show_comments'] = '1';

  // PIC Preferences
  $fbcmdPrefs['pic_show_date'] = '0';
  $fbcmdPrefs['pic_dateformat'] = 'M d Y';
  $fbcmdPrefs['pic_show_links'] = '0';
  $fbcmdPrefs['pic_show_src'] = '0';

  $fbcmdPrefs['apics_filename'] = '[pid].jpg';
  $fbcmdPrefs['ppics_filename'] = '[tid].jpg';
  $fbcmdPrefs['fpics_filename'] = '[pid].jpg';
  $fbcmdPrefs['opics_filename'] = '[pid].jpg';

  $fbcmdPrefs['pic_skip_exists'] = '1';
  $fbcmdPrefs['pic_mkdir'] = '1';
  $fbcmdPrefs['pic_mkdir_mode'] = 0777;
  $fbcmdPrefs['pic_retry_count'] = '10';
  $fbcmdPrefs['pic_retry_delay'] = '2';

  //Misc. Behaviour
  $fbcmdPrefs['delpost_comment_fail'] = '1';
  $fbcmdPrefs['events_attend_mask'] = '15';
  $fbcmdPrefs['fevents_attend_mask'] = '1';
  $fbcmdPrefs['fgroups_show_id'] = '1';
  $fbcmdPrefs['flist_chunksize'] = 10;
  $fbcmdPrefs['online_idle'] = '1';
  $fbcmdPrefs['pic_size'] = '1';
  $fbcmdPrefs['ppic_size'] = '1';
  $fbcmdPrefs['restatus_comment_new'] = '1';
  $fbcmdPrefs['savepref_include_files'] = '0';
  $fbcmdPrefs['stream_new_from'] = 'created_time';

  // Parameter Defaults
  $fbcmdPrefs['default_addalbum_title'] = '';
  $fbcmdPrefs['default_addalbum_description'] = '';
  $fbcmdPrefs['default_addalbum_location'] = '';
  $fbcmdPrefs['default_addalbum_privacy'] = 'everyone';
  $fbcmdPrefs['default_addpic_filename'] = '';
  $fbcmdPrefs['default_addpic_albumid'] = null;
  $fbcmdPrefs['default_addpic_caption'] = '';
  $fbcmdPrefs['default_addpicd_dirname'] = '';
  $fbcmdPrefs['default_addpicd_albumid'] = null;
  $fbcmdPrefs['default_albums_flist'] = '=ME';
  $fbcmdPrefs['default_allinfo_flist'] = '=ME';
  $fbcmdPrefs['default_apics_albumid'] = '';
  $fbcmdPrefs['default_apics_savedir'] = false;
  $fbcmdPrefs['default_comment_text'] = '';
  $fbcmdPrefs['default_display_text'] = 'FBCMD: The Command Line Interface for Facebook';
  $fbcmdPrefs['default_feed1_text'] = '';
  $fbcmdPrefs['default_feed2_title'] = '';
  $fbcmdPrefs['default_feed2_body'] = '';
  $fbcmdPrefs['default_feed2_imgsrc'] = '';
  $fbcmdPrefs['default_feed2_imglink'] = '';
  $fbcmdPrefs['default_feedlink_link'] = '';
  $fbcmdPrefs['default_feedlink_text'] = '';
  $fbcmdPrefs['default_feednote_title'] = '';
  $fbcmdPrefs['default_feednote_body'] = '';
  $fbcmdPrefs['default_fevents_flist'] = '=ME';
  $fbcmdPrefs['default_fgroups_flist'] = '=ME';
  $fbcmdPrefs['default_finfo_fields'] = 'birthday_date';
  $fbcmdPrefs['default_finfo_flist'] = '=ALL';
  $fbcmdPrefs['default_flast_flist'] = '=ME';
  $fbcmdPrefs['default_flast_count'] = '10';
  $fbcmdPrefs['default_fonline_flist'] = '=ALL';
  $fbcmdPrefs['default_fpics_flist'] = '=ME';
  $fbcmdPrefs['default_fpics_savedir'] = false;
  $fbcmdPrefs['default_friends_flist'] = '=ALL';
  $fbcmdPrefs['default_fstatus_flist'] = '=ALL';
  $fbcmdPrefs['default_fstream_flist'] = '=ALL';
  $fbcmdPrefs['default_fstream_count'] = '10';
  $fbcmdPrefs['default_loaddisp_filename'] = '';
  $fbcmdPrefs['default_loadinfo_filename'] = '';
  $fbcmdPrefs['default_loadnote_title'] = '';
  $fbcmdPrefs['default_loadnote_filename'] = '';
  $fbcmdPrefs['default_mutual_flist'] = '=ALL';
  $fbcmdPrefs['default_nsend_flist'] = '=ME';
  $fbcmdPrefs['default_nsend_message'] = '';
  $fbcmdPrefs['default_opics_flist'] = '=ME';
  $fbcmdPrefs['default_opics_savedir'] = false;
  $fbcmdPrefs['default_post_message'] = '';
  $fbcmdPrefs['default_post_name'] = null;
  $fbcmdPrefs['default_post_link'] = null;
  $fbcmdPrefs['default_post_caption'] = null;
  $fbcmdPrefs['default_post_description'] = null;
  $fbcmdPrefs['default_postimg_message'] = false;
  $fbcmdPrefs['default_postimg_imgsrc'] = '';
  $fbcmdPrefs['default_postimg_imglink'] = '0';
  $fbcmdPrefs['default_postimg_name'] = null;
  $fbcmdPrefs['default_postimg_link'] = null;
  $fbcmdPrefs['default_postimg_caption'] = null;
  $fbcmdPrefs['default_postimg_description'] = null;
  $fbcmdPrefs['default_postmp3_message'] = false;
  $fbcmdPrefs['default_postmp3_mp3src'] = '';
  $fbcmdPrefs['default_postmp3_mp3title'] = '';
  $fbcmdPrefs['default_postmp3_mp3artist'] = '';
  $fbcmdPrefs['default_postmp3_mp3album'] = '';
  $fbcmdPrefs['default_postmp3_name'] = null;
  $fbcmdPrefs['default_postmp3_link'] = null;
  $fbcmdPrefs['default_postmp3_caption'] = null;
  $fbcmdPrefs['default_postmp3_description'] = null;
  $fbcmdPrefs['default_postvid_message'] = false;
  $fbcmdPrefs['default_postvid_vidsrc'] = '';
  $fbcmdPrefs['default_postvid_preview'] = '';
  $fbcmdPrefs['default_postvid_name'] = false;
  $fbcmdPrefs['default_postvid_link'] = false;
  $fbcmdPrefs['default_postvid_caption'] = false;
  $fbcmdPrefs['default_postvid_description'] = false;
  $fbcmdPrefs['default_ppics_flist'] = '=ALL';
  $fbcmdPrefs['default_ppics_savedir'] = false;
  $fbcmdPrefs['default_recent_flist'] = '=ALL';
  $fbcmdPrefs['default_recent_count'] = '10';
  $fbcmdPrefs['default_savedisp_filename'] = '';
  $fbcmdPrefs['default_saveinfo_filename'] = '';
  $fbcmdPrefs['default_stream_filter'] = '1';
  $fbcmdPrefs['default_stream_count'] = '10';
  $fbcmdPrefs['default_tagpic_pid'] = '';
  $fbcmdPrefs['default_tagpic_target'] = '=ME';
  $fbcmdPrefs['default_tagpic_x'] = '50';
  $fbcmdPrefs['default_tagpic_y'] = '50';
  $fbcmdPrefs['default_wallpost_flist'] = '=ME';
  $fbcmdPrefs['default_wallpost_message'] = '';

  $fbcmdPrefAliases = array(
    'af' => 'apics_filename',
    'bl' => 'print_blanks',
    'ch' => 'flist_chunksize',
    'csv' => 'print_csv',
    'csvf' => 'csv_force_bookends',
    'debug' => 'facebook_debug',
    'dup' => 'hide_dup_rows',
    'edf' => 'event_dateformat',
    'emask' => 'events_attend_mask',
    'ff' => 'fpics_filename',
    'fmask' => 'fevents_attend_mask',
    'gid' => 'fgroups_show_id',
    'hdr' => 'print_header',
    'id' => 'show_id',
    'idle' => 'online_idle',
    'key' => 'keyfile',
    'of' => 'opics_filename',
    'pd' => 'pic_show_date',
    'pdf' => 'pic_dateformat',
    'pf' => 'ppics_filename',
    'plink' => 'pic_show_links',
    'posts' => 'postfile',
    'ppsize' => 'ppic_size',
    'pr' => 'pic_retry_count',
    'prd' => 'pic_retry_delay',
    'psize' => 'pic_size',
    'pskip' => 'pic_skip_exists',
    'psrc' => 'pic_show_src',
    'q' => 'quiet',
    'sapp' => 'stream_show_appdata',
    'satt' => 'stream_show_attachments',
    'scom' => 'stream_show_comments',
    'sd' => 'stream_show_date',
    'sdf' => 'stream_dateformat',
    'sid' => 'stream_show_postid',
    'slikes' => 'stream_show_likes',
    'spf' => 'savepref_include_files',
    'ssave' => 'stream_save',
    'std' => 'status_show_date',
    'stdf' => 'status_dateformat',
    't' => 'trace'
  );

  // STEP TWO: Load preferences from prefs.php in the base directory

  if (file_exists("{$fbcmdBaseDir}prefs.php")) {
    include("{$fbcmdBaseDir}prefs.php");
  }

  // STEP THREE: Read switches set from the command line

  ParseArguments($fbcmd_argv,$fbcmd_argc);

  // STEP FOUR: if a "-prefs=filename.php" was specified

  if ($fbcmdPrefs['prefs']) {
    if (file_exists($fbcmdPrefs['prefs'])) {
      include($fbcmdPrefs['prefs']);
    } else {
      FbcmdWarning("Could not load Preferences file {$fbcmdPrefs['prefs']}");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  $GLOBALS['facebook_config']['debug'] = $fbcmdPrefs['facebook_debug'];

////////////////////////////////////////////////////////////////////////////////

  $fbcmdCommandList = array(
  'ADDALBUM',  'ADDPIC',    'ADDPICD',   'ALBUMS',
  'ALLINFO',   'APICS',     'AUTH',      'COMMENT',
  'DELPOST',   'DISPLAY',   'EVENTS',    'FEED1',
  'FEED2',     'FEEDLINK',  'FEEDNOTE',  'FEVENTS',
  'FGROUPS',   'FINFO',     'FLAST',     'FONLINE',
  'FPICS',     'FQL',       'FRIENDS',   'FSTATUS',
  'FSTREAM',   'FULLPOST',  'HELP',      'LIKE',
  'LIMITS',    'LOADDISP',  'LOADINFO',  'LOADNOTE',
  'MUTUAL',    'NOTIFY',    'NSEND',     'OPICS',
  'POST',      'POSTIMG',   'POSTMP3',   'POSTVID',
  'PPICS',     'RECENT',    'RESET',     'RESTATUS',
  'SAVEDISP',  'SAVEINFO',  'SAVEPREF',  'SFILTERS',
  'STATUS',    'STREAM',    'TAGPIC',    'UFIELDS',
  'USAGE',     'VERSION',   'WALLPOST',  'WHOAMI'
  );

  if (isset($fbcmd_include_newCommands)) {
    array_merge_unique($fbcmdCommandList,$fbcmd_include_newCommands);
  }

////////////////////////////////////////////////////////////////////////////////

  if (($fbcmdCommand == '')||($fbcmdCommand == 'HELP')||($fbcmdCommand == 'USAGE')) {
    ShowUsage();
  }

////////////////////////////////////////////////////////////////////////////////

  if (in_array($fbcmdCommand,array('DFILE','FEED','FEED3','FSTATUSID','FLSTATUS','PICS'))) {
    FbcmdFatalError("{$fbcmdCommand} has been deprecated:\n  visit http://fbcmd.dtompkins.com/commands/" . strtolower($fbcmdCommand) . " for more information");
  }

////////////////////////////////////////////////////////////////////////////////

  if (!in_array($fbcmdCommand,$fbcmdCommandList)) {
    FbcmdFatalError("Unknown Command: [{$fbcmdCommand}] try fbcmd HELP");
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'SAVEPREF') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,"{$fbcmdBaseDir}prefs.php");
    SavePrefs($fbcmdParams[1]);
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  // include the Facebook API code

  try {
    if(!@include_once('facebookapi_php5_restlib.php')) throw new Exception('');
    if(!@include_once('facebook.php')) throw new Exception('');
    if(!@include_once('facebook_desktop.php')) throw new Exception('');
  } catch (Exception $e) {
    FbcmdFatalError('Missing Facebook API files: can\'t find facebook*.php in ' . get_include_path());
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'RESET') {
    ValidateParamCount(0);
    if (@file_put_contents($fbcmdPrefs['keyfile'],"EMPTY\nEMPTY\n# only the first two lines of this file are read\n# use fbcmd RESET to replace this file\n")==false) {
      FbcmdFatalError("Could not generate keyfile {$fbcmdPrefs['keyfile']}");
    }
    if (!$fbcmdPrefs['quiet']) {
      print "keyfile {$fbcmdPrefs['keyfile']} has been RESET\n";
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'AUTH') {
    ValidateParamCount(1);
    try {
      $fbObject = new FacebookDesktop($fbcmdPrefs['appkey'], $fbcmdPrefs['appsecret']);
      $session = $fbObject->do_get_session($fbcmdParams[1]);
      TraceReturn($session);
    } catch (Exception $e) {
      FbcmdException($e,'Invalid AUTH code / could not authorize session');
    }
    $fbcmdUserSessionKey = $session['session_key'];
    $fbcmdUserSecretKey = $session['secret'];
    if (@file_put_contents ($fbcmdPrefs['keyfile'],"{$fbcmdUserSessionKey}\n{$fbcmdUserSecretKey}\n# only the first two lines of this file are read\n# use fbcmd RESET to replace this file\n")==false) {
      FbcmdFatalError("Could not generate keyfile {$fbcmdPrefs['keyfile']}");
    }
    try {
      $fbObject->api_client->session_key = $fbcmdUserSessionKey;
      $fbObject->secret = $fbcmdUserSecretKey;
      $fbObject->api_client->secret = $fbcmdUserSecretKey;
      $fbUser = $fbObject->api_client->users_getLoggedInUser();
      $fbReturn = $fbObject->api_client->users_getInfo($fbUser,array('name'));
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e,'Invalid AUTH code / could not generate session key');
    }
    if (!$fbcmdPrefs['quiet']) {
      print "\nfbcmd [v$fbcmdVersion] AUTH Code accepted.\nWelcome to FBCMD {$fbReturn[0]['name']}.  Try fbcmd HELP to begin\n";
    }
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  // attempt to read in the keyfile

  if (!file_exists($fbcmdPrefs['keyfile'])) {
    FbcmdFatalError("Could not locate keyfile {$fbcmdPrefs['keyfile']}");
  }
  $fbcmdKeyFile = file($fbcmdPrefs['keyfile'],FILE_IGNORE_NEW_LINES);
  if (count($fbcmdKeyFile) < 2) {
    FbcmdFatalError("Invalid keyfile {$fbcmdPrefs['keyfile']}");
  }
  $fbcmdUserSessionKey = $fbcmdKeyFile[0];
  $fbcmdUserSecretKey = $fbcmdKeyFile[1];

  if (strncmp($fbcmdUserSessionKey,'EMPTY',5)==0) {
    if (!$fbcmdPrefs['quiet']) {
      print "\n";
      print "fbcmd [v{$fbcmdVersion}] Facebook Command Line Interface\n\n";
      print "Welcome to FBCMD.  To use this applicaiton, you need to obtain a\n";
      print "facebook authorization code which can be obtained here:\n\n";
      print "http://www.facebook.com/code_gen.php?v=1.0&api_key={$fbcmdPrefs['appkey']}\n\n";
      print "obtain your 6-digit code, and then execute fbcmd AUTH XXXXXX\n\n";
    }
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  // create the Facebook Object

  try {
    $fbObject = new FacebookDesktop($fbcmdPrefs['appkey'], $fbcmdPrefs['appsecret']);
    $fbObject->api_client->session_key = $fbcmdUserSessionKey;
    $fbObject->secret = $fbcmdUserSecretKey;
    $fbObject->api_client->secret = $fbcmdUserSecretKey;
    $fbUser = $fbObject->api_client->users_getLoggedInUser();
  } catch (Exception $e) {
    FbcmdException($e,'Could not use session key / log in user');
  }

////////////////////////////////////////////////////////////////////////////////

  // GLOBAL FQL strings for FLISTS

  $fqlFriendId = "SELECT uid2 FROM friend WHERE uid1={$fbUser} AND uid2=uid2";
  $fqlFriendBaseInfo = "SELECT uid,first_name,last_name,name,username,birthday_date,online_presence,status FROM user WHERE uid IN (SELECT uid2 FROM #fqlFriendId) OR uid={$fbUser}";
  $keyFriendBaseInfo = 'uid';
  $fqlFriendListNames = "SELECT flid,name FROM friendlist WHERE owner={$fbUser}";
  $keyFriendListNames = 'flid';
  $fqlFriendListMembers = "SELECT flid,uid FROM friendlist_member WHERE flid IN (SELECT flid FROM #fqlFriendListNames)";
  $fqlPageId = "SELECT page_id FROM page_fan WHERE uid={$fbUser}";
  $fqlPageNames = "SELECT page_id,name,username FROM page WHERE page_id IN (SELECT page_id FROM #fqlPageId)";
  $keyPageNames = 'page_id';
  $fqlGroupNames = "SELECT gid,name FROM group WHERE gid IN (SELECT gid FROM group_member WHERE uid={$fbUser})";
  $keyGroupNames = 'gid';

  $flistMatchArray = Array();
  $flistMatchIdString = '';

////////////////////////////////////////////////////////////////////////////////

  PrintStart();

////////////////////////////////////////////////////////////////////////////////

  if (isset($fbcmd_include_newCommands)) {
    if (in_array($fbcmdCommand,$fbcmd_include_newCommands)) {
      return;
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ADDALBUM') {
    ValidateParamCount(1,4);
    SetDefaultParam(1,$fbcmdPrefs['default_addalbum_title']);
    SetDefaultParam(2,$fbcmdPrefs['default_addalbum_description']);
    SetDefaultParam(3,$fbcmdPrefs['default_addalbum_location']);
    SetDefaultParam(4,$fbcmdPrefs['default_addalbum_privacy']);
    if (!in_array($fbcmdParams[4],array('friends','friends-of-friends','networks','everyone'))) {
      FbcmdFatalError("ADDALBUM 4th parameter must be one of:\n                     friends,friends-of-friends,networks,everyone");
    }
    try {
      $fbReturn = $fbObject->api_client->photos_createAlbum($fbcmdParams[1],$fbcmdParams[2],$fbcmdParams[3],$fbcmdParams[3]);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
    PrintHeaderQuiet('AID',PrintIfPref('pic_show_links','LINK'));
    PrintRowQuiet($fbReturn['aid'],PrintIfPref('pic_show_links',$fbReturn['link']));
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ADDPIC') {
    ValidateParamCount(1,3);
    SetDefaultParam(1,$fbcmdPrefs['default_addpic_filename']);
    SetDefaultParam(2,$fbcmdPrefs['default_addpic_albumid']);
    SetDefaultParam(3,$fbcmdPrefs['default_addpic_caption']);
    if (!file_exists($fbcmdParams[1])) {
      FbcmdFatalError("Could not find file {$fbcmdParams[1]}");
    }
    if ((strtoupper($fbcmdParams[2])=='1')||(strtoupper($fbcmdParams[2])=='LATEST')) {
      $fbcmdParams[2] = GetLatestAID();
    }
    try {
      $fbReturn = $fbObject->api_client->photos_upload($fbcmdParams[1], $fbcmdParams[2], $fbcmdParams[3], $fbUser);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
    PrintHeaderQuiet('PID',PrintIfPref('pic_show_links','LINK'),PrintIfPref('pic_show_src','SRC'));
    PrintRowQuiet($fbReturn['pid'],PrintIfPref('pic_show_links',$fbReturn['link']),PrintIfPref('pic_show_src',PhotoSrc($fbReturn)));
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ADDPICD') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_addpicd_dirname']);
    SetDefaultParam(2,$fbcmdPrefs['default_addpicd_albumid']);
    $fileList = FileMatches($fbcmdParams[1],'jpg');
    if (($fbcmdParams[2]=='1')||(strtoupper($fbcmdParams[2])=='LATEST')) {
      $fbcmdParams[2] = GetLatestAID();
    }
    if (count($fileList) > 0) {
      PrintHeaderQuiet('PID',PrintIfPref('pic_show_links','LINK'),PrintIfPref('pic_show_src','SRC'));
      foreach ($fileList as $fileName) {
        try {
          $fbReturn = $fbObject->api_client->photos_upload($fileName, $fbcmdParams[2], '', $fbUser);
          TraceReturn($fbReturn);
        } catch (Exception $e) {
          FbcmdException($e);
        }
        PrintRowQuiet($fbReturn['pid'],PrintIfPref('pic_show_links',$fbReturn['link']),PrintIfPref('pic_show_src',PhotoSrc($fbReturn)));
      }
    } else {
      FbcmdFatalError("Could Not Find files in {$fbcmdParams[1]}");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ALBUMS') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_albums_flist']);
    GetFlistIds($fbcmdParams[1],true);
    $fql = "SELECT aid,owner,name,size,created,link FROM album WHERE owner IN ({$flistMatchIdString})";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader(PrintIfPref('show_id','OWNER_ID'),'OWNER_NAME','AID',PrintIfPref('pic_show_date','CREATED'),'NAME','SIZE',PrintIfPref('pic_show_links','LINK'));
      foreach ($fbReturn as $a) {
        PrintRow(PrintIfPref('show_id',$a['owner']),ProfileName($a['owner']),$a['aid'],PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$a['created'])),$a['name'],$a['size'],PrintIfPref('pic_show_links',$a['link']));
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  $allUserInfoFields = "about_me,activities,affiliations,allowed_restrictions,birthday,birthday_date,books,current_location,education_history,email_hashes,family,first_name,has_added_app,hometown_location,hs_info,interests,is_app_user,last_name,locale,meeting_for,meeting_sex,movies,music,name,notes_count,online_presence,pic,pic_big,pic_big_with_logo,pic_small,pic_small_with_logo,pic_square,pic_square_with_logo,pic_with_logo,political,profile_blurb,profile_update_time,profile_url,proxied_email,quotes,relationship_status,religion,sex,significant_other_id,status,timezone,tv,uid,username,wall_count,work_history";

  if ($fbcmdCommand == 'ALLINFO') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_allinfo_flist']);
    GetFlistIds($fbcmdParams[1]);
    $fql = "SELECT {$allUserInfoFields} from user where uid in ({$flistMatchIdString}) ORDER BY last_name";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader(PrintIfPref('show_id','UID'),'NAME','FIELD','VALUE');
      foreach ($fbReturn as $a) {
        PrintRecursiveObject(array(PrintIfPref('show_id',$a['uid']),ProfileName($a['uid'])),'',$a);
      }
    }
  }

  ////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'APICS') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_apics_albumid']);
    SetDefaultParam(2,$fbcmdPrefs['default_apics_savedir']);
    if ((strtoupper($fbcmdParams[1])=='1')||(strtoupper($fbcmdParams[1])=='LATEST')) {
      $fbcmdParams[1] = GetLatestAID();
    }
    $fql = "SELECT pid,aid,owner,src_small,src_big,src,link,caption,created FROM photo WHERE aid IN ({$fbcmdParams[1]})";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader('AID','PID',PrintIfPref('pic_show_date','CREATED'),'CAPTION',PrintIfPref('pic_show_links','LINK'),PrintIfPref('pic_show_src','SRC'));
      foreach ($fbReturn as $pic) {
        PrintRow($pic['aid'],$pic['pid'],PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$pic['created'])),$pic['caption'],PrintIfPref('pic_show_links',$pic['link']),PrintIfPref('pic_show_src',PhotoSrc($pic)));
        if ($fbcmdParams[2]) {
          SavePhoto(PhotoSrc($pic),$pic,'0',$fbcmdParams[2],$fbcmdPrefs['apics_filename']);
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'COMMENT') {
    ValidateParamCount(2);
    SetDefaultParam(2,$fbcmdPrefs['default_comment_text']);
    $curPostId = GetPostId($fbcmdParams[1],true);
    if ($curPostId) {
      try {
        $fbReturn = $fbObject->api_client->stream_addComment($curPostId,$fbcmdParams[2]);
        TraceReturn($fbReturn);
      } catch (Exception $e) {
        FbcmdException($e);
      }
      PrintHeaderQuiet('POST_ID');
      PrintRowQuiet($curPostId);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'DELPOST') {
    ValidateParamCount(1);
    $curPostId = GetPostId($fbcmdParams[1],true);
    if ($fbcmdPrefs['delpost_comment_fail']) {
      $commentCount = GetCommentCount($curPostId);
      if ($commentCount > 0) {
        FbcmdFatalError("[DELPOST] {$curPostId} has comments: -delpost_comment_fail=1");
      }
    }
    if ($curPostId) {
      try {
        $fbReturn = $fbObject->api_client->stream_remove($curPostId);
        TraceReturn($fbReturn);
      } catch (Exception $e) {
        FbcmdException($e);
      }
      if ($fbReturn) {
        PrintHeaderQuiet('POST_ID');
        PrintRowQuiet($curPostId);
      } else {
        FbcmdFatalError("[DELPOST] Could Not Delete POST_ID {$curPostId}");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'DISPLAY') {
    ValidateParamCount(1);
    SetDefaultParam(1,$fbcmdPrefs['default_display_text']);
    try {
      $fbReturn = $fbObject->api_client->profile_setFBML($fbcmdParams[1],null,$fbcmdParams[1],'',$fbcmdParams[1],$fbcmdParams[1]);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'EVENTS') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,time());
    $eventAttend = ProcessEventMask($fbcmdPrefs['events_attend_mask']);
    $fql = "SELECT eid,name,start_time FROM event WHERE eid IN (SELECT eid FROM event_member WHERE uid={$fbUser} AND rsvp_status IN ({$eventAttend})) AND start_time > {$fbcmdParams[1]} ORDER BY start_time";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader('START_TIME','EVENT');
      foreach ($fbReturn as $event) {
        PrintRow(date($fbcmdPrefs['event_dateformat'],$event['start_time']),$event['name']);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FEED1') {
    ValidateParamCount(1);
    SetDefaultParam(1,$fbcmdPrefs['default_feed1_text']);
    try {
      $fbReturn = $fbObject->api_client->feed_publishUserAction($fbcmdPrefs['feed_template'],array('title-text' => $fbcmdParams[1], 'body-text' => ''),'','',FacebookRestClient::STORY_SIZE_ONE_LINE);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FEED2') {
    ValidateParamCount(array(2,4));
    SetDefaultParam(1,$fbcmdPrefs['default_feed2_title']);
    SetDefaultParam(2,$fbcmdPrefs['default_feed2_body']);
    if (ParamCount() == 2) {
      try {
        $fbReturn = $fbObject->api_client->feed_publishUserAction($fbcmdPrefs['feed_template'],array('title-text' => $fbcmdParams[1], 'body-text' => $fbcmdParams[2]),'','',FacebookRestClient::STORY_SIZE_SHORT);
        TraceReturn($fbReturn);
      } catch (Exception $e) {
        FbcmdException($e);
      }
    }
    if (ParamCount() == 4) {
      SetDefaultParam(3,$fbcmdPrefs['default_feed2_imgsrc']);
      SetDefaultParam(4,$fbcmdPrefs['default_feed2_imglink']);
      try {
        $fbReturn = $fbObject->api_client->feed_publishUserAction($fbcmdPrefs['feed_template'],array('title-text' => $fbcmdParams[1], 'body-text' => $fbcmdParams[2], 'images' => array( array('src' => $fbcmdParams[3], 'href' => $fbcmdParams[3]))),'','',FacebookRestClient::STORY_SIZE_SHORT);
        TraceReturn($fbReturn);
      } catch (Exception $e) {
        FbcmdException($e);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FEEDLINK') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_feedlink_link']);
    SetDefaultParam(2,$fbcmdPrefs['default_feedlink_text']);
    try {
      $fbReturn = $fbObject->api_client->links_post($fbcmdParams[1],$fbcmdParams[2]);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FEEDNOTE') {
    ValidateParamCount(2);
    SetDefaultParam(1,$fbcmdPrefs['default_feednote_title']);
    SetDefaultParam(2,$fbcmdPrefs['default_feednote_body']);
    try {
      $fbReturn = $fbObject->api_client->notes_create($fbcmdParams[1],$fbcmdParams[2]);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FEVENTS') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_fevents_flist']);
    SetDefaultParam(2,time());
    $eventAttend = ProcessEventMask($fbcmdPrefs['fevents_attend_mask']);
    GetFlistIds($fbcmdParams[1],true);
    PrintHeader(PrintIfPref('show_id','ID'),'NAME','START_TIME','EVENT');
    do {
      $curChunkIds = GetNextChunkIds();
      if ($curChunkIds) {
        $results = MultiFqlById($curChunkIds,"SELECT eid,name,start_time FROM event WHERE eid IN (SELECT eid FROM event_member WHERE uid=[id] AND rsvp_status IN ({$eventAttend})) AND start_time > {$fbcmdParams[2]} ORDER BY start_time");
        foreach ($curChunkIds as $id) {
          if ($results[$id]) {
            foreach ($results[$id] as $event) {
              PrintRow(PrintIfPref('show_id',$id),ProfileName($id),date($fbcmdPrefs['event_dateformat'],$event['start_time']),$event['name']);
            }
          }
        }
      }
    } while ($curChunkIds);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FGROUPS') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_fgroups_flist']);
    GetFlistIds($fbcmdParams[1],true);
    PrintHeader(PrintIfPref('show_id','ID'),'NAME',PrintIfPref('fgroups_show_id','GROUP_ID'),'GROUP');
    do {
      $curChunkIds = GetNextChunkIds();
      if ($curChunkIds) {
        $results = MultiFqlById($curChunkIds,"SELECT gid,name FROM group WHERE gid IN (SELECT gid FROM group_member WHERE uid=[id])");
        foreach ($curChunkIds as $id) {
          if ($results[$id]) {
            foreach ($results[$id] as $group) {
              PrintRow(PrintIfPref('show_id',$id),ProfileName($id),PrintIfPref('fgroups_show_id',$group['gid']),$group['name']);
            }
          }
        }
      }
    } while ($curChunkIds);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FINFO') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_finfo_fields']);
    SetDefaultParam(2,$fbcmdPrefs['default_finfo_flist']);
    GetFlistIds($fbcmdParams[2]);
    $fql = "SELECT uid,{$fbcmdParams[1]} from user where uid in ({$flistMatchIdString}) ORDER BY last_name";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      $fields = explode(',',$fbcmdParams[1]);
      if (in_array('uid',$fields)) {
        FbcmdWarning("FINFO: use -showid instead of specifying the uid field");
      }
      $headerFields = array();
      foreach ($fields as $f) {
        $headerFields[] = strtoupper($f);
      }
      PrintHeader(PrintIfPref('show_id','UID'),'NAME',$headerFields);
      foreach ($fbReturn as $user) {
        $outputFields = array();
        $isEmptyRow = true;
        foreach ($user as $key=>$value) {
          if ($key != 'uid') {
            $outputFields[] = DisplayField($value);
            if (!IsEmpty($value)) {
              $isEmptyRow = false;
            }
          }
        }
        if ((!$isEmptyRow)||($fbcmdPrefs['print_blanks'])) {
          PrintRow(PrintIfPref('show_id',$user['uid']),ProfileName($user['uid']),$outputFields);
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FLAST') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_flast_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_flast_count']);
    GetFlistIds($fbcmdParams[1],true);
    PrintHeader(PrintIfPref('show_id','ID'),'NAME',PrintIfPref('status_show_date','TIME'),'STATUS');
    do {
      $curChunkIds = GetNextChunkIds();
      if ($curChunkIds) {
        $results = MultiFqlById($curChunkIds,"SELECT uid,message,time FROM status WHERE uid=[id] ORDER BY time DESC LIMIT {$fbcmdParams[2]}");
        foreach ($curChunkIds as $id) {
          if ($results[$id]) {
            foreach ($results[$id] as $status) {
              PrintRow(PrintIfPref('show_id',$id),ProfileName($id),PrintIfPref('status_show_date',date($fbcmdPrefs['status_dateformat'],$status['time'])),$status['message']);
            }
          }
        }
      }
    } while ($curChunkIds);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FONLINE') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_fonline_flist']);
    GetFlistIds($fbcmdParams[1]);
    PrintHeader(PrintIfPref('show_id','ID'),'NAME','ONLINE_PRESENCE');
    foreach ($flistMatchArray as $id) {
      if (($indexFriendBaseInfo[$id]['online_presence'] == 'active')||(($indexFriendBaseInfo[$id]['online_presence']=='idle')&&($fbcmdPrefs['online_idle']))) {
        PrintRow(PrintIfPref('show_id',$id),ProfileName($id),$indexFriendBaseInfo[$id]['online_presence']);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FPICS') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_fpics_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_fpics_savedir']);
    GetFlistIds($fbcmdParams[1],true);
    PrintHeader(PrintIfPref('show_id','ID'),'NAME','PID',PrintIfPref('pic_show_date','CREATED'),PrintIfPref('pic_show_links','LINK'),PrintIfPref('pic_show_src','SRC'));
    do {
      $curChunkIds = GetNextChunkIds();
      if ($curChunkIds) {
        $results = MultiFqlById($curChunkIds,"SELECT aid,pid,owner,src_small,src_big,src,link,created FROM photo WHERE pid IN (SELECT pid FROM photo_tag WHERE subject=[id])");
        foreach ($curChunkIds as $id) {
          if ($results[$id]) {
            foreach ($results[$id] as $pic) {
              PrintRow(PrintIfPref('show_id',$id),ProfileName($id),$pic['pid'],PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$pic['created'])),PrintIfPref('pic_show_links',$pic['link']),PrintIfPref('pic_show_src',PhotoSrc($pic)));
              if ($fbcmdParams[2]) {
                SavePhoto(PhotoSrc($pic),$pic,$id,$fbcmdParams[2],$fbcmdPrefs['fpics_filename']);
              }
            }
          }
        }
      }
    } while ($curChunkIds);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FQL') {
    ValidateParamCount(1,2);
    $fql = $fbcmdParams[1];
    $fql = str_replace('[me]', $fbUser, $fql);
    SetDefaultParam(2,'');
    if ($fbcmdParams[2]) {
      GetFlistIds($fbcmdParams[2]);
      $fql = str_replace('[flist]', "({$flistMatchIdString})", $fql);
    }
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader('INDEX','FIELD','VALUE');
      for ($i = 0; $i < count($fbReturn); $i++) {
        PrintRecursiveObject(array($i+1),'',$fbReturn[$i]);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FRIENDS') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_friends_flist']);
    GetFlistIds($fbcmdParams[1], true, true, true);
    PrintHeader(array('ID','NAME'));
    foreach ($flistMatchArray as $id) {
      PrintRow($id,ProfileName($id));
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FSTATUS') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_fstatus_flist']);
    GetFlistIds($fbcmdParams[1]);
    $header = array();
    PrintHeader(PrintIfPref('show_id','ID'),'NAME',PrintIfPref('status_show_date','TIME'),'STATUS');
    foreach ($flistMatchArray as $id) {
      $status = $indexFriendBaseInfo[$id]['status'];
      if ($status) {
        if ($status['message']) {
          PrintRow(PrintIfPref('show_id',$id),ProfileName($id),PrintIfPref('status_show_date',date($fbcmdPrefs['status_dateformat'],$status['time'])),$status['message']);
        } else {
          if ($fbcmdPrefs['print_blanks']) {
            PrintRow(PrintIfPref('show_id',$id),ProfileName($id),PrintIfPref('status_show_date',''),'[blank]');
          }
        }
      } else {
        if ($fbcmdPrefs['print_blanks']) {
          PrintRow(PrintIfPref('show_id',$id),ProfileName($id),PrintIfPref('status_show_date',''),'[n/a]');
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FSTREAM') {
    ValidateParamCount(0,2);
    SetDefaultParam(1,$fbcmdPrefs['default_fstream_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_fstream_count']);
    GetFlistIds($fbcmdParams[1],true);
    if (strtoupper($fbcmdParams[2])=='NEW') {
      CheckTimeStamp();
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes FROM stream WHERE source_id IN ({$flistMatchIdString}) AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    } else {
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes FROM stream WHERE source_id IN ({$flistMatchIdString}) LIMIT {$fbcmdParams[2]}";
    }
    $queries = array('Stream');
    if ($fbcmdPrefs['stream_show_comments']) {
      $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id, comments.comment_list.fromid FROM #fqlStream)';
      $keyStreamNames = 'id';
      $queries[] = 'StreamNames';
    }
    MultiFQL($queries);
    if (!empty($dataStream)) {
      SavePostData($dataStream);
      PrintPostHeader();
      $postNum = 0;
      foreach ($dataStream as $a) {
        PrintPostObject(++$postNum,$a);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FULLPOST') {
    ValidateParamCount(1);
    $curPostId = GetPostId($fbcmdParams[1],true);
    if ($curPostId) {
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes FROM stream WHERE post_id='{$curPostId}'";
      $fqlComments = "SELECT fromid, time, text FROM comment WHERE post_id='{$curPostId}' ORDER BY time";
      $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id FROM #fqlStream) OR id IN (SELECT fromid FROM #fqlComments)';
      $keyStreamNames = 'id';
      MultiFQL(array('Stream','Comments','StreamNames'));
      if (!empty($dataStream)) {
        PrintPostHeader();
        PrintPostObject($fbcmdParams[1],$dataStream[0],$dataComments);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LIKE') {
    ValidateParamCount(1);
    $likesList = explode(',',$fbcmdParams[1]);
    PrintHeaderQuiet('POST_ID');
    foreach ($likesList as $like) {
      $curPostId = GetPostId($like);
      if ($curPostId) {
        try {
          $fbReturn = $fbObject->api_client->stream_addLike($curPostId);
          TraceReturn($fbReturn);
        } catch (Exception $e) {
          FbcmdException($e);
        }
        PrintRowQuiet($curPostId);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LIMITS') {
    ValidateParamCount(0);
    try {
      $fbReturn = $fbObject->api_client->admin_getAllocation('notifications_per_day');
      TraceReturn($fbReturn);
      $limitNSEND = $fbReturn;
    } catch (Exception $e) {
      FbcmdException($e);
    }
    PrintHeader('COMMAND','LIMIT','DURATION');
    PrintRow('FEED','10','per day');
    PrintRow('NSEND',$limitNSEND,'per day');
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LOADDISP') {
    ValidateParamCount(1);
    SetDefaultParam(1,$fbcmdPrefs['default_loaddisp_filename']);
    if (!file_exists($fbcmdParams[1])) {
      FbcmdFatalError("Could not locate file {$fbcmdParams[1]}");
    }
    $fbFbmlFile = @file_get_contents($fbcmdParams[1]);
    if ($fbFbmlFile == false) {
      FbcmdFatalError("Could not read file {$fbcmdParams[1]}");
    }
    try {
      $fbReturn = $fbObject->api_client->profile_setFBML($fbFbmlFile,null,$fbFbmlFile,'',$fbFbmlFile,$fbFbmlFile);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

  ////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LOADINFO') {
    ValidateParamCount(1);
    SetDefaultParam(1,$fbcmdPrefs['default_loadinfo_filename']);
    if (!file_exists($fbcmdParams[1])) {
      FbcmdFatalError("Could not locate file {$fbcmdParams[1]}");
    }
    $fbCmdInfo = '';
    try {
      if(!@include_once($fbcmdParams[1])) throw new Exception('');
    } catch(Exception $e) {
      FbcmdFatalError("Could not read Info File {$fbcmdParams[1]}");
    }
    if ($fbCmdInfo == '') {
      FbcmdFatalError("\$fbCmdInfo was not set properly in {$fbcmdParams[1]}");
    }
    try {
      $fbReturn = $fbObject->api_client->profile_setInfo($fbCmdInfo['title'], $fbCmdInfo['type'], $fbCmdInfo['info_fields']);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LOADNOTE') {
    ValidateParamCount(2);
    SetDefaultParam(1,$fbcmdPrefs['default_loadnote_title']);
    SetDefaultParam(2,$fbcmdPrefs['default_loadnote_filename']);
    if (!file_exists($fbcmdParams[2])) {
      FbcmdFatalError("Could not locate file {$fbcmdParams[1]}");
    }
    $fbFbmlFile = @file_get_contents($fbcmdParams[2]);
    if ($fbFbmlFile == false) {
      FbcmdFatalError("Could not read file {$fbcmdParams[2]}");
    }
    try {
      $rbReturn = $fbObject->api_client->notes_create($fbcmdParams[1],$fbFbmlFile);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'MUTUAL') {
    ValidateParamCount(1);
    SetDefaultParam(1,$fbcmdPrefs['default_mutual_flist']);
    GetFlistIds($fbcmdParams[1]);
    PrintHeader(PrintIfPref('show_id','ID'),'NAME',PrintIfPref('show_id','FRIEND_ID'),'FRIEND_NAME');
    do {
      $curChunkIds = GetNextChunkIds();
      if ($curChunkIds) {
        $results = MultiFqlById($curChunkIds,"SELECT uid,name FROM user WHERE uid IN (SELECT uid1 FROM friend WHERE uid1 IN (SELECT uid2 FROM friend WHERE uid1={$fbUser}) AND uid2=[id])");
        foreach ($curChunkIds as $id) {
          if ($results[$id]) {
            foreach ($results[$id] as $user) {
              PrintRow(PrintIfPref('show_id',$id),ProfileName($id),PrintIfPref('show_id',$id),$user['name']);
            }
          }
        }
      }
    } while ($curChunkIds);
  }

  ////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'NOTIFY') {
    ValidateParamCount(0);
    try {
      $fbReturn = $fbObject->api_client->notifications_get();
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
    PrintHeader('FIELD','VALUE');
    PrintRow('MESSAGES_UNREAD',$fbReturn['messages']['unread']);
    PrintRow('POKES',$fbReturn['pokes']['unread']);
    PrintRow('SHARES_UNREAD',$fbReturn['shares']['unread']);
    $fqlNotifyFriends = 'SELECT uid,name FROM user WHERE uid in (' . array_implode_safe(',',$fbReturn['friend_requests']) . ')';
    $fqlNotifyGroups = 'SELECT gid,name FROM group WHERE gid in (' . array_implode_safe(',',$fbReturn['group_invites']) . ')';
    $fqlNotifyEvents = 'SELECT eid,name FROM event WHERE eid in (' . array_implode_safe(',',$fbReturn['event_invites']) . ')';
    MultiFQL(array('NotifyFriends','NotifyGroups','NotifyEvents'));
    NotifyHelper($fbReturn['friend_requests'],$dataNotifyFriends,'FRIEND','REQUESTS');
    NotifyHelper($fbReturn['group_invites'],$dataNotifyGroups,'GROUP','INVITES');
    NotifyHelper($fbReturn['event_invites'],$dataNotifyEvents,'EVENT','INVITES');
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'NSEND') {
    ValidateParamCount(2);
    SetDefaultParam(1,$fbcmdPrefs['default_nsend_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_nsend_message']);
    GetFlistIds($fbcmdParams[1],false,false,true);
    PrintHeaderQuiet('RECIPIENT_NAME');
    foreach ($flistMatchArray as $id) {
      try {
        $fbReturn = $fbObject->api_client->notifications_send($id, $fbcmdParams[2], 'user_to_user');
        TraceReturn($fbReturn);
      } catch (Exception $e) {
        FbcmdException($e);
      }
      PrintRowQuiet(ProfileName($id));
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'OPICS') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_opics_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_opics_savedir']);
    GetFlistIds($fbcmdParams[1],true);
    PrintHeader(PrintIfPref('show_id','ID'),'NAME','AID','PID',PrintIfPref('pic_show_date','CREATED'),'CAPTION',PrintIfPref('pic_show_links','LINK'),PrintIfPref('pic_show_src','SRC'));
    do {
      $curChunkIds = GetNextChunkIds();
      if ($curChunkIds) {
        $results = MultiFqlById($curChunkIds,"SELECT pid,aid,owner,src_small,src_big,src,link,caption,created FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner=[id])");
        foreach ($curChunkIds as $id) {
          if ($results[$id]) {
            foreach ($results[$id] as $pic) {
              PrintRow(PrintIfPref('show_id',$id),ProfileName($id),$pic['aid'],$pic['pid'],PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$pic['created'])),$pic['caption'],PrintIfPref('pic_show_links',$pic['link']),PrintIfPref('pic_show_src',PhotoSrc($pic)));
              if ($fbcmdParams[2]) {
                SavePhoto(PhotoSrc($pic),$pic,'',$fbcmdParams[2],$fbcmdPrefs['opics_filename']);
              }
            }
          }
        }
      }
    } while ($curChunkIds);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POST') {
    ValidateParamCount(1,5);
    SetDefaultParam(1,$fbcmdPrefs['default_post_message']);
    SetDefaultParam(2,$fbcmdPrefs['default_post_name']);
    SetDefaultParam(3,$fbcmdPrefs['default_post_link']);
    SetDefaultParam(4,$fbcmdPrefs['default_post_caption']);
    SetDefaultParam(5,$fbcmdPrefs['default_post_description']);
    try {
      $fbReturn = $fbObject->api_client->stream_publish($fbcmdParams[1],
        array('name' => $fbcmdParams[2], 'href' => $fbcmdParams[3], 'caption' => $fbcmdParams[4], 'description' => $fbcmdParams[5] ), null, null, $fbUser);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if ($fbReturn) {
      PrintHeaderQuiet('POST_ID');
      PrintRowQuiet($fbReturn);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POSTIMG') {
    ValidateParamCount(2,7);
    SetDefaultParam(1,$fbcmdPrefs['default_postimg_message']);
    SetDefaultParam(2,$fbcmdPrefs['default_postimg_imgsrc']);
    if ($fbcmdPrefs['default_postimg_imglink'] == '0') {
      SetDefaultParam(3,$fbcmdParams[2]);
    } else {
      SetDefaultParam(3,$fbcmdPrefs['default_postimg_imglink']);
    }
    SetDefaultParam(4,$fbcmdPrefs['default_postimg_name']);
    SetDefaultParam(5,$fbcmdPrefs['default_postimg_link']);
    SetDefaultParam(6,$fbcmdPrefs['default_postimg_caption']);
    SetDefaultParam(7,$fbcmdPrefs['default_postimg_description']);
    try {
      $fbReturn = $fbObject->api_client->stream_publish($fbcmdParams[1],
        array('name' => $fbcmdParams[4], 'href' => $fbcmdParams[5], 'caption' => $fbcmdParams[6], 'description' => $fbcmdParams[7],
          'media' => array(array( 'type' => 'image', 'src' => $fbcmdParams[2], 'href' => $fbcmdParams[3]))), null, null, $fbUser);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if ($fbReturn) {
      PrintHeaderQuiet('POST_ID');
      PrintRowQuiet($fbReturn);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POSTMP3') {
    ValidateParamCount(2,9);
    SetDefaultParam(1,$fbcmdPrefs['default_postmp3_message']);
    SetDefaultParam(2,$fbcmdPrefs['default_postmp3_mp3src']);
    SetDefaultParam(3,$fbcmdPrefs['default_postmp3_mp3title']);
    SetDefaultParam(4,$fbcmdPrefs['default_postmp3_mp3artist']);
    SetDefaultParam(5,$fbcmdPrefs['default_postmp3_mp3album']);
    SetDefaultParam(6,$fbcmdPrefs['default_postmp3_name']);
    SetDefaultParam(7,$fbcmdPrefs['default_postmp3_link']);
    SetDefaultParam(8,$fbcmdPrefs['default_postmp3_caption']);
    SetDefaultParam(9,$fbcmdPrefs['default_postmp3_description']);
    try {
      $fbReturn = $fbObject->api_client->stream_publish($fbcmdParams[1],
        array('name' => $fbcmdParams[6], 'href' => $fbcmdParams[7], 'caption' => $fbcmdParams[8], 'description' => $fbcmdParams[9],
          'media' => array(array('type' => 'mp3', 'src' => $fbcmdParams[2], 'title' => $fbcmdParams[3], 'artist' => $fbcmdParams[4], 'album' => $fbcmdParams[5]))), null, null, $fbUser);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if ($fbReturn) {
      PrintHeaderQuiet('POST_ID');
      PrintRowQuiet($fbReturn);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POSTVID') {
    ValidateParamCount(3,7);
    SetDefaultParam(1,$fbcmdPrefs['default_postvid_message']);
    SetDefaultParam(2,$fbcmdPrefs['default_postvid_vidsrc']);
    SetDefaultParam(3,$fbcmdPrefs['default_postvid_preview']);
    SetDefaultParam(4,$fbcmdPrefs['default_postvid_name']);
    SetDefaultParam(5,$fbcmdPrefs['default_postvid_link']);
    SetDefaultParam(6,$fbcmdPrefs['default_postvid_caption']);
    SetDefaultParam(7,$fbcmdPrefs['default_postvid_description']);
    try {
      $fbReturn = $fbObject->api_client->stream_publish($fbcmdParams[1],
        array('name' => $fbcmdParams[4], 'href' => $fbcmdParams[5], 'caption' => $fbcmdParams[6], 'description' => $fbcmdParams[7],
          'media' => array(array('type' => 'video', 'video_src' => $fbcmdParams[2], 'preview_img' => $fbcmdParams[3]))), null, null, $fbUser);
        TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if ($fbReturn) {
      PrintHeaderQuiet('POST_ID');
      PrintRowQuiet($fbReturn);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'PPICS') {
    ValidateParamCount(0,2);
    SetDefaultParam(1,$fbcmdPrefs['default_ppics_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_ppics_savedir']);
    GetFlistIds($fbcmdParams[1]);
    $fql = "SELECT uid,pic,pic_big,pic_small,pic_square FROM user WHERE uid IN ({$flistMatchIdString}) ORDER BY last_name";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader(PrintIfPref('show_id','UID'),'NAME','SRC');
      foreach ($fbReturn as $user) {
        if ((UserPhotoSrc($user))||($fbcmdPrefs['print_blanks'])) {
          PrintRow(PrintIfPref('show_id',$user['uid']),ProfileName($user['uid']),UserPhotoSrc($user));
        }
        if (($fbcmdParams[2])&&(UserPhotoSrc($user))) {
          SavePhoto(UserPhotoSrc($user),null,$user['uid'],$fbcmdParams[2],$fbcmdPrefs['ppics_filename'],false);
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'RECENT') {
    ValidateParamCount(0,2);
    SetDefaultParam(1,$fbcmdPrefs['default_recent_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_recent_count']);
    GetFlistIds($fbcmdParams[1],true);
    $fql = "SELECT uid,message,time FROM status WHERE uid in ({$flistMatchIdString}) ORDER BY time DESC LIMIT {$fbcmdParams[2]}";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if ($fbReturn) {
      PrintHeader(PrintIfPref('show_id','ID'),'NAME',PrintIfPref('status_show_date','TIME'),'STATUS');
      foreach ($fbReturn as $status) {
        PrintRow(PrintIfPref('show_id',$status['uid']),ProfileName($status['uid']),PrintIfPref('status_show_date',date($fbcmdPrefs['status_dateformat'],$status['time'])),$status['message']);
      }
    }
  }

  ////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'RESTATUS') {
    ValidateParamCount(1);
    GetCurrentStatus();
    if ($userStatus != '') {
      $fql = "SELECT post_id,comments.count FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} AND attachment=='' LIMIT 1";
      try {
        $fbReturn = $fbObject->api_client->fql_query($fql);
        TraceReturn($fbReturn);
      } catch(Exception $e) {
        FbcmdException($e,'GET-POST');
      }
      if (isset($fbReturn[0]['post_id'])) {
        $postID = $fbReturn[0]['post_id'];
      } else {
        FbcmdFatalError("RESTATUS: Could not retrieve previous status post_id");
      }
      $deletePost = true;
      if ($fbcmdPrefs['restatus_comment_new']) {
        if (isset($fbReturn[0]['comments']['count'])) {
          if ($fbReturn[0]['comments']['count'] > 0) {
            $deletePost = false;
          }
        } else {
          FbcmdWarning ("Can not retreive comment count for post_id = {$p}");
        }
      }
      if ($deletePost) {
        try {
          $fbReturn = $fbObject->api_client->stream_remove($postID);
          TraceReturn($fbReturn);
        } catch (Exception $e) {
          FbcmdException($e);
        }
        if (!$fbReturn) {
          FbcmdFatalError("RESTATUS: Could not remove previous status");
        }
      }
    }
    try {
      $fbReturn = $fbObject->api_client->call_method('facebook.users.setStatus',array('status' => $fbcmdParams[1],'status_includes_verb' => true));
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'SAVEDISP') {
    ValidateParamCount(1);
    SetDefaultParam(1,$fbcmdPrefs['default_savedisp_filename']);
    try {
      $fbReturn = $fbObject->api_client->profile_getFBML($fbUser,2);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
    // strip out the <fb:fbml> tags
    $fbFbml = $fbReturn;
    $fbFbml = preg_replace('/<fb:fbml version="[\d\.]+">/','',$fbFbml);
    $fbFbml = preg_replace('/<\/fb:fbml>/','',$fbFbml);
    if (@file_put_contents($fbcmdParams[1],$fbFbml) == false) {
      FbcmdFatalError("Could not write file {$fbcmdParams[1]}");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'SAVEINFO') {
    SetDefaultParam(1,$fbcmdPrefs['default_saveinfo_filename']);
    ValidateParamCount(1);
    try {
      $fbReturn = $fbObject->api_client->profile_getInfo();
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
    $fbInfoFile = var_export($fbReturn,true);
    $fbInfoFile = "<?php\n\$fbCmdInfo = {$fbInfoFile};\n?>\n";
    if (@file_put_contents($fbcmdParams[1],$fbInfoFile) == false) {
      FbcmdFatalError("Could not write file {$fbcmdParams[1]}");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'SFILTERS') {
    ValidateParamCount(0);
    $fql = "SELECT filter_key,name,rank,type FROM stream_filter WHERE uid={$fbUser} ORDER BY rank";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if ($fbReturn) {
      PrintHeader('KEY','RANK','NAME','TYPE');
      foreach ($fbReturn as $filter) {
        PrintRow($filter['filter_key'],$filter['rank']+1,$filter['name'],$filter['type']);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'STATUS') {
    ValidateParamCount(0,1);
    if (ParamCount() == 0) {
      GetCurrentStatus();
      if ($userStatus == 'unknown_status') {
        FbcmdFatalError("STATUS: Unexpected Error");
      } else {
        if ($userStatus == '') {
          print "$userName [BLANK]\n";
        } else {
          print "$userName $userStatus\n";
        }
      }
    } else {
      try {
        $fbReturn = $fbObject->api_client->call_method('facebook.users.setStatus',array('status' => $fbcmdParams[1],'status_includes_verb' => true));
        TraceReturn($fbReturn);
      } catch(Exception $e) {
        FbcmdException($e);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'STREAM') {
    ValidateParamCount(0,2);
    SetDefaultParam(1,$fbcmdPrefs['default_stream_filter']);
    SetDefaultParam(2,$fbcmdPrefs['default_stream_count']);
    if (is_numeric($fbcmdParams[1])) {
      if ($fbcmdParams[1] > 0) {
        $fbcmdParams[1] -= 1;
      }
      $filterKeyQuery = "SELECT filter_key FROM stream_filter WHERE uid={$fbUser} AND rank={$fbcmdParams[1]}";
    } else {
      if (substr($fbcmdParams[1],0,1)== $fbcmdPrefs['prefix_filter']) {
        $filterKeyQuery = "'" . GetFilterByName($fbcmdParams[1]) . "'";
      } else {
        $filterKeyQuery = "'{$fbcmdParams[1]}'";
      }
    }
    if (strtoupper($fbcmdParams[2])=='NEW') {
      CheckTimeStamp();
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes FROM stream WHERE filter_key IN ({$filterKeyQuery}) AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    } else {
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes FROM stream WHERE filter_key IN ({$filterKeyQuery}) LIMIT {$fbcmdParams[2]}";
    }
    $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id, comments.comment_list.fromid FROM #fqlStream)';
    $keyStreamNames = 'id';
    MultiFQL(array('Stream','StreamNames'));
    if (!empty($dataStream)) {
      SavePostData($dataStream);
      PrintPostHeader();
      $postNum = 0;
      foreach ($dataStream as $a) {
        PrintPostObject(++$postNum,$a);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'TAGPIC') {
    ValidateParamCount(array(2,4));
    SetDefaultParam(1,$fbcmdPrefs['default_tagpic_pid']);
    SetDefaultParam(2,$fbcmdPrefs['default_tagpic_target']);
    SetDefaultParam(3,$fbcmdPrefs['default_tagpic_x']);
    SetDefaultParam(4,$fbcmdPrefs['default_tagpic_y']);
    $tagId = null;
    $tagText = $fbcmdParams[2];
    if (strtoupper($tagText) == '=ME') {
      $tagId = $fbUser;
      $tagText = null;
    } else {
      if (is_numeric($tagText)) {
        $tagId = $tagText;
        $tagText = null;
      } else {
        MultiFQL(array('FriendId','FriendBaseInfo'));
        foreach ($dataFriendBaseInfo as $friend) {
          if (strtoupper($tagText) == strtoupper($friend['name'])) {
            $tagId = $friend['uid'];
            $tagText = null;
            break;
          }
        }
      }
    }
    try {
      $fbReturn = $fbObject->api_client->photos_addTag($fbcmdParams[1],$tagId,$tagText,$fbcmdParams[3],$fbcmdParams[4],null,null);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'UFIELDS') {
    $uFields = explode(',',$allUserInfoFields);
    printHeader('FIELD_NAME');
    foreach ($uFields as $u) {
      PrintRow($u);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'VERSION') {
    ValidateParamCount(0);
    try {
      $fbOnlineVersion = @file_get_contents('http://fbcmd.dtompkins.com/attachments/curversion.txt');
    } catch (Exception $e) {
      FbcmdFatalError('Could not connect to http://fbcmd.dtompkins.com/');
    }
    if ($fbOnlineVersion == '') {
      FbcmdFatalError('Could not connect to http://fbcmd.dtompkins.com/');
    }
    PrintHeader('THIS_VERSION','ONLINE_VERSION','DOWNLOAD_URL');
    PrintRow($fbcmdVersion,$fbOnlineVersion,'http://fbcmd.dtompkins.com/');
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'WHOAMI') {
    ValidateParamCount(0);
    $fbReturn = $fbObject->api_client->users_getInfo($fbUser,array('name'));
    PrintRow($fbUser,$fbReturn[0]['name']);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'WALLPOST') {
    ValidateParamCount(2);
    SetDefaultParam(1,$fbcmdPrefs['default_wallpost_flist']);
    SetDefaultParam(2,$fbcmdPrefs['default_wallpost_message']);
    GetFlistIds($fbcmdParams[1],true,false,true);
    PrintHeaderQuiet('POST_ID','RECIPIENT_NAME');
    foreach ($flistMatchArray as $id) {
      try {
        $fbReturn = $fbObject->api_client->stream_publish($fbcmdParams[2],null,null,$id,$fbUser);
        TraceReturn($fbReturn);
        PrintRowQuiet($fbReturn,ProfileName($id));
      } catch (Exception $e) {
        FbcmdException($e);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  PrintFinish();
  return;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function array_flatten($obj) {
    $ret = array();
    if (is_array($obj)) {
      foreach ($obj as $o) {
        if (is_array($o)) {
          $ret = array_merge($ret,array_flatten($o));
        } else {
          $ret[] = $o;
        }
      }
    } else {
      $ret[] = $obj;
    }
    return $ret;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function array_implode_safe($sep,$obj) {
    if (is_array($obj)) {
      return implode($sep,$obj);
    } else {
      return '';
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function array_merge_unique(&$arrayMain,$arrayAdd) {
    foreach ($arrayAdd as $var) {
      if (!in_array($var,$arrayMain)) {
        $arrayMain[] = $var;
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  function array_push_unique(&$array,$var) {
    if (!in_array($var,$array)) {
      $array[] = $var;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function CheckTimeStamp() {
    LoadPostData();
    global $fbcmdPrefs;
    global $lastPostData;
    if (!isset($lastPostData['timestamp'])) {
      if ($fbcmdPrefs('stream_save')) {
        FbcmdFatalError("Unexpected: Could not determine timestamp from last stream command");
      } else {
        FbcmdFatalError("NEW requires the preference -savepostdata to be set");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

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

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function DisplayField($obj) {
    if (is_array($obj)) {
      $newlist = array();
      foreach ($obj as $o) {
        $newlist[] = DisplayField($o);
      }
      return '[' . implode(',',$newlist) . ']';
    } else {
      return $obj;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdException(Exception $e, $defaultCommand = true) {
    if ($defaultCommand) {
      global $fbcmdCommand;
      $defaultCommand = $fbcmdCommand;
    }
    $eCode = $e->getCode();
    if ($eCode == 612) {
      FbcmdPermissions('read_stream');
    }
    if (($eCode == 200)||($eCode == 250)||($eCode == 281)||($eCode == 282)||($eCode == 323)) {
      FbcmdPermissions('publish_stream');
    }
    if ($eCode == 602) {
      FbcmdFatalError("FINFO: invalid field(s): {$e->getMessage()}");
    }

    FbcmdFatalError("{$defaultCommand}\n[{$eCode}] {$e->getMessage()}");
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdFatalError($err) {
    global $fbcmdVersion;
    error_log("fbcmd [v{$fbcmdVersion}] ERROR: {$err}");
    exit;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdPermissions($fbPermissionName) {
    global $fbcmdCommand;
    global $fbcmdPrefs;
    FbcmdFatalError("{$fbcmdCommand} requires special permissions:\n\nvisit the website:\nhttp://www.facebook.com/authorize.php?api_key={$fbcmdPrefs['appkey']}&v=1.0&ext_perm={$fbPermissionName}\nto grant permission\n");
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdWarning($err) {
    global $fbcmdVersion;
    error_log("fbcmd [v{$fbcmdVersion}] WARNING: {$err}");
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FileMatches($dirName, $ext) {
    $matches = array();
    $dirName = CleanPath($dirName);
    if ($handle = @opendir($dirName)) {
      while (false !== ($file = readdir($handle))) {
        if (strtoupper(substr($file,-strlen($ext)))==strtoupper($ext)) {
          $matches[] = $dirName . $file;
        }
      }
    } else {
      FbcmdFatalError("Invalid Path: {$dirName}");
    }
    closedir($handle);
    return $matches;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FlistMatch($flistItem,$isPrefixed,$dataArray,$keyId,$keyMatch,$allowMultipleMatches = true, $forceExactMatch = false) {

    $matchList = array();
    $displayMatch = array();
    $isExact = false;

    if ($isPrefixed) {
      $matchString = substr($flistItem,1);
    } else {
      $matchString = $flistItem;
    }
    $matchStringUC = strtoupper($matchString);
    if ($matchString == '') {
      FbcmdWarning("Could not match empty flist entry");
      return array();
    }
    // Check for Exact Match
    foreach ($dataArray as $element) {
      if ($matchStringUC == strtoupper($element[$keyId])) {
        $matchList[] = $matchString;
        $displayMatch[] = $element[$keyMatch];
        $isExact = true;
      }
      if ($matchStringUC == strtoupper($element[$keyMatch])) {
        $matchList[] = $element[$keyId];
        $displayMatch[] = $element[$keyMatch];
        $isExact = true;
      }
    }
    // now match for imperfect matches, including regular expressions
    if ((!$isExact)&&(!$forceExactMatch)) {
      foreach ($dataArray as $element) {
        if (preg_match("/{$matchString}/i",$element[$keyMatch])) {
          $matchList[] = $element[$keyId];
          $displayMatch[] = $element[$keyMatch];
        }
      }
    }
    if (count($matchList)==0) {
      if (is_numeric($matchString)) {
        $matchList[] = $matchString;
      } else {
        FbcmdWarning("Could not match entry: {$flistItem}");
        return array();
      }
    }
    if ((count($matchList) > 1)&&(!$allowMultipleMatches)) {
      global $fbcmdCommand;
      FbcmdWarning("{$fbcmdCommand} does not allow Multiple Matches:");
      print "flist entry {$flistItem} matched:\n";
      foreach ($displayMatch as $item) {
        print "{$item}\n";
      }
      return array();
    }
    return $matchList;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetCommentCount($p, $warn = true) {
    global $fbUser;
    global $fbObject;

    $fql = "SELECT post_id,comments.count FROM stream WHERE post_id='{$p}'";

    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e,'GET-POST');
    }
    if (isset($fbReturn[0]['comments']['count'])) {
      return $fbReturn[0]['comments']['count'];
    } else {
      FbcmdWarning ("Can not retreive comment count for post_id = {$p}");
      return 0;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetCurrentStatus() {
    global $fbUser;
    global $fbObject;
    global $userName;
    global $userStatus;
    $fql = "SELECT name,status FROM user WHERE uid={$fbUser}";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    $userName = 'unknown_name';
    $userStatus = 'unknown_status';
    if ($fbReturn) {
      if (isset($fbReturn[0]['name'])) {
        $userName = $fbReturn[0]['name'];
      }
      if (isset($fbReturn[0]['status']['message'])) {
        $userStatus = $fbReturn[0]['status']['message'];
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetFilterByName($filtName) {
    global $fbUser;
    global $fbObject;
    $fql = "SELECT filter_key,name,rank,type FROM stream_filter WHERE uid={$fbUser} ORDER BY rank";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    $matchFilterName = FlistMatch($filtName,true,$fbReturn,'filter_key','name',false);
    if (count($matchFilterName) == 0) {
      FbcmdFatalError("Could not resolve filter name {$filtName}");
    } else {
      return $matchFilterName[0];
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetFlistIds($flistString, $allowPages = false, $allowMultipleMatches = true, $failOnEmpty = true) {

    global $fbcmdPrefs;
    global $flistMatchArray;
    global $flistMatchIdString;

    $unknownNames = array();

    $flistMatchArray = array();

    $flistFQL = array('FriendId','FriendBaseInfo');
    $flistItems = explode(',',$flistString);

    // Pre-process to see if Friend Lists or Pages or Groups are required
    foreach ($flistItems as $item) {
      if (substr($item,0,1)==$fbcmdPrefs['prefix_friendlist']) {
        array_push_unique($flistFQL,'FriendListNames');
        array_push_unique($flistFQL,'FriendListMembers');
      }
      if ((substr($item,0,1)==$fbcmdPrefs['prefix_page'])||(strtoupper($item)=='=PAGES')) {
        array_push_unique($flistFQL,'PageId');
        array_push_unique($flistFQL,'PageNames');
      }
      if (substr($item,0,1)==$fbcmdPrefs['prefix_group']) {
        array_push_unique($flistFQL,'GroupNames');
      }
    }

    MultiFQL($flistFQL);

    global $dataFriendId;
    global $dataFriendBaseInfo;
    global $indexFriendBaseInfo;
    global $fbUser;
    global $flistChunkCounter;
    $flistChunkCounter = 0;

    foreach ($flistItems as $item) {

      $itemUC = strtoupper($item);

      // =KEYWORDS /////////////////////////////////////////////////////////////

      if (substr($item,0,1) == '=') {
        if ($itemUC == '=ME') {
          array_push_unique($flistMatchArray,$fbUser);
          continue;
        }
        if ($itemUC == '=ALL') {
          foreach ($dataFriendId as $fid) {
            array_push_unique($flistMatchArray,$fid['uid2']);
          }
          continue;
        }
        if ($itemUC == '=BDAY') {
          $curDate = date('m/d',time());
          foreach ($dataFriendBaseInfo as $fbi) {
            if (substr($fbi['birthday_date'],0,5)==$curDate) {
              array_push_unique($flistMatchArray,$fbi['uid']);
            }
          }
          continue;
        }
        if ($itemUC == '=ONLINE') {
          foreach ($dataFriendBaseInfo as $fbi) {
            if (($fbi['online_presence']=='active')||(($fbi['online_presence']=='idle')&&($fbcmdPrefs['online_idle']))) {
              array_push_unique($flistMatchArray,$fbi['uid']);
            }
          }
          continue;
        }
        if ($itemUC == '=PAGES') {
          if (!$allowPages) {
            global $fbcmdCommand;
            FbcmdWarning("{$fbcmdCommand} does not support pages: {$item} ignored");
          } else {
            global $dataPageId;
            foreach ($dataPageId as $page_id) {
              array_push_unique($flistMatchArray,$page_id[0]); // TODO: should be $page_id['page_id']; // SEE BUG: http://bugs.developers.facebook.com/show_bug.cgi?id=5977
            }
          }
          continue;
        }
        FbcmdWarning("Unknown flist entry: {$item}");
        continue;
      }

      // _FRIEND LIST //////////////////////////////////////////////////////////

      if (substr($item,0,1) == $fbcmdPrefs['prefix_friendlist']) {
        global $dataFriendListNames;
        global $dataFriendListMembers;
        $flidMatches = FlistMatch($item,true,$dataFriendListNames,'flid','name',$allowMultipleMatches);
        if (count($flidMatches)) {
          foreach ($dataFriendListMembers as $flm) {
            // http://bugs.developers.facebook.com/show_bug.cgi?id=5977
            // if (in_array($flm[0],$flidMatches)) {
              // array_push_unique($flistMatchArray,$flm[1]);
            // }
            if (in_array($flm['flid'],$flidMatches)) {
              array_push_unique($flistMatchArray,$flm['uid']);
            }
          }
        }
        continue;
      }

      // @USERNAME /////////////////////////////////////////////////////////////

      if (substr($item,0,1) == $fbcmdPrefs['prefix_username']) {
        $uidMatches = FlistMatch($item,true,$dataFriendBaseInfo,'uid','username',$allowMultipleMatches);
        array_merge_unique($flistMatchArray,$uidMatches);
        continue;
      }

      // +PAGES ////////////////////////////////////////////////////////////////

      if (substr($item,0,1) == $fbcmdPrefs['prefix_page']) {
        if (!$allowPages) {
          global $fbcmdCommand;
          FbcmdWarning("{$fbcmdCommand} does not support pages: {$item} ignored");
        } else {
          global $dataPageNames;
          $pidMatches = FlistMatch($item,true,$dataPageNames,'page_id','name',$allowMultipleMatches);
          array_merge_unique($flistMatchArray,$pidMatches);
        }
        continue;
      }

      // ~GROUPS ///////////////////////////////////////////////////////////////

      if (substr($item,0,1) == $fbcmdPrefs['prefix_group']) {
        global $dataGroupNames;
        global $fbObject;
        $gidMatches = FlistMatch($item,true,$dataGroupNames,'gid','name',false);
        if (isset($gidMatches[0])) {
          $fql = "SELECT uid FROM group_member WHERE gid={$gidMatches[0]}";
          try {
            $fbReturn = $fbObject->api_client->fql_query($fql);
            TraceReturn($fbReturn);
          } catch(Exception $e) {
            FbcmdException($e);
          }
          if (!empty($fbReturn)) {
            foreach ($fbReturn as $u) {
              $flistMatchArray[] = $u['uid'];
            }
          } else {
            FbcmdWarning("Could Not get Group Members for GROUP {$gidMatches[0]}");
          }
        }
        continue;
      }

      // REGULAR NAMES /////////////////////////////////////////////////////////

      $uidMatches = FlistMatch($item,false,$dataFriendBaseInfo,'uid','name',$allowMultipleMatches);
      array_merge_unique($flistMatchArray,$uidMatches);
    }
    if (count($flistMatchArray)==0) {
      if ($failOnEmpty) {
        FbcmdFatalError("Empty flist: {$flistString}");
      } else {
        $flistMatchIdString = '';
      }
    } else {
      $flistMatchIdString = implode(',',$flistMatchArray);
    }

    foreach ($flistMatchArray as $id) {
      if (ProfileName($id) == 'unknown') {
        $unknownNames[] = $id;
      }
    }
    if (count($unknownNames) > 0) {
      global $fqlFlistNames;
      global $keyFlistNames;
      $fqlFlistNames = 'SELECT id,name FROM profile WHERE id IN (' . implode(',',$unknownNames) . ')';
      $keyFlistNames = 'id';
      MultiFQL(array('FlistNames'));
    }
    return;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetLatestAID() {
    global $fbUser;
    global $fbObject;
    $fql = "SELECT aid,name FROM album WHERE owner={$fbUser} ORDER BY created DESC LIMIT 1";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e,'LATEST-AID');
    }
    if (isset($fbReturn[0]['aid'])) {
      return $fbReturn[0]['aid'];
    } else {
      return null;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetNextChunkIds() {
    global $fbcmdPrefs;
    global $flistChunkCounter;
    global $flistMatchArray;

    if ($flistChunkCounter == -1) {
      return null;
    }
    if ($fbcmdPrefs['flist_chunksize']) {
      $startPos = $flistChunkCounter * $fbcmdPrefs['flist_chunksize'];
      $flistChunkCounter++;
      $len = $fbcmdPrefs['flist_chunksize'];
      if ($startPos + $len >= count($flistMatchArray)) {
        $flistChunkCounter = -1;
        $len = count($flistMatchArray) - $startPos;
      }
      return array_slice($flistMatchArray,$startPos,$len);
    } else {
      $flistChunkCounter = -1;
      return $flistMatchArray;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetPostId($p, $allowSpecial = false) {
    global $lastPostData;
    global $userStatus;
    global $fbUser;
    global $fbObject;

    if (($p == 0)||(strtoupper($p) == 'LAST')||(strtoupper($p) == 'CURSTATUS')) {
      if ($allowSpecial) {
        if (strtoupper($p) == 'CURSTATUS') {
          GetCurrentStatus();
          if ($userStatus == '') {
            FbcmdFatalError("CURSTATUS: Your status is blank");
          }
          $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} AND attachment=='' LIMIT 1";
        } else {
          $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} LIMIT 1";
        }
        try {
          $fbReturn = $fbObject->api_client->fql_query($fql);
          TraceReturn($fbReturn);
        } catch(Exception $e) {
          FbcmdException($e,'GET-POST');
        }
        if (isset($fbReturn[0]['post_id'])) {
          return $fbReturn[0]['post_id'];
        } else {
          FbcmdFatalError("GETPOST: Could not retrieve post_id = {$p}");
        }
      } else {
        global $fbcmdCommand;
        FbcmdWarning ("{$fbcmdCommand} does not support post_id = {$p}");
      }
    } else {
      if ($p < 1001) {
        LoadPostData();
        if (isset($lastPostData['ids'][$p])) {
          return $lastPostData['ids'][$p];
        } else {
          FbcmdWarning ("Invalid Post ID: {$p}");
          return false;
        }
      } else {
        return $p;
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function IsEmpty($obj) {
    if (is_array($obj)) {
      foreach ($obj as $o) {
        if (!IsEmpty($o)) {
          return false;
        }
      }
      return true;
    } else {
      if ($obj) {
        return false;
      } else {
        return true;
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function LoadPostData() {
    global $fbcmdPrefs;
    global $lastPostData;
    if (isset($lastPostData)) {
      return;
    } else {
      $lastPostData = array('0');
      if ($fbcmdPrefs['stream_save']) {
        if (!file_exists($fbcmdPrefs['postfile'])) {
          FbcmdWarning("Could not locate file {$fbcmdPrefs['postfile']}");
        } else {
          $lastPostData = unserialize(@file_get_contents($fbcmdPrefs['postfile']));
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function MultiFQL($queryList) {

    // This Function wraps the MultiQuery() API function in a non-obvious but convenient way:
    //
    // MultiFQL(array('Query1','Query2'))
    //
    // requires the global variables:
    //
    // $fqlQuery1 = "SELECT ...."
    // $fqlQuery2 = "SELECT x FROM y WHERE x IN (SELECT z FROM #fqlQuery1)"
    //
    // and generates the global variables $dataQuery1 and $dataQuery2
    // also, if $keyQuery1 is defined, then an associative array $indexQuery1 is generated

    global $fbObject;

    $queryStrings = Array();
    foreach ($queryList as $queryName) {
      $queryStrings[] = '"fql' . $queryName . '":"' . $GLOBALS['fql' . $queryName] . '"';
    }
    try {
      $fbMultiFqlReturn = $fbObject->api_client->fql_multiquery("{" . implode(',',$queryStrings) . "}");
      TraceReturn($fbMultiFqlReturn);
    } catch (Exception $e) {
      FbcmdException($e,'MultiFQL');
    }
    if ($fbMultiFqlReturn) {
      for ($i=0; $i < count($queryList); $i++) {
        foreach ($fbMultiFqlReturn as $ret) {
          if ($ret['name'] == 'fql' . $queryList[$i]) {
            $GLOBALS['data' . $queryList[$i]] = $ret['fql_result_set'];
            if (isset($GLOBALS['key' . $queryList[$i]])) {
              $GLOBALS['index' . $queryList[$i]] = Array();
              if ((is_array($ret['fql_result_set']))&&(count($ret['fql_result_set'] > 0))) {
                foreach ($ret['fql_result_set'] as $record) {
                  $GLOBALS['index' . $queryList[$i]][$record[$GLOBALS['key' . $queryList[$i]]]] = $record;
                }
              }
            }
          }
        }
      }
    } else {
      FbcmdFatalError('Unexpected: MultiFQL Empty');
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function MultiFqlById($idArray,$selectStatement) {
    global $fbObject;
    $queryStrings = array();
    foreach ($idArray as $id) {
      $queryStrings[] = '"fql' . $id . '":"' . str_replace('[id]', $id, $selectStatement). '"';
    }
    try {
      $fbReturn = $fbObject->api_client->fql_multiquery("{" . implode(',',$queryStrings) . "}");
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e,'MULTI-FQL-ID');
    }
    $results = array();
    if ($fbReturn) {
      foreach ($fbReturn as $ret) {
        if($ret['fql_result_set']) {
          $id = substr($ret['name'],3);
          $results[$id] = $ret['fql_result_set'];
        }
      }
    }
    foreach ($idArray as $id) {
      if (!isset($results[$id])) {
        $results[$id] = null;
      }
    }
    return $results;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function NotifyHelper($notifyArray,$dataNotify,$baseType,$inviteType) {
    if ($notifyArray) {
      PrintRow($baseType . '_' . $inviteType,count($notifyArray));
      for ($j=0; $j < count($dataNotify); $j++) {
        PrintRow($baseType . '_'  . ($j+1), $dataNotify[$j]['name']);
      }
    } else {
      PrintRow($baseType . '_' . $inviteType,'0');
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ParamCount()
  {
    global $fbcmdParams;
    return count($fbcmdParams)-1;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ParseArguments($in_argv,$in_argc) {
    global $fbcmdCommand;
    global $fbcmdParams;
    global $fbcmdPrefs;
    global $fbcmdPrefAliases;

    for ($i=1; $i < $in_argc; $i++) {
      $curArg = $in_argv[$i];
      if (substr($curArg,0,1) == '-') {
        while (substr($curArg,0,1) == '-') {
          $curArg = substr($curArg,1);
        }
        if (strpos($curArg,"=")) {
          $switchKey = substr($curArg,0,strpos($curArg,"="));
          $switchValue = substr($curArg,strpos($curArg,"=")+1);
          if ($switchValue == '') {
            $switchValue = '0';
          }
        } else {
          $switchKey = $curArg;
          $switchValue = '1';
        }
        $switchKey = strtolower($switchKey);
        if (isset($fbcmdPrefAliases[$switchKey])) {
          $switchKey = $fbcmdPrefAliases[$switchKey];
        }
        if (isset($fbcmdPrefs[$switchKey])) {
          $fbcmdPrefs[$switchKey] = $switchValue;
        } else {
          FbcmdWarning("Ignoring Parameter {$i}: Unknown Switch [{$switchKey}]");
        }
      } else {
        if ($fbcmdCommand == '') {
          $fbcmdCommand = strtoupper($curArg);
          $fbcmdParams[] = $fbcmdCommand;
        } else {
          $fbcmdParams[] = $curArg;
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PhotoSrc($obj) {
    global $fbcmdPrefs;
    $fieldName = 'src_big';
    if ($fbcmdPrefs['pic_size']==0) {
      $fieldName = 'src_small';
    }
    if ($fbcmdPrefs['pic_size']==2) {
      $fieldName = 'src';
    }
    if (isset($obj[$fieldName])) {
      return $obj[$fieldName];
    } else {
      return '';
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintAttachmentData($base,$post,$field,$display) {
    global $fbcmdPrefs;
    if (isset($post['attachment'][$field])) {
      if ($post['attachment'][$field] != '') {
        PrintRow($base,$display,htmlspecialchars_decode(strip_tags($post['attachment'][$field])));
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function PrintCsvRow($rowIn) {
    global $fbcmdPrefs;
    $rowOut = array();
    foreach ($rowIn as $col) {
      $bookend = false;
      if (strpos($col,$fbcmdPrefs['csv_bookend'])) {
        $col = str_replace($fbcmdPrefs['csv_bookend'],$fbcmdPrefs['csv_escaped_bookend'],$col);
        $bookend = true;
      }
      if ((strpos($col,$fbcmdPrefs['csv_separator']))||($fbcmdPrefs['csv_force_bookends'])) {
        $bookend = true;
      }
      if ($bookend) {
        $col = $fbcmdPrefs['csv_bookend'] . $col . $fbcmdPrefs['csv_bookend'];
      }
      $rowOut[] = $col;
    }
    print implode($fbcmdPrefs['csv_separator'],$rowOut) . "\n";
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintFinish() {
    global $fbcmdPrefs;
    global $printMatrix;
    if ($fbcmdPrefs['print_csv']) {
      return;
    }
    if (isset($printMatrix)) {
      $columnWidth = Array();
      if (count($printMatrix) > 0) {
        // Determine column widths
        foreach ($printMatrix as $row) {
          while (count($row) > count($columnWidth)) {
            $columnWidth [] = 0;
          }
          for ($i=0; $i<count($row); $i++) {
            if (strlen($row[$i])>$columnWidth[$i]) {
              $columnWidth[$i]=strlen($row[$i]);
            }
          }
        }
        foreach ($printMatrix as $row) {
          for ($i=0; $i<count($row); $i++) {
            print str_pad ($row[$i],$columnWidth[$i]+2,' ');
          }
          print "\n";
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintHeader() {
    global $fbcmdPrefs;
    if ($fbcmdPrefs['print_header']) {
      PrintRow(func_get_args());
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintHeaderQuiet() {
    global $fbcmdPrefs;
    if ($fbcmdPrefs['quiet']) {
      return;
    }
    if ($fbcmdPrefs['print_header']) {
      PrintRow(func_get_args());
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintIf($boolValue,$optVar) {
    if ($boolValue) {
      return $optVar;
    } else {
      return 'SKIP_COLUMN';
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintIfPref($paramName,$optVar) {
    global $fbcmdPrefs;
    return PrintIf($fbcmdPrefs[$paramName],$optVar);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintPostHeader() {
    global $fbcmdPrefs;
    $header = array();
    $header[] = PrintIfPref('stream_save','[#]');
    $header[] = PrintIfPref('stream_show_postid','POST_ID');
    $header[] = PrintIfPref('show_id','SOURCE_UID');
    $header[] = 'NAME';
    $header[] = PrintIfPref('stream_show_date','TIME');
    $header[] = 'TYPE';
    $header[] = 'MESSAGE';
    PrintHeader($header);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintPostObject($postNum, $post, $commentData = false) {

    global $fbcmdPrefs;

    $postInfo = array();

    if ($fbcmdPrefs['stream_save']) {
      $postInfo[] = '[' . $postNum . ']';
    }

    if ($fbcmdPrefs['stream_show_postid']) {
      $postInfo[] = $post['post_id'];
    }

    $userInfo = array();
    $userInfo[] = PrintIfPref('show_id',$post['actor_id']);

    $userInfo[] = ProfileName($post['actor_id']);

    $timeInfo = PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$post['created_time']));

    if ($post['attachment']) {
      if (isset($post['attachment']['media'][0]['type'])) {
        $msgType = $post['attachment']['media'][0]['type'] . ' post';
      } else {
        $msgType = 'attach post';
      }
    } else {
      if ($post['app_data']) {
        $msgType = 'app post';
      } else {
        if ($post['target_id']) {
          $msgType = 'wall post';
        } else {
          $msgType = 'status';
        }
      }
    }

    $msgShow = $post['message'];

    if ($post['target_id']) {
      $msgShow = '--> ' . ProfileName($post['target_id']) . ' :: ' . $post['message'];
    } else {
      $msgShow = $post['message'];
    }
    if ($msgShow == '') if (isset($post['attachment']['name'])) $msgShow = $post['attachment']['name'];
    if ($msgShow == '') if (isset($post['attachment']['caption'])) $msgShow = $post['attachment']['caption'];
    if ($msgShow == '') if (isset($post['attachment']['href'])) $msgShow = $post['attachment']['href'];
    if ($msgShow == '') if (isset($post['attachment']['description'])) $msgShow = $post['attachment']['description'];

    PrintRow($postInfo,$userInfo,$timeInfo,$msgType,$msgShow);

    if ($fbcmdPrefs['stream_show_appdata']) {
      if ($post['app_data'] != '') {
        PrintRecursiveObject(array($postInfo,$userInfo,$timeInfo),':app',$post['app_data']);
      }
    }

    if ($fbcmdPrefs['stream_show_attachments']) {
      if ($post['attachment']) {
        PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'name',':name');
        PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'href',':link');
        PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'caption',':caption');
        PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'description',':desc');
      }
    }

    if ($fbcmdPrefs['stream_show_likes']) {
      if (isset($post['likes']['count'])) {
        if ($post['likes']['count'] > 0) {
          if ($post['likes']['count']==1) {
            $likesMessage = '1 person likes this.';
          } else {
            $likesMessage = "{$post['likes']['count']} people like this.";
          }
          if (isset($post['likes']['friends'])) {
            if ((is_array($post['likes']['friends']))&&(count($post['likes']['friends']) > 0)) {
              $likers = array();
              foreach ($post['likes']['friends'] as $id) {
                $likers[] = ProfileName($id);
              }
              $likesMessage = $likesMessage . ' (' . implode(',',$likers) . ')';
            }
          }
          PrintRow($postInfo,$userInfo,$timeInfo,':likes',$likesMessage);
        }
      }
    }

    if ($commentData) {
      $shownCount = count($commentData);
      if (isset($post['comments']['count'])) {
        $totalCount = $post['comments']['count'];
        if ($shownCount < $totalCount) {
          PrintRow($postInfo,$userInfo,$timeInfo,':comment',"Showing {$shownCount} of {$totalCount} Comments");
        }
      }
      foreach ($commentData as $comment) {
        $timeInfo = PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$comment['time']));
        PrintRow($postInfo,$userInfo,$timeInfo,':comment',ProfileName($comment['fromid']) . ' :: ' . $comment['text']);
      }
    } else {
      if ($fbcmdPrefs['stream_show_comments']) {
        if (isset($post['comments']['count'])) {
          $totalCount = $post['comments']['count'];
          if ($totalCount > 0) {
            $shownCount = 0;
            if (isset($post['comments']['comment_list'])) {
              if (is_array($post['comments']['comment_list'])) {
                $shownCount = count($post['comments']['comment_list']);
              }
            }
            if ($shownCount == 0) {
              PrintRow($postInfo,$userInfo,$timeInfo,':comment',"{$totalCount} Comments");
            } else {
              if ($shownCount < $totalCount) {
                PrintRow($postInfo,$userInfo,$timeInfo,':comment',"Showing {$shownCount} of {$totalCount} Comments");
              }
              foreach ($post['comments']['comment_list'] as $comment) {
                $timeInfo = PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$comment['time']));
                PrintRow($postInfo,$userInfo,$timeInfo,':comment',ProfileName($comment['fromid']) . ' :: ' . $comment['text']);
              }
            }
          }
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

 function PrintRow() {
    global $fbcmdPrefs;
    global $printMatrix;
    $rowArray = array_flatten(func_get_args());
    $goodColumns = array();
    foreach ($rowArray as $col) {
      if (is_array($col)) {
        foreach ($col as $c) {
          if ($c != 'SKIP_COLUMN') {
            $goodColumns[] = $c;
          }
        }
      } else {
        if ($col != 'SKIP_COLUMN') {
          $goodColumns[] = $col;
        }
      }
    }
    if ($fbcmdPrefs['hide_dup_rows']) {
      for ($j=0;$j<count($goodColumns);$j++) {
        $row = count($printMatrix)-1;
        $match = false;
        $search = true;
        while (($search)&&($row >= 0)) {
          if (isset($printMatrix[$row][$j])) {
            if ($printMatrix[$row][$j] == '') {
              $row--;
            } else {
              $search = false;
              if ($printMatrix[$row][$j]==$goodColumns[$j]) {
                $match = true;
              }
            }
          } else {
            $search = false;
          }
        }
        if ($match) {
          $goodColumns[$j] = '';
        } else {
          break;
        }
      }
    }
    $printMatrix[] = $goodColumns;

    if ($fbcmdPrefs['print_csv']) {
      PrintCsvRow($goodColumns);
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintRowQuiet() {
    global $fbcmdPrefs;
    if ($fbcmdPrefs['quiet']) {
      return;
    }
    PrintRow(func_get_args());
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintRecursiveObject ($arrayPrefix, $fieldPrefix, $obj) {
    global $fbcmdPrefs;
    if (is_array($obj)) {
      foreach ($obj as $key=>$value) {
        if ($fieldPrefix=='') {
          PrintRecursiveObject($arrayPrefix,"{$key}",$value);
        } else {
          PrintRecursiveObject($arrayPrefix,"{$fieldPrefix}.{$key}",$value);
        }
      }
    } else {
      if (($obj)||($fbcmdPrefs['print_blanks'])) {
        $row = $arrayPrefix;
        $row[] = $fieldPrefix;
        $row[] = $obj;
        PrintRow($row);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintStart() {
    global $printMatrix;
    $printMatrix = Array();
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ProcessEventMask($mask) {
    if ((!is_numeric($mask))||($mask > 15)||($mask <= 0)) {
      FbcmdWarning("Invalid Event Mask: using 1 ('attending')");
      $mask = 1;
    }
    $eventOptions = array();
    if ($mask & 1) $eventOptions[] = "'attending'";
    if ($mask & 2) $eventOptions[] = "'unsure'";
    if ($mask & 4) $eventOptions[] = "'not_replied'";
    if ($mask & 8) $eventOptions[] = "'declined'";
    return implode(',',$eventOptions);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ProfileName($id) {
    global $indexFriendBaseInfo;
    global $indexPageNames;
    global $indexStreamNames;
    global $indexFlistNames;
    if (isset($indexFriendBaseInfo[$id])) {
      return $indexFriendBaseInfo[$id]['name'];
    }
    if (isset($indexPageNames[$id])) {
      return $indexPageNames[$id]['name'];
    }
    if (isset($indexStreamNames[$id])) {
      return $indexStreamNames[$id]['name'];
    }
    if (isset($indexFlistNames[$id])) {
      return $indexFlistNames[$id]['name'];
    }
    return 'unknown';
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SavePhoto($urlSource, $picObject, $tagId, $outputDir, $fileFormat, $checkSkip = true) {
    global $fbcmdPrefs;
    $photoContents = false;
    $retry=0;

    $fileFormat = str_replace('\\', '/', $fileFormat);
    if ($picObject) {
      $fileFormat = str_replace('[aid]', $picObject['aid'], $fileFormat);
      $fileFormat = str_replace('[pid]', $picObject['pid'], $fileFormat);
      $fileFormat = str_replace('[oid]', $picObject['owner'], $fileFormat);
      $fileFormat = str_replace('[oname]', ProfileName($picObject['owner']), $fileFormat);
    }
    if ($tagId) {
      $fileFormat = str_replace('[tid]', $tagId, $fileFormat);
      $fileFormat = str_replace('[tname]', ProfileName($tagId), $fileFormat);
    }
    $outputFile = CleanPath($outputDir) . $fileFormat;
    VerifyOutputDir($outputFile);

    if (($fbcmdPrefs['pic_skip_exists'])&&($checkSkip)) {
      if (file_exists($outputFile)) {
        return false;
      }
    }

    do {
      try {
        $photoContents = @file_get_contents($urlSource);
      } catch (Exception $e) {
        FbcmdWarning("[{$e->getCode()}] {$e->getMessage()}");
      }
      if ($photoContents == false) {
        if (++$retry > $fbcmdPrefs['pic_retry_count']) {
          FbcmdWarning("Could not download {$urlSource}");
          return false;
        } else {
          FbcmdWarning("Retry {$retry} :: {$urlSource}");
          sleep($fbcmdPrefs['pic_retry_delay']);
        }
      }
    } while ($photoContents == false);

    if (file_put_contents($outputFile, $photoContents)==false) {
      FbcmdWarning("Could not save {$outputFile}");
      return false;
    }

    return true;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SavePostData($obj) {
    global $fbcmdPrefs;
    if ($fbcmdPrefs['stream_save']) {
      $savePostData = array ('ids' => array('0'), 'timestamp' => time());
      foreach ($obj as $post) {
        $savePostData['ids'][] = $post['post_id'];
      }
      if (@file_put_contents($fbcmdPrefs['postfile'],serialize($savePostData)) == false) {
        FbcmdWarning("Could not generate keyfile {$fbcmdPrefs['postfile']}");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SavePrefs($fileName) {
    global $fbcmdPrefs;
    $fileContents = "<?php\n";
    foreach ($fbcmdPrefs as $switchKey => $switchValue) {
      if ($switchKey != 'prefs') {
        if (($fbcmdPrefs['savepref_include_files'])||(($switchKey != 'keyfile')&&($switchKey != 'postfile'))) {
          $fileContents .= "  \$fbcmdPrefs['{$switchKey}'] = " . var_export($switchValue,true) . ";\n";
        }
      }
    }
    $fileContents .= "?>\n";
    if (@file_put_contents($fileName,$fileContents) == false) {
      FbcmdFatalError("Could not write {$fileName}");
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SetDefaultParam($n,$value)
  {
    global $fbcmdParams;
    if (ParamCount() < $n) {
      $fbcmdParams[$n] = $value;
    } else {
      if ($fbcmdParams[$n] == '0') {
        $fbcmdParams[$n] = $value;
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ShowUsage() {
    global $fbcmdVersion;

    print "\n";
    print "fbcmd [v{$fbcmdVersion}] Facebook Command Line Interface\n\n";

    print "usage:\n\n";

    print "  fbcmd COMMAND required_parameter(s) [optional_parameter(s)] -switch=value\n\n";

    print "commands: (can be in lower case)\n\n";

    print "  ADDALBUM  title [description] [location] [privacy]                           \n";
    print "            Create a new photo album                                           \n\n";

    print "  ADDPIC    filename [album_id] [caption]                                      \n";
    print "            Upload (add) a photo to an album                                   \n\n";

    print "  ADDPICD   dirname [album_id]                                                 \n";
    print "            Upload (add) all *.jpg files in a directory to an album            \n\n";

    print "  ALBUMS    [flist]                                                            \n";
    print "            List all photo albums for friend(s)                                \n\n";

    print "  ALLINFO   flist                                                              \n";
    print "            List all available profile information for friend(s)               \n\n";

    print "  APICS     album_id [savedir]                                                 \n";
    print "            List [and optionally save] all photos from an album                \n\n";

    print "  AUTH      authcode                                                           \n";
    print "            Sets your facebook authorization code                              \n\n";

    print "  COMMENT   post_id text                                                       \n";
    print "            Add a comment to a story that appears in the stream                \n\n";

    print "  DELPOST   post_id                                                            \n";
    print "            Deletes a post from your stream                                    \n\n";

    print "  DISPLAY   fbml                                                               \n";
    print "            Sets the content of your FBCMD profile box                         \n\n";

    print "  EVENTS    [time]                                                             \n";
    print "            Display your events                                                \n\n";

    print "  FEED1     title                                                              \n";
    print "            Add a one-line story to your news feed                             \n\n";

    print "  FEED2     title body [img_src img_link]                                      \n";
    print "            Add a short story to your news feed with optional picture          \n\n";

    print "  FEEDLINK  [link] text                                                        \n";
    print "            Share a link in your news feed                                     \n\n";

    print "  FEEDNOTE  title body                                                         \n";
    print "            Share a note in your news feed                                     \n\n";

    print "  FEVENTS   flist [time]                                                       \n";
    print "            List events for friend(s)                                          \n\n";

    print "  FGROUPS   [flist]                                                            \n";
    print "            List groups that friend(s) are members of                          \n\n";

    print "  FINFO     fields [flist]                                                     \n";
    print "            List information fields for friend(s) (see UFIELDS)                \n\n";

    print "  FLAST     flist [count]                                                      \n";
    print "            See the last [count] status updates of friend(s)                   \n\n";

    print "  FONLINE   [flist]                                                            \n";
    print "            List any friends who are currently online                          \n\n";

    print "  FPICS     flist [savedir]                                                    \n";
    print "            List [and optionally save] all photos where friend(s) are tagged   \n\n";

    print "  FQL       statement [flist]                                                  \n";
    print "            Perform a custom FQL Query                                         \n\n";

    print "  FRIENDS   [flist]                                                            \n";
    print "            Generate a list of all your friends                                \n\n";

    print "  FSTATUS   [flist]                                                            \n";
    print "            List current status of friend(s)                                   \n\n";

    print "  FSTREAM   [flist] [count]                                                    \n";
    print "            Show stream stories for friend(s)                                  \n\n";

    print "  FULLPOST  post_id                                                            \n";
    print "            Displays a stream post with all of the comments                    \n\n";

    print "  HELP      <no parameters>                                                    \n";
    print "            Display this message                                               \n\n";

    print "  LIKE      post_ids                                                           \n";
    print "            Like a story that appears in the stream                            \n\n";

    print "  LIMITS    <no parameters>                                                    \n";
    print "            Display current limits on FBCMD usage                              \n\n";

    print "  LOADDISP  fbml_filename                                                      \n";
    print "            Same as DISPLAY but loads the contents from a file                 \n\n";

    print "  LOADINFO  info_filename                                                      \n";
    print "            Sets the content of the FBCMD section on your Info Tab             \n\n";

    print "  LOADNOTE  title filename                                                     \n";
    print "            Same as FEEDNOTE but loads the contents from a file                \n\n";

    print "  MUTUAL    flist                                                              \n";
    print "            List friend(s) in common with other friend(s)                      \n\n";

    print "  NOTIFY    <no parameters>                                                    \n";
    print "            See (simple) notifications such as # of unread messages            \n\n";

    print "  NSEND     flist message                                                      \n";
    print "            Send a notification message to friend(s)                           \n\n";

    print "  OPICS     flist [savedir]                                                    \n";
    print "            List [and optionally save] all photos owned by friend(s)           \n\n";

    print "  POST      message [name] [link] [caption] [desc]                             \n";
    print "            Post (share) a story in your stream                                \n\n";

    print "  POSTIMG   message img_src [img_link] [name] [link] [caption] [desc]          \n";
    print "            Post (share) an image in your stream                               \n\n";

    print "  POSTMP3   msg mp3_src [title] [artist] [album] [name] [link] [caption] [desc] \n";
    print "            Post (share) an .mp3 file in your stream                           \n\n";

    print "  POSTVID   message vid_src img_src [name] [link] [caption] [desc]             \n";
    print "            Post (share) a video in your stream                                \n\n";

    print "  PPICS     [flist] [savedir]                                                  \n";
    print "            List [and optionally save] all profile photos of friend(s)         \n\n";

    print "  RECENT    [flist] [count]                                                    \n";
    print "            Shows the [count] most recent friend status updates                \n\n";

    print "  RESET     <no parameters>                                                    \n";
    print "            Reset any authorization codes set by AUTH                          \n\n";

    print "  RESTATUS  message                                                            \n";
    print "            Replace your status (deletes your status and adds a new status)    \n\n";

    print "  SAVEDISP  fbml_filename                                                      \n";
    print "            Saves the content of your FBCMD profile box to a file              \n\n";

    print "  SAVEINFO  info_filename                                                      \n";
    print "            Saves the content of the FBCMD section on your Info Tab to a file  \n\n";

    print "  SAVEPREF  [filename]                                                         \n";
    print "            Save your current preferences / switch settings to a file          \n\n";

    print "  SFILTERS  <no parameters>                                                    \n";
    print "            Display available stream filters for the STREAM command            \n\n";

    print "  STATUS    [message]                                                          \n";
    print "            Set your status (or display current status if no parameter)        \n\n";

    print "  STREAM    [stream_filter] [count]                                            \n";
    print "            Show stream stories (with optional filter)                         \n\n";

    print "  TAGPIC    pic_id target [x y]                                                \n";
    print "            Tag a photo                                                        \n\n";

    print "  UFIELDS   <no parameters>                                                    \n";
    print "            List current user table fields (for use with FINFO)                \n\n";

    print "  VERSION   <no parameters>                                                    \n";
    print "            Check for the latest version of FBCMD available                    \n\n";

    print "  WHOAMI    <no parameters>                                                    \n";
    print "            Display the currently authorized user                              \n\n";

    print "  WALLPOST  flist message                                                      \n";
    print "            Post a message on the wall of friend(s)                            \n\n";

    print "examples:\n\n";

    print "  fbcmd status \"is excited to play with fbcmd\"\n";
    print "  fbcmd finfo birthday_date -csv\n";
    print "  fbcmd stream 1 25\n\n";

    print "for additional help, examples, parameter usage, flists, preference settings,\n";
    print "visit the FBCMD wiki at:\n\n";
    print "  http://fbcmd.dtompkins.com\n\n";
    exit;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function TraceReturn($obj) {
    global $fbcmdPrefs;
    if ($fbcmdPrefs['trace']) {
      print_r ($obj);
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function UserPhotoSrc($obj) {
    global $fbcmdPrefs;
    $fieldName = 'pic_big';
    if ($fbcmdPrefs['ppic_size']==0) {
      $fieldName = 'pic_small';
    }
    if ($fbcmdPrefs['ppic_size']==2) {
      $fieldName = 'pic';
    }
    if ($fbcmdPrefs['ppic_size']==3) {
      $fieldName = 'pic_square';
    }
    if (isset($obj[$fieldName])) {
      return $obj[$fieldName];
    } else {
      return '';
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ValidateParamCount($a, $b=null)
  {
    global $fbcmdParams;
    global $fbcmdCommand;
    $num  = ParamCount();
    $showHelp = false;
    if (is_array($a)) {
      if (!in_array($num,$a)) {
        $showHelp = true;
      }
    } else {
      if ($b == null) {
        if ($num != $a) {
          $showHelp = true;
        }
      } else {
        if (($num < $a)||($num > $b)) {
          $showHelp = true;
        }
      }
    }
    if ($showHelp) {
      FbcmdFatalError("[{$fbcmdCommand}] Invalid number of parameters: try fbcmd HELP\n      visit http://fbcmd.dtompkins.com/commands/" . strtolower($fbcmdCommand) . " for more information");
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function VerifyOutputDir($fileName) {
    global $fbcmdPrefs;
    if (strrpos($fileName,'/')) {
      $filePath = CleanPath(substr($fileName,0,strrpos($fileName,'/')));
      if (!file_exists($filePath)) {
        if ($fbcmdPrefs['pic_mkdir']) {
          if (!mkdir($filePath,$fbcmdPrefs['pic_mkdir_mode'],true)) {
            FbcmdFatalError("Could Not Create Path: {$filePath}");
          }
        } else {
          FbcmdFatalError("Invalid Path: {$filePath}");
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

?>
