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
//                                                                            //
//   [Project Home Page & wiki]  http://fbcmd.dtompkins.com                   //
//   [Facebook Page]             http://facebook.com/fbcmd                    //
//   [Discussion Group]          http://groups.google.com/group/fbcmd         //
//   [Open Source Repository]    http://github.com/dtompkins/fbcmd            //
//                                                                            //
//   Copyright (c) 2007,2012 Dave Tompkins                                    //
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
//   http://fbcmd.dtompkins.com/contribute                                    //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   http://fbcmd.dtompkins.com/history for a revision history.               //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Disclaimer: This is my first (and currently only) PHP applicaiton,       //
//               so my apologies if I don't follow PHP best practices.        //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

  $fbcmdVersion = '1.1';

////////////////////////////////////////////////////////////////////////////////

  // you can include fbcmd.php from another program
  // see support/sample-mycmd.php for more information

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
  // your peronal files: sessionkeys.txt, prefs.php, postdata.txt, maildata.txt

  // Defaults: Windows:          %USERPROFILE%\fbcmd\ (c:\Users\YOURUSERNAME\fbcmd\)
  // Defaults: Mac/Linux/Other:  $HOME/.fbcmd/        (~/.fbcmd/)

  $fbcmdBaseDir = getenv('FBCMD');
  if ($fbcmdBaseDir) {
    $fbcmdBaseDir = CleanPath($fbcmdBaseDir);
  } else {
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
      if (getenv('USERPROFILE')) {
        $fbcmdBaseDir = CleanPath(getenv('USERPROFILE')) . 'fbcmd/';
      } else {
        $fbcmdBaseDir = 'c:/fbcmd/';
      }
    } else {
      $fbcmdBaseDir = CleanPath(getenv('HOME')) . '.fbcmd/';
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // This section sets your preferences

  // see http://fbcmd.dtompkins.com/preferences for more information

  // STEP ONE: System Defaults

  // Do NOT change these System Default preference values here:
  // Modify your own prefs.php file instead

  AddPreference('albumfile',"[datadir]albumdata.txt",'afile');
  AddPreference('album_save','1','asave');
  AddPreference('apics_filename','[pid].jpg','af');
  AddPreference('appkey','42463270450'); // was: d96ea311638cf65f04b33c87eacf371e (depricated?)
  AddPreference('appsecret','88af69b7ab8d437bff783328781be79b');
  AddPreference('auto_mkdir','1');
  AddPreference('csv_bookend','"');
  AddPreference('csv_escaped_bookend','""');
  AddPreference('csv_force_bookends','0','csvf');
  AddPreference('csv_separator',',');
  AddPreference('delpost_comment_fail','1');
  AddPreference('eventfile',"[datadir]eventdata.txt",'efile');
  AddPreference('event_dateformat','D M d H:i','edf');
  AddPreference('event_save','1','esave');
  AddPreference('events_attend_mask','15','emask');
  AddPreference('facebook_debug','0','debug');
  AddPreference('feed_template','60736970450');
  AddPreference('fevents_attend_mask','1','fmask');
  AddPreference('fgroups_show_id','1','gid');
  AddPreference('flist_chunksize','10','ch');
  AddPreference('folder_blankrow','1','fbr');
  AddPreference('folder_dateformat','M d H:i','fdf');
  AddPreference('folder_show_date','0','fd');
  AddPreference('folder_show_snippet','1','snip');
  AddPreference('folder_show_threadid','0','tid');
  AddPreference('fpics_filename','[pid].jpg','ff');
  AddPreference('go_default_numeric','link');
  AddPreference('keyfile',"[datadir]sessionkeys.txt",'key');
  AddPreference('launch_exec','');
  AddPreference('mail_save','1','msave');
  AddPreference('mailfile',"[datadir]maildata.txt",'mfile');
  AddPreference('mkdir_mode',0777);
  AddPreference('msg_blankrow','1','mbr');
  AddPreference('msg_dateformat','M d H:i','mdf');
  AddPreference('msg_show_date','0','md');
  AddPreference('noticefile',"[datadir]noticedata.txt",'nfile');
  AddPreference('notices_blankrow','1','nbr');
  AddPreference('notices_dateformat','M d H:i','ndf');
  AddPreference('notices_save','1','nsave');
  AddPreference('notices_show_date','0','nd');
  AddPreference('notices_show_id','0','nid');
  AddPreference('online_idle','1','idle');
  AddPreference('opics_filename','[pid].jpg','of');
  AddPreference('pic_dateformat','M d Y','pdf');
  AddPreference('pic_retry_count','10','pr');
  AddPreference('pic_retry_delay','2','prd');
  AddPreference('pic_show_albumid','0','paid');
  AddPreference('pic_show_date','0','pd');
  AddPreference('pic_show_links','0','plink');
  AddPreference('pic_show_src','0','psrc');
  AddPreference('pic_size','1','psize');
  AddPreference('pic_skip_exists','1','pskip');
  AddPreference('postfile',"[datadir]postdata.txt",'pfile');
  AddPreference('sharepost','0','share');
  AddPreference('ppic_size','1','ppsize');
  AddPreference('ppics_filename','[tid].jpg','pf');
  AddPreference('prefix_filter','#');
  AddPreference('prefix_friendlist','_');
  AddPreference('prefix_group','~');
  AddPreference('prefix_page','+');
  AddPreference('prefix_tag','@');
  AddPreference('prefix_username','!');
  AddPreference('prefs','');
  AddPreference('print_blanks','0','bl');
  AddPreference('print_clean','1','clean');
  AddPreference('print_col_padding','2','pad');
  AddPreference('print_csv','0','csv');
  AddPreference('print_header','1','hdr');
  AddPreference('print_linefeed_subst',' ');
  AddPreference('print_wrap','1','wrap');
  AddPreference('print_wrap_cut','1','cut');
  AddPreference('print_wrap_env_var','COLUMNS');
  AddPreference('print_wrap_min_width','20');
  AddPreference('print_wrap_width','80','col');
  AddPreference('quiet','0','q');
  AddPreference('restatus_comment_new','1');
  AddPreference('show_id','0','id');
  AddPreference('status_dateformat','D M d H:i','stdf');
  AddPreference('status_show_date','0','std');
  AddPreference('status_tag','1','tag');
  AddPreference('status_tag_order','friends:username:0,friends:name:0,pages:username:0,pages:name:0,friends:name:1,pages:name:1,groups:name:0,groups:name:1');
  AddPreference('status_tag_syntax','/@(\S+)/');
  AddPreference('stream_blankrow','1','sbr');
  AddPreference('stream_dateformat','D H:i','sdf');
  AddPreference('stream_new_from','created_time');
  AddPreference('stream_save','1','ssave');
  AddPreference('stream_show_appdata','0','sapp');
  AddPreference('stream_show_attachments','0','satt');
  AddPreference('stream_show_comments','1','scom');
  AddPreference('stream_show_date','0','sd');
  AddPreference('stream_show_likes','1','slikes');
  AddPreference('stream_show_postid','0','sid');
  AddPreference('trace','0','t');
  AddPreference('update_branch','master');

  // Parameter Defaults
  AddPreference('default_addalbum_title','');
  AddPreference('default_addalbum_description','');
  AddPreference('default_addalbum_location','');
  AddPreference('default_addalbum_privacy','everyone');
  AddPreference('default_addperm','create_event,friends_about_me,friends_activities,friends_birthday,friends_checkins,friends_education_history,friends_events,friends_groups,friends_hometown,friends_interests,friends_likes,friends_location,friends_notes,friends_online_presence,friends_photo_video_tags,friends_photos,friends_relationship_details,friends_relationships,friends_religion_politics,friends_status,friends_videos,friends_website,friends_work_history,manage_friendlists,manage_pages,offline_access,publish_checkins,publish_stream,read_friendlists,read_mailbox,read_requests,read_stream,rsvp_event,user_about_me,user_activities,user_birthday,user_checkins,user_education_history,user_events,user_groups,user_hometown,user_interests,user_likes,user_location,user_notes,user_online_presence,user_photo_video_tags,user_photos,user_relationship_details,user_relationships,user_religion_politics,user_status,user_videos,user_website,user_work_history');
  AddPreference('default_addpic_filename','');
  AddPreference('default_addpic_albumid',null);
  AddPreference('default_addpic_caption','');
  AddPreference('default_addpicd_dirname','');
  AddPreference('default_addpicd_albumid',null);
  AddPreference('default_albums_flist','=ME');
  AddPreference('default_allinfo_flist','=ME');
  AddPreference('default_apics_albumid','');
  AddPreference('default_apics_savedir',false);
  AddPreference('default_comment_text','');
  AddPreference('default_display_text','FBCMD: The Command Line Interface for Facebook');
  AddPreference('default_feed1_text','');
  AddPreference('default_feed2_title','');
  AddPreference('default_feed2_body','');
  AddPreference('default_feed2_imgsrc','');
  AddPreference('default_feed2_imglink','');
  AddPreference('default_feedlink_link','');
  AddPreference('default_feedlink_text','');
  AddPreference('default_feednote_title','');
  AddPreference('default_feednote_body','');
  AddPreference('default_fevents_flist','=ME');
  AddPreference('default_fgroups_flist','=ME');
  AddPreference('default_finfo_fields','birthday_date');
  AddPreference('default_finfo_flist','=ALL');
  AddPreference('default_flast_flist','=ME');
  AddPreference('default_flast_count','10');
  AddPreference('default_fonline_flist','=ALL');
  AddPreference('default_fpics_flist','=ME');
  AddPreference('default_fpics_savedir',false);
  AddPreference('default_friends_flist','=ALL');
  AddPreference('default_fstatus_flist','=ALL');
  AddPreference('default_fstream_flist','=ALL');
  AddPreference('default_fstream_count','10');
  AddPreference('default_inbox_count','10');
  AddPreference('default_loaddisp_filename','');
  AddPreference('default_loadinfo_filename','');
  AddPreference('default_loadnote_title','');
  AddPreference('default_loadnote_filename','');
  AddPreference('default_mutual_flist','=ALL');
  AddPreference('default_mywall_count','10');
  AddPreference('default_notices_type','');
  AddPreference('default_nsend_flist','=ME');
  AddPreference('default_nsend_message','');
  AddPreference('default_opics_flist','=ME');
  AddPreference('default_opics_savedir',false);
  AddPreference('default_post_message','');
  AddPreference('default_post_name',null);
  AddPreference('default_post_link',null);
  AddPreference('default_post_caption',null);
  AddPreference('default_post_description',null);
  AddPreference('default_postimg_imgsrc','');
  AddPreference('default_postimg_imglink','0');
  AddPreference('default_postmp3_mp3src','');
  AddPreference('default_postmp3_mp3title','');
  AddPreference('default_postmp3_mp3artist','');
  AddPreference('default_postmp3_mp3album','');
  AddPreference('default_postflash_swfsrc',false);
  AddPreference('default_postflash_imgsrc',false);
  AddPreference('default_ppics_flist','=ALL');
  AddPreference('default_ppics_savedir',false);
  AddPreference('default_ppost_id',null);
  AddPreference('default_recent_flist','=ALL');
  AddPreference('default_recent_count','10');
  AddPreference('default_savedisp_filename','');
  AddPreference('default_saveinfo_filename','');
  AddPreference('default_sentmail_count','10');
  AddPreference('default_showpref_defaults','0');
  AddPreference('default_stream_filter','1');
  AddPreference('default_stream_count','10');
  AddPreference('default_tagpic_pid','');
  AddPreference('default_tagpic_target','=ME');
  AddPreference('default_tagpic_x','50');
  AddPreference('default_tagpic_y','50');
  AddPreference('default_updates_count','10');
  AddPreference('default_wallpost_flist','=ME');


  // STEP TWO: Load preferences from prefs.php in the base directory

  if (file_exists("{$fbcmdBaseDir}prefs.php")) {
    include("{$fbcmdBaseDir}prefs.php");
  }

  // STEP THREE: Read switches set from the command line
  // This also sets $fbcmdCommand & $fbcmdParams

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

  if ($fbcmdCommand == 'SAVEPREF') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,"{$fbcmdBaseDir}prefs.php");
    SavePrefs($fbcmdParams[1]);
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  $GLOBALS['facebook_config']['debug'] = $fbcmdPrefs['facebook_debug'];

////////////////////////////////////////////////////////////////////////////////

  $fbcmdCommandList = array();

  AddCommand('ADDALBUM',  'title [description] [location] [privacy]~Create a new photo album');
  AddCommand('ADDPERM',   '[permissions_list]~(Launch a website to) grant FBCMD extended permissions.');
  AddCommand('ADDPIC',    'filename [album_id|latest] [caption]~Upload (add) a photo to an album');
  AddCommand('ADDPICD',   'dirname [album_id|latest]~Upload (add) all *.jpg files in a directory to an album');
  AddCommand('ALBUMS',    '[flist]~List all photo albums for friend(s)');
  AddCommand('ALLINFO',   'flist~List all available profile information for friend(s)');
  AddCommand('APICS',     'album_id [savedir]~List [and optionally save] all photos from an album');
  AddCommand('AUTH',      'authcode~Sets your facebook authorization code for offline access');
  AddCommand('COMMENT',   'post_id text~Add a comment to a story that appears in the stream');
  AddCommand('DELPOST',   'post_id~Deletes a post from your stream');
  AddCommand('DISPLAY',   'fbml~Sets the content of your FBCMD profile box');
  AddCommand('EVENTS',    '[time]~Display your events');
  AddCommand('FEED1',     'title~Add a one-line story to your news feed');
  AddCommand('FEED2',     'title body [img_src img_link]~Add a short story to your news feed with optional picture');
  AddCommand('FEEDLINK',  '[link] text~Share a link in your news feed');
  AddCommand('FEEDNOTE',  'title body~Share a note in your news feed');
  AddCommand('FEVENTS',   'flist [time]~List events for friend(s)');
  AddCommand('FGROUPS',   '[flist]~List groups that friend(s) are members of');
  AddCommand('FINBOX',    '[flist]~Display mail messages from specific friend(s)');
  AddCommand('FINFO',     'fields [flist]~List information fields for friend(s) (see UFIELDS)');
  AddCommand('FLAST',     'flist [count]~See the last [count] status updates of friend(s)');
  AddCommand('FONLINE',   '[flist]~List any friends who are currently online');
  AddCommand('FPICS',     'flist [savedir]~List [and optionally save] all photos where friend(s) are tagged');
  AddCommand('FQL',       'statement [flist]~Perform a custom FQL Query');
  AddCommand('FRIENDS',   '[flist]~Generate a list of all your friends');
  AddCommand('FSTATUS',   '[flist]~List current status of friend(s)');
  AddCommand('FSTREAM',   '[flist] [count|new]~Show stream stories for friend(s)');
  AddCommand('FULLPOST',  'post_id~Displays a stream post with all of the comments');
  AddCommand('GO',        'destination [id]~Launches a web browser for the given destination');
  AddCommand('HELP',      '[command|preference]~Display this help message, or launch web browser for [command]');
  AddCommand('HOME',      '[webpage]~Launch a web browser to visit the FBCMD home page');
  AddCommand('INBOX',     '[count|unread|new]~Display the latest messages from the inbox');
  AddCommand('LIKE',      'post_ids~Like a story that appears in the stream');
  AddCommand('LIMITS',    '<no parameters>~Display current limits on FBCMD usage');
  AddCommand('LOADDISP',  'fbml_filename~Same as DISPLAY but loads the contents from a file');
  AddCommand('LOADINFO',  'info_filename~Sets the content of the FBCMD section on your Info Tab');
  AddCommand('LOADNOTE',  'title filename~Same as FEEDNOTE but loads the contents from a file');
  AddCommand('MSG',       'message_id~Displays a full message thread (e.g.: after an INBOX)');
  AddCommand('MUTUAL',    'flist~List friend(s) in common with other friend(s)');
  AddCommand('MYWALL',    '[count|new]~Show the posts from other users to your wall');
  AddCommand('NOTICES',   '[unread|markread]~See notifications from facebook, applications & users');
  AddCommand('NOTIFY',    '<no parameters>~See (simple) notifications such as # of unread messages');
  AddCommand('NSEND',     'flist message~Send a notification message to friend(s)');
  AddCommand('OPICS',     'flist [savedir]~List [and optionally save] all photos owned by friend(s)');
  AddCommand('PINBOX',    '[count|unread|new]~Display the inbox (latest updates) from pages you are a fan of');
  AddCommand('PPOST',     'page_id [POST parameters]~Post a message to a your page (for page administrators)');
  AddCommand('POST',      'message <[name] [link] [caption] [description]>~IMG message img_src [img_link] <[n] [l] [c] [d]>~MP3 message mp3_src [title] [artist] [album] <[n] [l] [c] [d]>~FLASH swf_src img_src <[n] [l] [c] [d]>~Post (share) a story (or media) in your stream');
  AddCommand('PPICS',     '[flist] [savedir]~List [and optionally save] all profile photos of friend(s)');
  AddCommand('RECENT',    '[flist] [count]~Shows the [count] most recent friend status updates');
  AddCommand('RESET',     '<no parameters>~Reset any authorization codes set by AUTH');
  AddCommand('RESTATUS',  'message~Replace your status (deletes your status and adds a new status)');
  AddCommand('RSVP',      'event_id yes|no|maybe~RSVP to an Event from the EVENTS command');
  AddCommand('SAVEDISP',  'fbml_filename~Saves the content of your FBCMD profile box to a file');
  AddCommand('SAVEINFO',  'info_filename~Saves the content of the FBCMD section on your Info Tab to a file');
  AddCommand('SAVEPREF',  '[filename]~Save your current preferences / switch settings to a file');
  AddCommand('SENTMAIL',  '[count|unread|new]~Display the latest messages from the sent mail folder');
  AddCommand('SFILTERS',  '<no parameters>~Display available stream filters for the STREAM command');
  AddCommand('SHOWPREF',  '[0|1]~Show your current preferences (and optionally defaults too)');
  AddCommand('SHOWPERM',  '<no parameters>~List all possible permissions and show if granted to FBCMD');
  AddCommand('STATUS',    '[message]~Set your status (or display current status if no parameter)');
  AddCommand('STREAM',    '[filter_rank|filter_key|#filter_name] [count|new]~Show stream stories (with optional filter -- see SFILTERS)');
  AddCommand('TAGPIC',    'pic_id target [x y]~Tag a photo');
  AddCommand('UFIELDS',   '<no parameters>~List current user table fields (for use with FINFO)');
  AddCommand('UPDATE',    '[branch] [dir] [trace] [ignore_err]~Update FBCMD to the latest version');
  AddCommand('USAGE',     '(same as HELP)');
  AddCommand('VERSION',   '[branch]~Check for the latest version of FBCMD available');
  AddCommand('WALLPOST',  'flist [POST parameters]~Post a message on the wall of friend(s)');
  AddCommand('WHOAMI',    '<no parameters>~Display the currently authorized user');

  if (isset($fbcmd_include_newCommands)) {
    foreach ($fbcmd_include_newCommands as $c) {
      AddCommand($c[0],$c[1]);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if (in_array($fbcmdCommand,array('DFILE','FEED','FEED3','FSTATUSID','FLSTATUS','PICS'))) {
    FbcmdFatalError("{$fbcmdCommand} has been deprecated:\n  visit http://fbcmd.dtompkins.com/commands/" . strtolower($fbcmdCommand) . " for more information");
  }

////////////////////////////////////////////////////////////////////////////////

  if (!in_array($fbcmdCommand,$fbcmdCommandList)&&($fbcmdCommand != '')) {
    FbcmdFatalError("Unknown Command: [{$fbcmdCommand}] try fbcmd HELP");
  }

////////////////////////////////////////////////////////////////////////////////

  if (($fbcmdCommand == 'HELP')||($fbcmdCommand == 'USAGE')) {
    ValidateParamCount(0,1);
    if (ParamCount() == 0) {
      ShowUsage();
    }
    if (in_array(strtoupper($fbcmdParams[1]),$fbcmdCommandList)) {
      LaunchBrowser('http://fbcmd.dtompkins.com/commands/' . strtolower($fbcmdParams[1]));
      return;
    }
    if (isset($fbcmdPrefs[$fbcmdParams[1]])) {
      LaunchBrowser('http://fbcmd.dtompkins.com/preferences/' . strtolower($fbcmdParams[1]));
      return;
    }
    FbcmdWarning("HELP: did not recognize [{$fbcmdParams[1]}]");
    ShowUsage();
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'UPDATE') {
    ValidateParamCount(0,4);
    $updatePhp = CleanPath(realpath(dirname($argv[0]))) . 'fbcmd_update.php';
    if (!file_exists($updatePhp)) {
      $updatePhpAlt = CleanPath(realpath($fbcmdPrefs['install_dir'])) . 'fbcmd_update.php';
      if (file_exists($updatePhpAlt)) {
        $updatePhp = $updatePhpAlt;
      } else {
        FbcmdFatalError("Could not locate [{$updatePhp}]");
      }
    }
    $execCmd = "php \"$updatePhp\"";
    if (ParamCount() >= 1) {
      $execCmd .= " \"{$fbcmdParams[1]}\"";
    }
    if (ParamCount() >= 2) {
      $execCmd .= " \"{$fbcmdParams[2]}\"";
    }
    if (ParamCount() >= 3) {
      $execCmd .= " {$fbcmdParams[3]}";
    }
    if (ParamCount() >= 4) {
      $execCmd .= " {$fbcmdParams[4]}";
    }
    passthru($execCmd);
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'HOME') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,'');
    LaunchBrowser('http://fbcmd.dtompkins.com/' . strtolower($fbcmdParams[1]));
  }

////////////////////////////////////////////////////////////////////////////////

  $urlAuth = "http://www.facebook.com/code_gen.php?v=1.0&api_key={$fbcmdPrefs['appkey']}";
  $urlAccess = "https://www.facebook.com/dialog/oauth?client_id={$fbcmdPrefs['appkey']}&redirect_uri=http://www.facebook.com/connect/login_success.html";

  AddGoDestination('access',      'Allow fbcmd to (initially) access your account',$urlAccess);
  AddGoDestination('album',       '#An album from the ALBUM command');
  AddGoDestination('app',         'The fbcmd page on facebook','http://facebook.com/fbcmd');
  AddGoDestination('auth',        'Authorize fbcmd for permanent access',$urlAuth);
  AddGoDestination('contribute',  'The fbcmd contact page','http://fbcmd.dtompkins.com/contribute');
  AddGoDestination('editapps',    'The facebook edit applications page','http://www.facebook.com/editapps.php');
  AddGoDestination('event',       '#An event from the EVENT command');
  AddGoDestination('faq',         'The fbcmd FAQ','http://fbcmd.dtompkins.com/faq');
  AddGoDestination('friend.name', 'The facebook page of your friend...uses status tagging','http://fbcmd.dtompkins.com/faq');
  AddGoDestination('github',      'The source repository at github','http://github.com/dtompkins/fbcmd');
  AddGoDestination('group',       'The fbcmd discussion group','http://groups.google.com/group/fbcmd');
  AddGoDestination('help',        'the fbcmd help page','http://fbcmd.dtompkins.com/help');
  AddGoDestination('home',        'The fbcmd home page','http://fbcmd.dtompkins.com');
  AddGoDestination('inbox',       'Your facebook inbox','http://www.facebook.com/inbox');
  AddGoDestination('install',     'The fbcmd installation page','http://fbcmd.dtompkins.com/installation');
  AddGoDestination('link',        '#A link from a post from the STREAM command');
  AddGoDestination('msg',         '#A mail thread from he INBOX command');
  AddGoDestination('notice',      '#A notice from the NOTICES command');
  AddGoDestination('post',        '#A post from the STREAM command');
  AddGoDestination('stream',      'Your facebook home page','http://www.facebook.com/home.php');
  AddGoDestination('update',      'The fbcmd update page','http://fbcmd.dtompkins.com/update');
  AddGoDestination('wall',        'Your facebook profile');
  AddGoDestination('wiki',        'The fbcmd wiki','http://fbcmd.dtompkins.com');
  AddGoDestination('a',           '#shortcut for [album]');
  AddGoDestination('e',           '#shortcut for [event]');
  AddGoDestination('m',           '#shortcut for [msg]');
  AddGoDestination('n',           '#shortcut for [notice]');
  AddGoDestination('p',           '#shortcut for [post]');
  AddGoDestination('l',           '#shortcut for [link]');


  if ($fbcmdCommand == 'GO') {
    if (ParamCount() == 0) {
      print "\nGO Destinations:\n\n";
      foreach ($goDestinations as $key) {
        $desc = $goDestinationsHelp[$key];
        if (substr($desc,0,1) == "#") {
          print str_pad("  go {$key} id",19,' ') . substr($desc,1) . "\n";
        } else {
          print str_pad("  go {$key}",19,' ') . $desc . "\n";
        }
      }
      print "\n";
      return;
    } else {
      if (isset($goDestinationsUrl[strtolower($fbcmdParams[1])])) {
        LaunchBrowser($goDestinationsUrl[strtolower($fbcmdParams[1])]);
        return;
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  require_once('facebook/facebook.php');
  require_once('facebook/facebook_desktop.php');

////////////////////////////////////////////////////////////////////////////////

  $fbcmdKeyFileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs['keyfile']);
  
  if ($fbcmdCommand == 'RESET') {
    ValidateParamCount(0);
    VerifyOutputDir($fbcmdKeyFileName);
    if (@file_put_contents($fbcmdKeyFileName,"EMPTY\nEMPTY\n# only the first two lines of this file are read\n# use fbcmd RESET to replace this file\n") == false) {
      FbcmdFatalError("Could not generate keyfile {$fbcmdKeyFileName}");
    }
    if (!$fbcmdPrefs['quiet']) {
      print "keyfile {$fbcmdKeyFileName} has been RESET\n";
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
    VerifyOutputDir($fbcmdKeyFileName);
    if (@file_put_contents ($fbcmdKeyFileName,"{$fbcmdUserSessionKey}\n{$fbcmdUserSecretKey}\n# only the first two lines of this file are read\n# use fbcmd RESET to replace this file\n") == false) {
      FbcmdFatalError("Could not generate keyfile {$fbcmdKeyFileName}");
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
      print "\nfbcmd [v$fbcmdVersion] AUTH Code accepted.\nWelcome to FBCMD, {$fbReturn[0]['name']}!\n\n";
      print "most FBCMD commands require additional permissions.\n";
      print "to grant default permissions, execute: fbcmd addperm\n";
    }
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  if (!file_exists($fbcmdKeyFileName)) {
    print "\n";
    print "Welcome to fbcmd! [version $fbcmdVersion]\n\n";
    //print "It appears to be the first time you are running the application\n";
    //print "as fbcmd could not locate your keyfile: [{$fbcmdKeyFileName}]\n\n";
    ShowAuth();
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  $fbcmdKeyFile = file($fbcmdKeyFileName,FILE_IGNORE_NEW_LINES);
  if (count($fbcmdKeyFile) < 2) {
    FbcmdFatalError("Invalid keyfile {$fbcmdKeyFileName}");
  }
  $fbcmdUserSessionKey = $fbcmdKeyFile[0];
  $fbcmdUserSecretKey = $fbcmdKeyFile[1];

  if (strncmp($fbcmdUserSessionKey,'EMPTY',5) == 0) {
    ShowAuth();
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

  if ($fbcmdCommand == '') {
    ShowUsage();
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
  
  $allPermissions = 'ads_management,create_event,email,friends_about_me,friends_activities,friends_birthday,friends_checkins,friends_education_history,friends_events,friends_groups,friends_hometown,friends_interests,friends_likes,friends_location,friends_notes,friends_online_presence,friends_photo_video_tags,friends_photos,friends_relationship_details,friends_relationships,friends_religion_politics,friends_status,friends_videos,friends_website,friends_work_history,manage_friendlists,manage_pages,offline_access,publish_checkins,publish_stream,read_friendlists,read_insights,read_mailbox,read_requests,read_stream,rsvp_event,sms,user_about_me,user_activities,user_birthday,user_checkins,user_education_history,user_events,user_groups,user_hometown,user_interests,user_likes,user_location,user_notes,user_online_presence,user_photo_video_tags,user_photos,user_relationship_details,user_relationships,user_religion_politics,user_status,user_videos,user_website,user_work_history,xmpp_login';

////////////////////////////////////////////////////////////////////////////////

  PrintStart();

////////////////////////////////////////////////////////////////////////////////

  if (isset($fbcmd_include_newCommands)) {
    foreach ($fbcmd_include_newCommands as $c) {
      if ($fbcmdCommand == $c[0]) {
        return;
      }
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
      $fbReturn = $fbObject->api_client->photos_createAlbum($fbcmdParams[1],$fbcmdParams[2],$fbcmdParams[3],$fbcmdParams[4]);
      TraceReturn($fbReturn);
    } catch (Exception $e) {
      FbcmdException($e);
    }
    PrintHeaderQuiet('AID',PrintIfPref('pic_show_links','LINK'));
    PrintRowQuiet($fbReturn['aid'],PrintIfPref('pic_show_links',$fbReturn['link']));
  }
  
////////////////////////////////////////////////////////////////////////////////  

  if ($fbcmdCommand == 'ADDPERM') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_addperm']);
    if (strtoupper($fbcmdParams[1]) == 'ALL') {
      $fbcmdParams[1] = $allPermissions;
    }
    $url = "{$urlAccess}&scope={$fbcmdParams[1]}";
    print "launching: $url\n";
    LaunchBrowser($url);
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
    $fbcmdParams[2] = GetAlbumId($fbcmdParams[2]);
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
    $fbcmdParams[2] = GetAlbumId($fbcmdParams[2]);
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
      PrintHeader(PrintIfPref('album_save','[#]'),PrintIfPref('show_id','OWNER_ID'),'OWNER_NAME',PrintIfPref('pic_show_albumid','AID'),PrintIfPref('pic_show_date','CREATED'),'NAME','SIZE',PrintIfPref('pic_show_links','LINK'));
      $albumNum = 0;
      foreach ($fbReturn as $a) {
        $albumNum++;
        PrintRow(PrintIfPref('album_save','[' . $albumNum. ']'),PrintIfPref('show_id',$a['owner']),ProfileName($a['owner']),PrintIfPref('pic_show_albumid',$a['aid']),PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$a['created'])),$a['name'],$a['size'],PrintIfPref('pic_show_links',$a['link']));
      }
      SaveAlbumData($fbReturn);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  $allUserInfoFields = "about_me,activities,affiliations,allowed_restrictions,birthday,birthday_date,books,current_location,education_history,email_hashes,family,first_name,has_added_app,hometown_location,hs_info,interests,is_app_user,is_blocked,last_name,locale,meeting_for,meeting_sex,movies,music,name,notes_count,online_presence,pic,pic_big,pic_big_with_logo,pic_small,pic_small_with_logo,pic_square,pic_square_with_logo,pic_with_logo,political,profile_blurb,profile_update_time,profile_url,proxied_email,quotes,relationship_status,religion,sex,significant_other_id,status,timezone,tv,uid,username,verified,wall_count,website,work_history";

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

  if ($fbcmdCommand == 'SHOWPERM') {
    ValidateParamCount(0);
    $fql = "SELECT {$allPermissions} from permissions where uid={$fbUser}";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader('PERMISSION','GRANTED?');
      $permList = explode(',',$allPermissions);
      foreach ($permList as $perm) {
        PrintRow($perm,$fbReturn[0][$perm]);
      }
    }    
  }

  ////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'APICS') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_apics_albumid']);
    SetDefaultParam(2,$fbcmdPrefs['default_apics_savedir']);
    $fbcmdParams[1] = GetAlbumId($fbcmdParams[1]);
    $fql = "SELECT pid,aid,owner,src_small,src_big,src,link,caption,created FROM photo WHERE aid IN ({$fbcmdParams[1]})";
    try {
      $fbReturn = $fbObject->api_client->fql_query($fql);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    if (!empty($fbReturn)) {
      PrintHeader(PrintIfPref('pic_show_albumid','AID'),'PID',PrintIfPref('pic_show_date','CREATED'),'CAPTION',PrintIfPref('pic_show_links','LINK'),PrintIfPref('pic_show_src','SRC'));
      foreach ($fbReturn as $pic) {
        PrintRow(PrintIfPref('pic_show_albumid',$pic['aid']),$pic['pid'],PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$pic['created'])),$pic['caption'],PrintIfPref('pic_show_links',$pic['link']),PrintIfPref('pic_show_src',PhotoSrc($pic)));
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
    $fqlEventMember = "SELECT eid,rsvp_status FROM event_member WHERE uid={$fbUser} AND rsvp_status IN ({$eventAttend})";
    $keyEventMember = 'eid';
    $fqlEvent = "SELECT eid,name,start_time FROM event WHERE eid IN (SELECT eid FROM #fqlEventMember) AND start_time > {$fbcmdParams[1]} ORDER BY start_time";
    MultiFQL(array('EventMember','Event'));
    if (!empty($dataEvent)) {
      PrintHeader(PrintIfPref('event_save','[#]'),'START_TIME','RSVP','EVENT');
      $eventNum = 0;
      foreach ($dataEvent as $event) {
        $eventNum++;
        PrintRow(PrintIfPref('event_save','[' . $eventNum . ']'),date($fbcmdPrefs['event_dateformat'],$event['start_time']),$indexEventMember[$event['eid']]['rsvp_status'],$event['name']);
      }
      SaveEventData($dataEvent);
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

  if ($fbcmdCommand == 'FINBOX') {
    ValidateParamCount(1);
    GetFlistIds($fbcmdParams[1],true);
    $matchInRecipients = "('" . implode("' in recipients OR '",$flistMatchArray) . "' in recipients)";
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_inbox_count']);
    $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE (folder_id = 0 OR folder_id = 1 OR folder_id = 4) and $matchInRecipients";
    $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
    $keyMessageNames = 'id';
    MultiFQL(array('Thread','MessageNames'));
    if (!empty($dataThread)) {
      PrintFolderHeader();
      $threadNum = 0;
      foreach ($dataThread as $t) {
        PrintFolderObject(++$threadNum,$t);
      }
      SaveMailData($dataThread);
    }
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
        $fbcmdPrefs['uid'] = 1;
        $fbcmdPrefs['print_blanks'] = 1;
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
      if (($indexFriendBaseInfo[$id]['online_presence'] == 'active')||(($indexFriendBaseInfo[$id]['online_presence'] == 'idle')&&($fbcmdPrefs['online_idle']))) {
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
    if (strtoupper($fbcmdParams[2]) == 'NEW') {
      CheckStreamTimeStamp();
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id IN ({$flistMatchIdString}) AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    } else {
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id IN ({$flistMatchIdString}) LIMIT {$fbcmdParams[2]}";
    }
    $queries = array('Stream');
    if ($fbcmdPrefs['stream_show_comments']) {
      $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id, comments.comment_list.fromid FROM #fqlStream)';
      $keyStreamNames = 'id';
      $queries[] = 'StreamNames';
    }
    MultiFQL($queries);
    if (!empty($dataStream)) {
      PrintPostHeader();
      $postNum = 0;
      foreach ($dataStream as $a) {
        PrintPostObject(++$postNum,$a);
      }
      SavePostData($dataStream);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FULLPOST') {
    ValidateParamCount(1);
    $curPostId = GetPostId($fbcmdParams[1],true);
    if ($curPostId) {
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE post_id='{$curPostId}'";
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

  if ($fbcmdCommand == 'MYWALL') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_mywall_count']);
    if (strtoupper($fbcmdParams[1]) == 'NEW') {
      CheckStreamTimeStamp();
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id={$fbUser} AND target_id={$fbUser} AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    } else {
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id={$fbUser} AND target_id={$fbUser} LIMIT {$fbcmdParams[1]}";
    }
    $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id FROM #fqlStream)';
    $keyStreamNames = 'id';
    MultiFQL(array('Stream','StreamNames'));
    if (!empty($dataStream)) {
      PrintHeader(PrintIfPref('stream_save','[#]'),PrintIfPref('stream_show_postid','POST_ID'),PrintIfPref('show_id','UID'),'NAME',PrintIfPref('stream_show_date','DATE'),'MESSAGE');
      if ($fbcmdPrefs['stream_blankrow']) {
        PrintRow('');
      }
      $postNum = 0;
      foreach ($dataStream as $a) {
        $postNum++;
        PrintRow(PrintIfPref('stream_save','[' . $postNum . ']'),PrintIfPref('stream_show_postid',$a['post_id']),PrintIfPref('show_id',$a['actor_id']),ProfileName($a['actor_id']),PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$a['created_time'])),$a['message']);
        if ($fbcmdPrefs['stream_blankrow']) {
          PrintRow('');
        }
      }
      SavePostData($dataStream);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'GO') {
    global $goDestinations;
    $hasLaunched = false;
    ValidateParamCount(1,2);
    if ((ParamCount() == 1) && (is_numeric($fbcmdParams[1]))) {
      $fbcmdParams[2] = $fbcmdParams[1];
      $fbcmdParams[1] = $fbcmdPrefs['go_default_numeric'];
    }
    $go = $fbcmdParams[1];
    if (ParamCount() == 1) {
      if (in_array(strtolower($go),$goDestinations)) {
        $go = strtoupper($go);
        if ($go == 'WALL') {
          LaunchBrowser("http://www.facebook.com/profile.php?id={$fbUser}");
        }
      } else {
        MultiFQL(array('FriendId','FriendBaseInfo','PageId','PageNames','GroupNames'));
        $tagList = MatchTag($fbcmdParams[1]);
        if ($tagList) {
          LaunchBrowser("http://www.facebook.com/profile.php?id={$tagList[0][0]}");
        }
      }
    } else {
      $go = strtoupper($go);
      if ($fbcmdParams[2]) {
        if (($go == 'ALBUM')||($go == 'A')) {
          $lastNoticeData = LoadDataFile('album_save','albumfile');
          if (isset($lastNoticeData['link'][$fbcmdParams[2]])) {
            LaunchBrowser($lastNoticeData['link'][$fbcmdParams[2]]);
          }
        }
        if (($go == 'EVENT')||($go == 'E')) {
          $lastEventData = LoadDataFile('event_save','eventfile');
          if (isset($lastEventData['ids'][$fbcmdParams[2]])) {
            LaunchBrowser('http://www.facebook.com/event.php?eid=' . $lastEventData['ids'][$fbcmdParams[2]]);
          }
        }
        if (($go == 'LINK')||($go == 'L')) {
          $lastPostData = LoadDataFile('stream_save','postfile');
          if (isset($lastPostData['link'][$fbcmdParams[2]])) {
            LaunchBrowser($lastPostData['link'][$fbcmdParams[2]]);
          } else {
            $go = 'POST';
          }
        }
        if (($go == 'NOTICE')||($go == 'N')) {
          $lastNoticeData = LoadDataFile('notices_save','noticefile');
          if (isset($lastNoticeData['href'][$fbcmdParams[2]])) {
            LaunchBrowser($lastNoticeData['href'][$fbcmdParams[2]]);
          }
        }
        if (($go == 'MSG')||($go == 'M')) {
          $curThreadId = GetThreadId($fbcmdParams[2]);
          if ($curThreadId) {
            LaunchBrowser("http://www.facebook.com/inbox/?folder=[fb]messages&tid={$curThreadId}");
          }
        }
        if (($go == 'POST')||($go == 'P')) {
          $lastPostData = LoadDataFile('stream_save','postfile');
          if (isset($lastPostData['url'][$fbcmdParams[2]])) {
            LaunchBrowser($lastPostData['url'][$fbcmdParams[2]]);
          }
        }
      }
    }
    if (!$hasLaunched) {
      FbcmdWarning("Problem with your GO requeset");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'INBOX') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_inbox_count']);
    if (strtoupper($fbcmdParams[1]) == 'UNREAD') {
      $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 0 AND unread > 0";
    } else {
      if (strtoupper($fbcmdParams[1]) == 'NEW') {
        CheckMailTimeStamp();
        $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 0 AND updated_time > {$lastMailData['timestamp']}";
      } else {
        $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 0 LIMIT {$fbcmdParams[1]}";
      }
    }
    $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
    $keyMessageNames = 'id';
    MultiFQL(array('Thread','MessageNames'));
    if (!empty($dataThread)) {
      PrintFolderHeader();
      $threadNum = 0;
      foreach ($dataThread as $t) {
        PrintFolderObject(++$threadNum,$t);
      }
      SaveMailData($dataThread);
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

  if ($fbcmdCommand == 'MSG') {
    ValidateParamCount(1);
    $curThreadId = GetThreadId($fbcmdParams[1]);
    if ($curThreadId) {
      $fqlThread = "SELECT subject,recipients,message_count,snippet,unread FROM thread WHERE thread_id = {$curThreadId}";
      $fqlMessage = "SELECT message_id,thread_id,author_id,body,created_time,attachment,viewer_id FROM message WHERE thread_id = {$curThreadId}";
      $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
      $keyMessageNames = 'id';
      MultiFQL(array('Thread','Message','MessageNames'));
      if (!empty($dataMessage)) {
        PrintHeader(PrintIfPref('show_id','USER_ID'),'FROM',PrintIfPref('msg_show_date','DATE'),'MESSAGE');
        if ($fbcmdPrefs['msg_blankrow']) {
          PrintRow('');
        }
        if ($dataThread[0]['subject'] != '') {
          PrintRow(PrintIfPref('show_id',''),'Subject',PrintIfPref('msg_show_date',''),$dataThread[0]['subject']);
          if ($fbcmdPrefs['msg_blankrow']) {
            PrintRow('');
          }
        }
        foreach ($dataMessage as $m) {
          if ($m['created_time'] == '') { // note: the created_time field appears to be flakey
            $displayDate = '';
          } else {
            $displayDate = date($fbcmdPrefs['msg_dateformat'],$m['created_time']);
          }
          PrintRow(PrintIfPref('show_id',$m['author_id']),ProfileName($m['author_id']),PrintIfPref('msg_show_date',$displayDate),$m['body']);
          if ($fbcmdPrefs['msg_blankrow']) {
            PrintRow('');
          }
        }
      }
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

  if ($fbcmdCommand == 'NOTICES') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_notices_type']);
    if ((strtoupper($fbcmdParams[1]) == 'UNREAD')||(strtoupper($fbcmdParams[1]) == 'MARKREAD')) {
      $fqlNotification = "SELECT notification_id,sender_id,title_html,title_text,body_html,body_text,href,app_id,created_time,is_unread FROM notification WHERE recipient_id={$fbUser} AND is_hidden = 0 AND is_unread = 1";
    } else {
      $fqlNotification = "SELECT notification_id,sender_id,title_html,title_text,body_html,body_text,href,app_id,created_time,is_unread FROM notification WHERE recipient_id={$fbUser} AND is_hidden = 0";
    }
    $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT sender_id FROM #fqlNotification)';
    $keyMessageNames = 'id';
    $fqlApplicationNames = 'SELECT app_id,display_name FROM application WHERE app_id IN (SELECT app_id FROM #fqlNotification)';
    $keyApplicationNames = 'app_id';
    MultiFQL(array('Notification','MessageNames','ApplicationNames'));

    if (!empty($dataNotification)) {
      PrintNotificationHeader();
      $threadNum = 0;
      foreach ($dataNotification as $n) {
        PrintNotificationObject(++$threadNum,$n);
      }
      if (strtoupper($fbcmdParams[1]) == 'MARKREAD') {
        $unreadIds = array();
        foreach ($dataNotification as $n) {
          $unreadIds[] = $n['notification_id'];
        }
        if (count($unreadIds) > 0) {
          $fbReturn = $fbObject->api_client->call_method('facebook.Notifications.markRead',array('notification_ids' => implode(',',$unreadIds)));
          TraceReturn($fbReturn);
        }
      }
      SaveNoticeData($dataNotification);
    }
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
    PrintHeader(PrintIfPref('show_id','ID'),'NAME',PrintIfPref('pic_show_albumid','AID'),'PID',PrintIfPref('pic_show_date','CREATED'),'CAPTION',PrintIfPref('pic_show_links','LINK'),PrintIfPref('pic_show_src','SRC'));
    do {
      $curChunkIds = GetNextChunkIds();
      if ($curChunkIds) {
        $results = MultiFqlById($curChunkIds,"SELECT pid,aid,owner,src_small,src_big,src,link,caption,created FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner=[id])");
        foreach ($curChunkIds as $id) {
          if ($results[$id]) {
            foreach ($results[$id] as $pic) {
              PrintRow(PrintIfPref('show_id',$id),ProfileName($id),PrintIfPref('pic_show_albumid',$pic['aid']),$pic['pid'],PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$pic['created'])),$pic['caption'],PrintIfPref('pic_show_links',$pic['link']),PrintIfPref('pic_show_src',PhotoSrc($pic)));
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

  if ($fbcmdCommand == 'PINBOX') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_updates_count']);
    if (strtoupper($fbcmdParams[1]) == 'UNREAD') {
      $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 4 AND unread > 0";
    } else {
      if (strtoupper($fbcmdParams[1]) == 'NEW') {
        CheckMailTimeStamp();
        $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 4 AND updated_time > {$lastMailData['timestamp']}";
      } else {
        $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 4 LIMIT {$fbcmdParams[1]}";
      }
    }
    $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
    $keyMessageNames = 'id';
    MultiFQL(array('Thread','MessageNames'));
    if (!empty($dataThread)) {
      PrintFolderHeader();
      $threadNum = 0;
      foreach ($dataThread as $t) {
        PrintFolderObject(++$threadNum,$t);
      }
      SaveMailData($dataThread);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POST') {
    ValidateParamCount(1,10);
    $fbReturn = StreamPostHelper(null, $fbUser, 1);
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

  if ($fbcmdCommand == 'PPOST') {
    ValidateParamCount(2,11);
    SetDefaultParam(1,$fbcmdPrefs['default_ppost_id']);

    if (is_numeric($fbcmdParams[1])) {
      $postUserId = $fbcmdParams[1];
    } else {
      MultiFQL(array('PageId','PageNames'));
      $tagList = MatchTag($fbcmdParams[1]);
      if ($tagList) {
        $postUserId = $tagList[0][0];
      } else {
        FbcmdFatalError("Could not determine page {$fbcmdParams[1]}");
      }
    }
    $fbReturn = StreamPostHelper(null, $postUserId, 2);
    if ($fbReturn) {
      PrintHeaderQuiet('POST_ID');
      PrintRowQuiet($fbReturn);
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
    if ($fbcmdPrefs['status_tag']) {
      $statusText = TagText($fbcmdParams[1]);
    } else {
      $statusText = $fbcmdParams[1];
    }
    try {
      $fbReturn = $fbObject->api_client->call_method('facebook.users.setStatus',array('status' => $statusText,'status_includes_verb' => true));
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'RSVP') {
    ValidateParamCount(2);
    $eid = GetEventId($fbcmdParams[1]);
    $rsvp = $fbcmdParams[2];
    if (strtoupper($rsvp) == 'YES') {
      $rsvp = 'attending';
    }
    if (strtoupper($rsvp) == 'NO') {
      $rsvp = 'declined';
    }
    if (strtoupper($rsvp) == 'MAYBE') {
      $rsvp = 'unsure';
    }
    try {
      $fbReturn = $fbObject->api_client->events_rsvp($eid,$rsvp);
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

  if ($fbcmdCommand == 'SENTMAIL') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_sentmail_count']);
    if (strtoupper($fbcmdParams[1]) == 'UNREAD') {
      $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 1 AND unread > 0";
    } else {
      if (strtoupper($fbcmdParams[1]) == 'NEW') {
        CheckMailTimeStamp();
        $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 1 AND updated_time > {$lastMailData['timestamp']}";
      } else {
        $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 1 LIMIT {$fbcmdParams[1]}";
      }
    }
    $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
    $keyMessageNames = 'id';
    MultiFQL(array('Thread','MessageNames'));
    if (!empty($dataThread)) {
      PrintFolderHeader();
      $threadNum = 0;
      foreach ($dataThread as $t) {
        PrintFolderObject(++$threadNum,$t);
      }
      SaveMailData($dataThread);
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

  if ($fbcmdCommand == 'SHOWPREF') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_showpref_defaults']);
    PrintHeader('PREFERANCE','VALUE');
    foreach ($fbcmdPrefs as $switchKey => $switchValue) {
      if ($switchKey != 'prefs') {
        if ((substr($switchKey,0,8) != 'default_')||($fbcmdParams[1]))
        PrintRow($switchKey,var_export($switchValue,true));
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'STATUS') {
    ValidateParamCount(0,1);
    if (ParamCount() == 0) {
      GetCurrentStatus();
      if ($userStatus == 'unknown_status') {
        FbcmdFatalError("STATUS: unknown_status:\n  have you granted permission to read your status? try: fbcmd addperm");
      } else {
        if ($userStatus == '') {
          print "$userName [BLANK]\n";
        } else {
          print "$userName $userStatus\n";
        }
      }
    } else {
      if ($fbcmdPrefs['status_tag']) {
        $statusText = TagText($fbcmdParams[1]);
      } else {
        $statusText = $fbcmdParams[1];
      }
      try {
        $fbReturn = $fbObject->api_client->call_method('facebook.users.setStatus',array('status' => $statusText,'status_includes_verb' => true));
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
      if (substr($fbcmdParams[1],0,1) == $fbcmdPrefs['prefix_filter']) {
        $filterKeyQuery = "'" . GetFilterByName($fbcmdParams[1]) . "'";
      } else {
        $filterKeyQuery = "'{$fbcmdParams[1]}'";
      }
    }
    if (strtoupper($fbcmdParams[2]) == 'NEW') {
      CheckStreamTimeStamp();
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE filter_key IN ({$filterKeyQuery}) AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    } else {
      $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE filter_key IN ({$filterKeyQuery}) LIMIT {$fbcmdParams[2]}";
    }
    $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id, comments.comment_list.fromid FROM #fqlStream)';
    $keyStreamNames = 'id';
    MultiFQL(array('Stream','StreamNames'));
    if (!empty($dataStream)) {
      PrintPostHeader();
      $postNum = 0;
      foreach ($dataStream as $a) {
        PrintPostObject(++$postNum,$a);
      }
      SavePostData($dataStream);
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
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['update_branch']);
    PrintHeader('LOCAL_VERSION','ONLINE_VERSION','UPDATE_BRANCH');
    PrintRow($fbcmdVersion,GetGithubVersion($fbcmdParams[1]),$fbcmdPrefs['update_branch']);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'WHOAMI') {
    ValidateParamCount(0);
    $fbReturn = $fbObject->api_client->users_getInfo($fbUser,array('name'));
    PrintRow($fbUser,$fbReturn[0]['name']);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'WALLPOST') {
    ValidateParamCount(2,11);
    SetDefaultParam(1,$fbcmdPrefs['default_wallpost_flist']);

    GetFlistIds($fbcmdParams[1],true,false,true);
    PrintHeaderQuiet('POST_ID','RECIPIENT_NAME');
    foreach ($flistMatchArray as $id) {
      $fbReturn = StreamPostHelper($id, $fbUser, 2);
      PrintRowQuiet($fbReturn,ProfileName($id));
    }
  }

////////////////////////////////////////////////////////////////////////////////

  PrintFinish();
  return;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function AddCommand($cmd,$help) {
    global $fbcmdCommandList;
    global $fbcmdCommandHelp;
    $fbcmdCommandList[] = $cmd;
    $fbcmdCommandHelp[$cmd] = $help;
  }

////////////////////////////////////////////////////////////////////////////////

  function AddGoDestination($goCmd,$display,$url = '') {
    global $goDestinations;
    global $goDestinationsHelp;
    global $goDestinationsUrl;
    $goDestinations[] = $goCmd;
    $goDestinationsHelp[$goCmd] = $display;
    if ($url) {
      $goDestinationsUrl[$goCmd] = $url;
    }
  }

////////////////////////////////////////////////////////////////////////////////

  function AddPreference($pref, $value, $shortcut = '') {
    global $fbcmdPrefs;
    global $fbcmdPrefAliases;
    $fbcmdPrefs[$pref] = $value;
    if ($shortcut) {
      $fbcmdPrefAliases[$shortcut] = $pref;
    }
  }

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

  function CheckMailTimeStamp() {
    global $fbcmdPrefs;
    global $lastMailData;
    $lastMailData = LoadDataFile('mail_save','mailfile');
    if (!isset($lastMailData['timestamp'])) {
      if ($fbcmdPrefs['mail_save']) {
        FbcmdFatalError("Unexpected: Could not determine timestamp from last folder command");
      } else {
        FbcmdFatalError("NEW requires the preference -mail_save=1");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function CheckStreamTimeStamp() {
    global $fbcmdPrefs;
    global $lastPostData;
    $lastPostData = LoadDataFile('stream_save','postfile');
    if (!isset($lastPostData['timestamp'])) {
      if ($fbcmdPrefs['stream_save']) {
        FbcmdFatalError("Unexpected: Could not determine timestamp from last stream command");
      } else {
        FbcmdFatalError("NEW requires the preference -stream_save to be set");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function CleanColumns($columns) {
    global $fbcmdPrefs;
    global $printMatrix;

    if ($fbcmdPrefs['print_clean']) {
      for ($j=0;$j<count($columns);$j++) {
        if ($columns[$j] == '') {
          $match = true;
        } else {
          $row = count($printMatrix)-1;
          $match = false;
          $search = true;
          while (($search)&&($row >= 0)) {
            if (isset($printMatrix[$row][$j])) {
              if ($printMatrix[$row][$j] == '') {
                $row--;
              } else {
                $search = false;
                if (strcmp($printMatrix[$row][$j],$columns[$j]) == 0) {
                  $match = true;
                }
              }
            } else {
              $search = false;
            }
          }
        }
        if ($match) {
          $columns[$j] = '';
        } else {
          break;
        }
      }
    }
    return $columns;
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
    FbcmdFatalError("{$defaultCommand}\n[{$eCode}] {$e->getMessage()}");
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdFatalError($err) {
    global $fbcmdVersion;
    print "fbcmd [v{$fbcmdVersion}] ERROR: {$err}";
    PrintFinish();
    exit;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdWarning($err) {
    global $fbcmdVersion;
    print "fbcmd [v{$fbcmdVersion}] WARNING: {$err}";
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FileMatches($dirName, $ext) {
    $matches = array();
    $dirName = CleanPath($dirName);
    if ($handle = @opendir($dirName)) {
      while (false !== ($file = readdir($handle))) {
        if (strtoupper(substr($file,-strlen($ext))) == strtoupper($ext)) {
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
    if (count($matchList) == 0) {
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

  function GetAlbumId($a) { //, $allowSpecial = false) {
    global $lastAlbumData;
    global $userStatus;
    global $fbUser;
    global $fbObject;


    if (strtoupper($a) == 'LATEST') {
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
        FbcmdFatalError("Could not retrieve latest album_id");
      }
    } else {
      if ($a < 1001) {
        $lastAlbumData = LoadDataFile('album_save','albumfile');
        if (isset($lastAlbumData['ids'][$a])) {
          return $lastAlbumData['ids'][$a];
        } else {
          FbcmdWarning ("Invalid Album ID: {$a}");
          return false;
        }
      } else {
        return $a;
      }
      // }
    }
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

  function GetEventId($a) {
    global $lastEventData;
    global $userStatus;
    global $fbUser;
    global $fbObject;

    if ($a < 1001) {
      $lastEventData = LoadDataFile('event_save','eventfile');
      if (isset($lastEventData['ids'][$a])) {
        return $lastEventData['ids'][$a];
      } else {
        FbcmdWarning ("Invalid Event ID: {$a}");
        return false;
      }
    } else {
      return $a;
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
      if (substr($item,0,1) == $fbcmdPrefs['prefix_friendlist']) {
        array_push_unique($flistFQL,'FriendListNames');
        array_push_unique($flistFQL,'FriendListMembers');
      }
      if ((substr($item,0,1) == $fbcmdPrefs['prefix_page'])||(strtoupper($item)=='=PAGES')) {
        array_push_unique($flistFQL,'PageId');
        array_push_unique($flistFQL,'PageNames');
      }
      if (substr($item,0,1) == $fbcmdPrefs['prefix_group']) {
        array_push_unique($flistFQL,'GroupNames');
      }
      if (substr($item,0,1) == $fbcmdPrefs['prefix_tag']) {
        array_push_unique($flistFQL,'PageId');
        array_push_unique($flistFQL,'PageNames');
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
        if (substr($itemUC,0,5) == '=BDAY') {
          $matchTime = time();
          if(preg_match("/=BDAY\+(\d+)?$/",$itemUC,$matches)) {
            if(isset($matches[1])) {
              $matchTime += 24*60*60 * $matches[1];
            } else {
              $matchTime += 24*60*60;
            }
          }
          if(preg_match("/=BDAY-(\d+)?$/",$itemUC,$matches)) {
            if(isset($matches[1])) {
              $matchTime -= 24*60*60 * $matches[1];
            } else {
              $matchTime -= 24*60*60;
            }
          }
          if(preg_match("/=BDAY=(.+)$/",$itemUC,$matches)) {
            $matchTime = strtotime($matches[1]);
            if (!$matchTime) {
              FbcmdWarning("Bad BDAY Syntax: [{$item}] using today");
              $matchTime = time();
            }
          }
          $matchDate = date('m/d',$matchTime);
          foreach ($dataFriendBaseInfo as $fbi) {
            if (substr($fbi['birthday_date'],0,5) == $matchDate) {
              array_push_unique($flistMatchArray,$fbi['uid']);
            }
          }
          continue;
        }
        if ($itemUC == '=ONLINE') {
          foreach ($dataFriendBaseInfo as $fbi) {
            if (($fbi['online_presence'] == 'active')||(($fbi['online_presence'] == 'idle')&&($fbcmdPrefs['online_idle']))) {
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
              array_push_unique($flistMatchArray,$page_id['page_id']);
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

      // !USERNAME /////////////////////////////////////////////////////////////

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

      // @TAG FORMAT ///////////////////////////////////////////////////////////

      if (substr($item,0,1) == $fbcmdPrefs['prefix_tag']) {
        $tagList = MatchTag(substr($item,1),$allowPages,false);
        if ($tagList) {
          array_merge_unique($flistMatchArray,array($tagList[0][0]));
        }
        continue;
      }

      // REGULAR NAMES /////////////////////////////////////////////////////////

      $uidMatches = FlistMatch($item,false,$dataFriendBaseInfo,'uid','name',$allowMultipleMatches);
      array_merge_unique($flistMatchArray,$uidMatches);
    }
    if (count($flistMatchArray) == 0) {
      if ($failOnEmpty) {
        if (substr(strtoupper($flistString),0,5) == '=BDAY') {
          print "No Friends With Birthday Matches\n";
          exit;
        } else {
          FbcmdFatalError("Empty flist: {$flistString}");
        }
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

  function GetGithubVersion($branch) {
    try {
      $phpFile = @file_get_contents("http://github.com/dtompkins/fbcmd/raw/{$branch}/fbcmd.php");
      preg_match ("/fbcmdVersion\s=\s'([^']+)'/",$phpFile,$matches);
      if (isset($matches[1])) {
        $githubVersion = $matches[1];
      } else {
        $githubVersion = 'err';
      }

    } catch (Exception $e) {
      $githubVersion = 'unavailable';
    }
    return $githubVersion;
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
        $lastPostData = LoadDataFile('stream_save','postfile');
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

  function GetThreadId($p) { //, $allowSpecial = false) {
    global $lastMailData;
    global $userStatus;
    global $fbUser;
    global $fbObject;

    // if (($p == 0)||(strtoupper($p) == 'LAST')||(strtoupper($p) == 'CURSTATUS')) {
      // if ($allowSpecial) {
        // if (strtoupper($p) == 'CURSTATUS') {
          // GetCurrentStatus();
          // if ($userStatus == '') {
            // FbcmdFatalError("CURSTATUS: Your status is blank");
          // }
          // $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} AND attachment=='' LIMIT 1";
        // } else {
          // $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} LIMIT 1";
        // }
        // try {
          // $fbReturn = $fbObject->api_client->fql_query($fql);
          // TraceReturn($fbReturn);
        // } catch(Exception $e) {
          // FbcmdException($e,'GET-POST');
        // }
        // if (isset($fbReturn[0]['post_id'])) {
          // return $fbReturn[0]['post_id'];
        // } else {
          // FbcmdFatalError("GETPOST: Could not retrieve post_id = {$p}");
        // }
      // } else {
        // global $fbcmdCommand;
        // FbcmdWarning ("{$fbcmdCommand} does not support post_id = {$p}");
      // }
    // } else {
      if ($p < 1001) {
        $lastMailData = LoadDataFile('mail_save','mailfile');
        if (isset($lastMailData['ids'][$p])) {
          return $lastMailData['ids'][$p];
        } else {
          FbcmdWarning ("Invalid Thread ID: {$p}");
          return false;
        }
      } else {
        return $p;
      }
    // }
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

  function LaunchBrowser($url) {
    global $fbcmdPrefs;
    global $hasLaunched;
    $hasLaunched = true;
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
      pclose(popen("start \"\" /B \"{$url}\"", "r"));
    } else {
      if ($fbcmdPrefs['launch_exec']) {
        $execString = str_replace('[url]', $url, $fbcmdPrefs['launch_exec']);
        exec($execString);
      } else {
        if (strtoupper(substr(PHP_OS, 0, 6)) == 'DARWIN') {
          exec("open \"{$url}\" > /dev/null 2>&1 &");
        } else {
          exec("xdg-open \"{$url}\" > /dev/null 2>&1 &");
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function LoadDataFile($prefSave, $prefFile) {
    global $fbcmdPrefs;
    global $fbcmdBaseDir;
    $fileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs[$prefFile]);
    $loadData = array('0');
    if ($fbcmdPrefs[$prefSave]) {
      if (!file_exists($fileName)) {
        FbcmdWarning("Could not locate {$prefFile} [{$fileName}]");
      } else {
        $loadData = unserialize(@file_get_contents($fileName));
      }
    }
    return($loadData);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function MatchTag($tag, $allowPages = true, $allowGroups = true) {
    global $fbcmdPrefs;
    global $dataFriendBaseInfo;
    global $dataPageNames;
    global $dataGroupNames;
    $matchOrder = explode(',',$fbcmdPrefs['status_tag_order']);
    $matchList = array();
    foreach ($matchOrder as $order) {
      $matchParams = explode(':',$order);
      if ($matchParams[0] == 'friends') {
        $matchList = TagFieldMatch($tag, $dataFriendBaseInfo, $matchParams[1], 'uid', $matchParams[2]);
      }
      if (($matchParams[0] == 'pages')&&($allowPages)) {
        $matchList = TagFieldMatch($tag, $dataPageNames, $matchParams[1], 'page_id', $matchParams[2]);
      }
      if (($matchParams[0] == 'groups')&&($allowGroups)) {
        $matchList = TagFieldMatch($tag, $dataGroupNames, $matchParams[1], 'gid', $matchParams[2]);
      }
      if (count($matchList) > 0) {
        break;
      }
    }
    if (count($matchList) == 1) {
      return ($matchList);
    } else {
      if (count($matchList) == 0) {
        FbcmdWarning("Tag [{$tag}] had no matches");
      } else {
        FbcmdWarning("Tag [{$tag}] had multiple matches:");
        foreach ($matchList as $item) {
          if ($item[1] != $item[2]) {
            print "  {$item[1]} ({$item[2]})\n";
          } else {
            print "  {$item[1]}\n";
          }
        }
      }
      return (false);
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
    if ($fbcmdPrefs['pic_size'] == 0) {
      $fieldName = 'src_small';
    }
    if ($fbcmdPrefs['pic_size'] == 2) {
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
      if ($fbcmdPrefs['print_linefeed_subst']) {
        $col = str_replace("\n", $fbcmdPrefs['print_linefeed_subst'], $col);
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
        foreach ($printMatrix as $row) {
          while (count($row) > count($columnWidth)) {
            $columnWidth[] = 0;
          }
          for ($i=0; $i<count($row); $i++) {
            if (strlen($row[$i])>$columnWidth[$i]) {
              $columnWidth[$i]=strlen($row[$i]);
            }
          }
        }
        for ($i=0; $i<count($columnWidth)-1; $i++) {
          $columnWidth[$i] += $fbcmdPrefs['print_col_padding'];
        }

        if ($fbcmdPrefs['print_wrap']) {
          $consoleWidth = $fbcmdPrefs['print_wrap_width'];
          if ($fbcmdPrefs['print_wrap_env_var']) {
            if (getenv($fbcmdPrefs['print_wrap_env_var'])) {
              $consoleWidth = getenv($fbcmdPrefs['print_wrap_env_var']);
            }
          }
          $colToWrap = count($columnWidth) - 1;
          $wrapWidth = $consoleWidth - array_sum($columnWidth) + $columnWidth[$colToWrap] - 1;
          if ($wrapWidth < $fbcmdPrefs['print_wrap_min_width']) {
            $wrapWidth = $columnWidth[$colToWrap]+1;
          }
          $backupMatrix = $printMatrix;
          $printMatrix = array();
          foreach ($backupMatrix as $row) {
            if (isset($row[$colToWrap])) {
              $rightCol = array_pop($row);
              $wrapped = wordwrap($rightCol,$wrapWidth,"\n",$fbcmdPrefs['print_wrap_cut']);
              $newRows = explode("\n",$wrapped);
              foreach ($newRows as $nr) {
                $addRow = $row;
                array_push($addRow,$nr);
                $printMatrix[] = CleanColumns($addRow);
              }
            } else {
              $printMatrix[] = $row;
            }
          }
        } else {
          if ($fbcmdPrefs['print_linefeed_subst']) {
            $colToWrap = count($columnWidth) - 1;
            for ($j=0; $j < count($printMatrix); $j++) {
              if (isset($printMatrix[$j][$colToWrap])) {
                $printMatrix[$j][$colToWrap] = str_replace("\n", $fbcmdPrefs['print_linefeed_subst'], $printMatrix[$j][$colToWrap]);
              }
            }
          }
        }

        foreach ($printMatrix as $row) {
          for ($i=0; $i<count($row); $i++) {
            if ($i < count($row)-1) {
              print str_pad($row[$i], $columnWidth[$i], ' ');
            } else {
              print $row[$i];
            }
          }
          print "\n";
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintFolderHeader() {
    global $fbcmdPrefs;
    $threadInfo = array();
    if ($fbcmdPrefs['mail_save']) {
      $threadInfo[] = '[#]';
    }
    if ($fbcmdPrefs['folder_show_threadid']) {
      $threadInfo[] = 'THREAD_ID';
    }
    PrintHeader($threadInfo,'FIELD','VALUE');
    if ($fbcmdPrefs['folder_blankrow']) {
      PrintRow('');
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintFolderObject($threadNum, $thread) {
    global $fbcmdPrefs;
    global $fbUser;

    $threadInfo = array();
    if ($fbcmdPrefs['mail_save']) {
      $showThreadNum = '[' . $threadNum . ']';
      if ($thread['unread']) {
        $showThreadNum .= '*';
      }
      $threadInfo[] = $showThreadNum;
    }
    if ($fbcmdPrefs['folder_show_threadid']) {
      $threadInfo[] = $thread['thread_id'];
    }

    $subjectShow = $thread['subject'];
    if ($subjectShow == '') {
      $subjectShow = '[no subject]';
    }
    PrintRow($threadInfo,'subject',$subjectShow);

    $recipientsList = array();
    foreach ($thread['recipients'] as $r) {
      if ($r != $fbUser) {
        $recipientsList[] = ProfileName($r);
      }
    }
    $recipientsShow = implode(',',$recipientsList);
    PrintRow($threadInfo,':to/from',$recipientsShow);


    if ($fbcmdPrefs['folder_show_date']) {
      PrintRow($threadInfo,':date', date($fbcmdPrefs['folder_dateformat'],$thread['updated_time']));
    }


    if ($fbcmdPrefs['folder_show_snippet']) {
      $snippetShow = str_replace("\n", ' ', $thread['snippet']);
      if (count($recipientsList) > 1) {
        $snippetShow = ProfileName($thread['snippet_author']) . " :: " . $snippetShow;
      }
      PrintRow($threadInfo,':snippet', $snippetShow);
    }

    if ($fbcmdPrefs['folder_blankrow']) {
      PrintRow('');
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

  function PrintNotificationHeader() {
    global $fbcmdPrefs;
    $header = array();
    $header[] = '[#]';
    $header[] = PrintIfPref('notices_show_id','NOTIFICATION_ID');
    $header[] = 'SOURCE';
    $header[] = 'FIELD';
    $header[] = 'VALUE';
    PrintHeader($header);
    if ($fbcmdPrefs['notices_blankrow']) {
      PrintRow('');
    }
  }
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintNotificationObject($threadNum, $n) {
    global $fbcmdPrefs;
    global $fbUser;

    $postInfo = array();
    $postInfo[] = '[' . $threadNum . ']';
    if ($n['is_unread']) {
      $postInfo[0] .= '*';
    }
    if ($fbcmdPrefs['notices_show_id']) {
      $postInfo[] = $n['notification_id'];
    }
    $prefix = '';
    if ($n['sender_id'] != $fbUser) {
      PrintRow($postInfo,ProfileName($n['app_id']),$prefix . 'from',ProfileName($n['sender_id']));
      $prefix = ':';
    }
    if ($fbcmdPrefs['notices_show_date']) {
      PrintRow($postInfo,ProfileName($n['app_id']),$prefix . 'date',date($fbcmdPrefs['notices_dateformat'],$n['created_time']));
      $prefix = ':';
    }
    if ($n['title_text'] != '') {
      PrintRow($postInfo,ProfileName($n['app_id']),$prefix . 'title',strip_tags($n['title_text']));
      $prefix = ':';
    }
    if ($n['body_text'] != '') {
      PrintRow($postInfo,ProfileName($n['app_id']),$prefix . 'body',strip_tags($n['body_text']));
    }
    if ($fbcmdPrefs['notices_blankrow']) {
      PrintRow('');
    }
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
    if ($fbcmdPrefs['stream_blankrow']) {
      PrintRow('');
    }
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
      $msgType = 'attach post';
      if (isset($post['attachment']['media'][0])) {
        if (isset($post['attachment']['media'][0]['type'])) {
          $msgType = $post['attachment']['media'][0]['type'] . ' post';
        }
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
          if ($post['likes']['count'] == 1) {
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
          PrintRow($postInfo,$userInfo,$timeInfo,':comments',"Showing {$shownCount} of {$totalCount} Comments");
        }
      }
      $commentCount = 0;
      foreach ($commentData as $comment) {
        $commentCount++;
        $timeInfo = PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$comment['time']));
        PrintRow($postInfo,$userInfo,$timeInfo,':comment' . $commentCount,ProfileName($comment['fromid']) . ' :: ' . $comment['text']);
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
              PrintRow($postInfo,$userInfo,$timeInfo,':comments',"{$totalCount} Comments");
            } else {
              if ($shownCount < $totalCount) {
                PrintRow($postInfo,$userInfo,$timeInfo,':comments',"Showing {$shownCount} of {$totalCount} Comments");
              }
              $commentCount = 0;
              foreach ($post['comments']['comment_list'] as $comment) {
                $commentCount++;
                $timeInfo = PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$comment['time']));
                PrintRow($postInfo,$userInfo,$timeInfo,':comment' . $commentCount,ProfileName($comment['fromid']) . ' :: ' . $comment['text']);
              }
            }
          }
        }
      }
    }
    if ($fbcmdPrefs['stream_blankrow']) {
      PrintRow('');
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

 function PrintRow() {
    global $fbcmdPrefs;
    global $printMatrix;
    $rowArray = array_flatten(func_get_args());
    $columns = array();
    foreach ($rowArray as $col) {
      if (is_array($col)) {
        foreach ($col as $c) {
          if ($c != 'SKIP_COLUMN') {
            $columns[] = $c;
          }
        }
      } else {
        if ($col != 'SKIP_COLUMN') {
          $columns[] = $col;
        }
      }
    }
    $printColumns = CleanColumns($columns);
    $printMatrix[] = $printColumns;

    if ($fbcmdPrefs['print_csv']) {
      PrintCsvRow($printColumns);
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
        if ($fieldPrefix == '') {
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
    global $indexMessageNames;
    global $indexFlistNames;
    global $indexApplicationNames;
    if (isset($indexFriendBaseInfo[$id])) {
      return $indexFriendBaseInfo[$id]['name'];
    }
    if (isset($indexPageNames[$id])) {
      return $indexPageNames[$id]['name'];
    }
    if (isset($indexStreamNames[$id])) {
      return $indexStreamNames[$id]['name'];
    }
    if (isset($indexMessageNames[$id])) {
      return $indexMessageNames[$id]['name'];
    }
    if (isset($indexFlistNames[$id])) {
      return $indexFlistNames[$id]['name'];
    }
    if (isset($indexApplicationNames[$id])) {
      return $indexApplicationNames[$id]['display_name'];
    }
    return 'unknown';
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SaveAlbumData($obj) {
    $saveData = array ('ids' => array('0'), 'link' => array('0'), 'timestamp' => time());
    foreach ($obj as $d) {
      $saveData['ids'][] = $d['aid'];
      $saveData['link'][] = $d['link'];
    }
    SaveDataFile('album_save','albumfile',$saveData);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SaveDataFile($prefSave, $prefFile, $saveData) {
    global $fbcmdPrefs;
    global $fbcmdBaseDir;
    $fileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs[$prefFile]);
    if ($fbcmdPrefs[$prefSave]) {
      if (@file_put_contents($fileName,serialize($saveData)) == false) {
        FbcmdWarning("Could not generate {$prefFile} {$fileName}");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SaveEventData($obj) {
    $saveData = array ('ids' => array('0'), 'timestamp' => time());
    foreach ($obj as $d) {
      $saveData['ids'][] = $d['eid'];
    }
    SaveDataFile('event_save','eventfile',$saveData);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////

  function SaveMailData($obj) {
    $saveData = array ('ids' => array('0'), 'timestamp' => time());
    foreach ($obj as $d) {
      $saveData['ids'][] = $d['thread_id'];
    }
    SaveDataFile('mail_save','mailfile',$saveData);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SaveNoticeData($obj) {
    $saveData = array ('ids' => array('0'), 'href' => array('0'), 'timestamp' => time());
    foreach ($obj as $d) {
      $saveData['ids'][] = $d['notification_id'];
      $saveData['href'][] = $d['href'];
    }
    SaveDataFile('notices_save','noticefile',$saveData);
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

    if (file_put_contents($outputFile, $photoContents) == false) {
      FbcmdWarning("Could not save {$outputFile}");
      return false;
    }

    return true;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SavePostData($obj) {
    $saveData = array ('ids' => array('0'), 'url' => array('0'), 'link' => array('0'), 'timestamp' => time());
    foreach ($obj as $d) {
      $saveData['ids'][] = $d['post_id'];
      if (isset($d['attachment']['href'])) {
        $saveData['link'][] = $d['attachment']['href'];
      } else {
        $saveData['link'][] = $d['permalink'];
      }
      $saveData['url'][] = $d['permalink'];
    }
    SaveDataFile('stream_save','postfile',$saveData);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SavePrefs($fileName) {
    $fileContents = SavePrefsContents();
    if (@file_put_contents($fileName,$fileContents) == false) {
      FbcmdFatalError("Could not write {$fileName}");
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // If you modify this, copy it to fbcmd_update.php
  function SavePrefsContents() {
    global $fbcmdPrefs;
    $fileContents = "<?php\n";
    foreach ($fbcmdPrefs as $switchKey => $switchValue) {
      if ($switchKey != 'prefs') {
        if (strpos($switchKey,'mkdir_mode') === false) {
          $fileContents .= "  \$fbcmdPrefs['{$switchKey}'] = " . var_export($switchValue,true) . ";\n";
        } else {
          $fileContents .= "  \$fbcmdPrefs['{$switchKey}'] = 0" . decoct($switchValue) . ";\n";
        }
      }
    }
    $fileContents .= "?>\n";
    return $fileContents;
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

  function ShowAuth() {
    global $fbcmdPrefs, $urlAccess, $urlAuth;
    print "\n";
    print "This application needs to be authorized to access your facebook account.\n";
    print "\n";
    print "Step 1: Allow basic (initial) access to your acount via this url:\n\n";
    print "{$urlAccess}\n";
    print "to launch this page, execute: fbcmd go access\n";
    print "\n";
    print "Step 2: Generate an offline authorization code at this url:\n\n";
    print "{$urlAuth}\n";
    print "to launch this page, execute: fbcmd go auth\n";
    print "\n";
    print "obtain your authorization code (XXXXXX) and then execute: fbcmd auth XXXXXX\n\n";
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ShowUsageCmd($cmd) {
    global $fbcmdCommandList;
    global $fbcmdCommandHelp;

    if (!isset($fbcmdCommandHelp[$cmd])) {
      $fbcmdCommandHelp[$cmd] = "[No Help Available]\n";
    }
    $helpText = explode('~',$fbcmdCommandHelp[$cmd]);
    print "  " . str_pad($cmd, 10, ' ') . $helpText[0]. "\n";
    for ($j=1; $j < count($helpText); $j++) {
      print "            " . $helpText[$j] . "\n";
    }
    print "\n";
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ShowUsage() {
    global $fbcmdVersion;
    global $fbcmdCommandList;
    global $fbcmdCommandHelp;

    print "\n";
    print "fbcmd [v{$fbcmdVersion}] Facebook Command Line Interface\n\n";

    print "syntax:\n\n";

    print "  fbcmd COMMAND required_parameter(s) [optional_parameter(s)] -switch=value\n\n";

    print "commands: (can be in lower case)\n\n";

    foreach ($fbcmdCommandList as $cmd) {
      ShowUsageCmd($cmd);
    }

    print "examples:\n\n";

    print "  fbcmd status \"is excited to play with fbcmd\"\n";
    print "  fbcmd finfo birthday_date -csv\n";
    print "  fbcmd stream #family 25\n\n";

    print "for additional help, examples, parameter usage, flists, preference settings,\n";
    print "visit the FBCMD wiki at:\n\n";
    print "  http://fbcmd.dtompkins.com\n\n";
    exit;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function StreamPostHelper($target_id, $uid, $offset) {
    global $fbcmdParams;
    global $fbcmdPrefs;
    global $fbObject;

    $firstParam = strtoupper($fbcmdParams[$offset]);

    if (in_array($firstParam, array('MP3','IMG','FLASH'))) {
      if ($firstParam == 'MP3') {
        ValidateParamCount($offset+2, $offset+9);
        SetDefaultParam($offset+1,$fbcmdPrefs['default_post_message']);
        SetDefaultParam($offset+2,$fbcmdPrefs['default_postmp3_mp3src']);
        SetDefaultParam($offset+3,$fbcmdPrefs['default_postmp3_mp3title']);
        SetDefaultParam($offset+4,$fbcmdPrefs['default_postmp3_mp3artist']);
        SetDefaultParam($offset+5,$fbcmdPrefs['default_postmp3_mp3album']);
        $msg = $fbcmdParams[$offset+1];
        $media = array(array('type' => 'mp3', 'src' => $fbcmdParams[$offset+2], 'title' => $fbcmdParams[$offset+3], 'artist' => $fbcmdParams[$offset+4], 'album' => $fbcmdParams[$offset+5]));
        $offsetPostData = $offset + 6;
      }
      if ($firstParam == 'IMG') {
        ValidateParamCount($offset+2, $offset+7);
        SetDefaultParam($offset+1,$fbcmdPrefs['default_post_message']);
        SetDefaultParam($offset+2,$fbcmdPrefs['default_postimg_imgsrc']);
        if ($fbcmdPrefs['default_postimg_imglink'] == '0') {
          SetDefaultParam($offset+3,$fbcmdParams[$offset+2]);
        } else {
          SetDefaultParam($offset+3,$fbcmdPrefs['default_postimg_imglink']);
        }
        $msg = $fbcmdParams[$offset+1];
        $media = array(array('type' => 'image', 'src' => $fbcmdParams[$offset+2], 'href' => $fbcmdParams[$offset+3]));
        $offsetPostData = $offset + 4;
      }
      if ($firstParam == 'FLASH') {
        ValidateParamCount($offset+3, $offset+7);
        SetDefaultParam($offset+1,$fbcmdPrefs['default_post_message']);
        SetDefaultParam($offset+2,$fbcmdPrefs['default_postflash_swfsrc']);
        SetDefaultParam($offset+3,$fbcmdPrefs['default_postflash_imgsrc']);
        $msg = $fbcmdParams[$offset+1];
        $media = array(array('type' => 'flash', 'swfsrc' => $fbcmdParams[$offset+2], 'imgsrc' => $fbcmdParams[$offset+3]));
        $offsetPostData = $offset + 4;
      }
    } else {
      ValidateParamCount($offset,$offset+4);
      SetDefaultParam($offset,$fbcmdPrefs['default_post_message']);
      $msg = $fbcmdParams[$offset];
      $media = '';
      $offsetPostData = $offset + 1;
    }

    SetDefaultParam($offsetPostData, $fbcmdPrefs['default_post_name']);
    SetDefaultParam($offsetPostData + 1, $fbcmdPrefs['default_post_link']);
    SetDefaultParam($offsetPostData + 2, $fbcmdPrefs['default_post_caption']);
    SetDefaultParam($offsetPostData + 3, $fbcmdPrefs['default_post_description']);

    $attachment = array('name' => $fbcmdParams[$offsetPostData], 'href' => $fbcmdParams[$offsetPostData + 1], 'caption' => $fbcmdParams[$offsetPostData + 2], 'description' => $fbcmdParams[$offsetPostData + 3]);
    if ($media) {
      $attachment['media'] = $media;
    }
    
    if (($fbcmdPrefs['sharepost'])&&($fbcmdParams[$offsetPostData + 1])) {
      $actionLinks = array(array('text' => 'Share', 'href' => 'http://www.facebook.com/share.php?u=' . $fbcmdParams[$offsetPostData + 1]));
    } else {
      $actionLinks = null;
    }

    try {
      $fbReturn = $fbObject->api_client->stream_publish($msg, $attachment, $actionLinks, $target_id, $uid);
      TraceReturn($fbReturn);
    } catch(Exception $e) {
      FbcmdException($e);
    }
    return $fbReturn;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function TagFieldMatch($matchString, $dataToSearch, $matchField, $idField, $partial = false, $nameField = 'name') {
    $matchList = array();
    if ($partial) {
      $matchExp = "/$matchString/i";
    } else {
      $matchExp = "/^$matchString/i";
    }
    if (isset($dataToSearch)) {
      foreach ($dataToSearch as $d) {
        if (isset($d[$matchField])) {
          if (preg_match($matchExp,$d[$matchField])) {
            $matchList[] = array($d[$idField],$d[$nameField],$d[$matchField]);
          }
        }
      }
    }
    return $matchList;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function TagText($textToTag) {
    global $fbcmdPrefs;
    $textToTag = str_replace('@@','[[AT]]',$textToTag);
    if (preg_match_all($fbcmdPrefs['status_tag_syntax'], $textToTag, $matches, PREG_SET_ORDER)) {
      MultiFQL(array('FriendId','FriendBaseInfo','PageId','PageNames','GroupNames'));
      foreach ($matches as $pregMatch) {
        $matchList = MatchTag($pregMatch[1]);
        if ($matchList) {
          $taggedText = "@[{$matchList[0][0]}:{$matchList[0][0]}:{$matchList[0][1]}]";
        } else {
          $taggedText = "[[AT]]{$pregMatch[1]}";
        }
        $textToTag = str_replace($pregMatch[0],$taggedText,$textToTag);
      }
    }
    $textToTag = str_replace('[[AT]]','@',$textToTag);
    return $textToTag;
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
    if ($fbcmdPrefs['ppic_size'] == 0) {
      $fieldName = 'pic_small';
    }
    if ($fbcmdPrefs['ppic_size'] == 2) {
      $fieldName = 'pic';
    }
    if ($fbcmdPrefs['ppic_size'] == 3) {
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
      print "\n";
      FbcmdWarning("[{$fbcmdCommand}] Invalid number of parameters");
      print "\n";
      print "try:        [fbcmd help ". strtolower($fbcmdCommand). "]\nto launch:  http://fbcmd.dtompkins.com/commands/" . strtolower($fbcmdCommand) . "\n\nbasic help:\n\n";
      ShowUsageCmd($fbcmdCommand);
      exit;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function VerifyOutputDir($fileName) {
    global $fbcmdPrefs;
    $fileName = str_replace('\\', '/', $fileName);
    if (strrpos($fileName,'/')) {
      $filePath = CleanPath(substr($fileName,0,strrpos($fileName,'/')));
      if (!file_exists($filePath)) {
        if ($fbcmdPrefs['auto_mkdir']) {
          if (!mkdir($filePath,$fbcmdPrefs['mkdir_mode'],true)) {
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
