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
//   [Facebook Page]             http://facebook.com/cmdlinepage              //
//   [Facebook App Home]         http://apps.facebook.com/cmdline/            //
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
//  (see facebook.php for the Apache License used by facebook)                //
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
//   see history.txt, todo.txt & bugs.txt for the respective information      //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Disclaimer: This is my first (and currently only) PHP applicaiton,       //
//               so my apologies if I don't follow PHP best practices.        //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

  $fbcmdVersion = '2.0-beta2';

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

  // set the default & core globals to be empty

  $fbcmdCommand = '';
  $fbcmdParams = array();
  $fbcmdPrefs = array();
  $fbcmdAlias = array();
  $fbcmdRefCache = array();
  $fbcmdAuthInfo = array();
  $fbcmdAuthVersion = 2;

////////////////////////////////////////////////////////////////////////////////

  // because the FB object does a session_start(), we need to do one first
  // otherwise we get Warning: session_start(): Cannot send session cookie...

  session_start();

////////////////////////////////////////////////////////////////////////////////

  // create a list of all the valid commands

  $fbcmdCommandList = array();

  // the following are 1.1 commands that haven't been done yet, or will be changed
  $notYet = array('EVENTS','FINBOX','FLAST','FONLINE','FQL','FSTATUS','FSTREAM','FULLPOST','INBOX','MSG','MYWALL','NOTICES','NOTIFY','OPICS','PPICS','RECENT','RESTATUS','RSVP','SENTMAIL','SFILTERS','STREAM','TAGPIC');
  AddCommand('EVENTS',    '[time]~Display your events');
  AddCommand('FINBOX',    '[flist]~Display mail messages from specific friend(s)');
  AddCommand('FLAST',     'flist [count]~See the last [count] status updates of friend(s)');
  AddCommand('FONLINE',   '[flist]~List any friends who are currently online');
  AddCommand('FQL',       'statement [flist]~Perform a custom FQL Query');
  AddCommand('FSTATUS',   '[flist]~List current status of friend(s)');
  AddCommand('FSTREAM',   '[flist] [count|new]~Show stream stories for friend(s)');
  AddCommand('FULLPOST',  'post_id~Displays a stream post with all of the comments');
  AddCommand('INBOX',     '[count|unread|new]~Display the latest messages from the inbox');
  AddCommand('MSG',       'message_id~Displays a full message thread (e.g.: after an INBOX)');
  AddCommand('MYWALL',    '[count|new]~Show the posts from other users to your wall');
  AddCommand('NOTICES',   '[unread|markread]~See notifications from facebook, applications & users');
  AddCommand('NOTIFY',    '<no parameters>~See (simple) notifications such as # of unread messages');
  AddCommand('OPICS',     'flist [savedir]~List [and optionally save] all photos owned by friend(s)');
  AddCommand('PPICS',     '[flist] [savedir]~List [and optionally save] all profile photos of friend(s)');
  AddCommand('RECENT',    '[flist] [count]~Shows the [count] most recent friend status updates');
  AddCommand('RESTATUS',  'message~Replace your status (deletes your status and adds a new status)');
  AddCommand('RSVP',      'event_id yes|no|maybe~RSVP to an Event from the EVENTS command');
  AddCommand('SENTMAIL',  '[count|unread|new]~Display the latest messages from the sent mail folder');
  AddCommand('SFILTERS',  '<no parameters>~Display available stream filters for the STREAM command');
  AddCommand('STREAM',    '[filter_rank|filter_key|#filter_name] [count|new]~Show stream stories (with optional filter -- see SFILTERS)');
  AddCommand('TAGPIC',    'pic_id target [x y]~Tag a photo');

  // The following are 2.0 commands (not all complete yet)

  AddCommand('ACCOUNTS',  '<no parameters>~List your accounts (e.g.: your pages)');
  AddCommand('ADDALBUM',  'title [description]~Create a new photo album');
  AddCommand('ADDPERM',   '[permissions_list]~(Launch a website to) grant FBCMD extended permissions.');
  AddCommand('ADDPIC',    'filename [album_id] [caption]~Upload (add) a photo to an album');
  AddCommand('ADDPICD',   'dirname [album_id]~Upload (add) all *.jpg files in a directory to an album~(change to *.png with -pext=png)');
  AddCommand('ALBUMS',    '<no parameters>~List all your photo albums');
  AddCommand('ALIAS',     '[aliasname objname]~Create a new alias for an object~or list all aliases if no parameters');
  AddCommand('APICS',     'album_id [savedir]~List [and optionally save] all photos from an album');
  AddCommand('AS',        'objname COMMAND <parameters>~execute COMMAND on behalf of objname (eg: for pages)');
  AddCommand('AUTH',      'authcode~Enter your facebook authorization code');
  AddCommand('COMMENT',   'objname text~Add a comment to a post, picture, etc.');
  AddCommand('COUNT',     '[N|all] COMMAND <parameters>~retrieve N results for COMMAND');
  AddCommand('DEL',       'objname~Deletes a facebook object');
  AddCommand('FRIENDS',   '<no parameters>~List your friends');
  AddCommand('GO',        'destination [id]~Launches a web browser for the given destination');
  AddCommand('GRAPHAPI',  'path [method] [php_params]~method is one of GET,POST,DELETE~php_params in php format: "array(\'fld1\' => val1, \'f2\' => v2, ...)"~e.g.: fbcmd graphapi /me/feed post "array(\'message\' => \'hi\')"');
  AddCommand('GROUPS',    '<no parameters>~List your groups');
  AddCommand('HELP',      '[command|preference]~Display this help message, or launch web browser for [command]');
  AddCommand('HOME',      '[webpage]~Launch a web browser to visit the FBCMD home page');
  AddCommand('INFO',      'objname [fields]~Display info for a facebook object (user, me, page, event, etc.)~fields is a comma-separated list of fields for the object');
  AddCommand('LAST',      '[N]~Show results from [Nth] successful command');
  AddCommand('LIKE',      'objname~Like an object (can\'t like pages)');
  AddCommand('LIKES',     '[category]~List your likes~[category] is one of books,games,movies,music,television');
  AddCommand('LINKS',     '<no parameters>~Display your posted links');
  AddCommand('LOADNOTE',  'title filename~Same as POSTNOTE but loads the contents from a file');
  AddCommand('LOOP',      'objlist COMMAND <parameters>~execute COMMAND for each objname in objlist');
  AddCommand('MATCH',     'objname~Try to resolve a name to an object');
  AddCommand('MUTUAL',    'friendid~List friend you have in common with another friend');
  AddCommand('NEWS',      '<no parameters>~Display News Feed Items');
  AddCommand('NOTES',     'Display your notes');
  AddCommand('POST',      '<extra args> message [link_url] [name] [caption] [description]~Post a story on your feed.~<extra args> include:~  [IMG url] add a picture.~  [SRC url] add a source (eg: for videos, url for flash source)');
  AddCommand('POSTS',     '<no parameters>~Display your posts');
  AddCommand('POSTLINK',  'link_url [message] [name] [caption] [description]~Share a link on your news feed');
  AddCommand('POSTNOTE',  'title body~Share a note on your news feed');
  AddCommand('PREV',      '[N]~Show output from [Nth] previous command or missed matched id');
  AddCommand('REFRESH',   '<no parameters>~Refresh the cache of references (do after new friends, likes, etc.)');
  AddCommand('RESET',     '<no parameters>~Delete your authorization info');
  AddCommand('SAVEPREF',  '[filename]~Save your current preferences / switch settings to a file');
  AddCommand('SHOWPREF',  '[0|1]~Show your preferences (settings)~if arg is 1, will show command & output defaults');
  AddCommand('SHOWPERM',  '<no parameters>~List permissions granted to FBCMD');
  AddCommand('STATUS',    '[text message]~Set your status');
  AddCommand('STATUSES',  '<no parameters>~Display your statuses');
  AddCommand('TARGET',    'objname COMMAND <parameters>~execute COMMAND for/on the objname~(can also use @objname syntax instead of target objname)~(e.g.: fbcmd @bob post "Hello, Bob!")');
  AddCommand('TEST',      '<no parameters>~Test your installation');
  AddCommand('TPICS',     '[savedir]~List [and optionally save] all photos where you are tagged');
  AddCommand('UNLIKE',    'objname~Unlike an object');
  AddCommand('UPDATE',    '[branch] [dir] [trace] [ignore_err]~Update FBCMD to the latest version');
  AddCommand('USAGE',     '(same as HELP)');
  AddCommand('VERSION',   '[branch]~Check for the latest version of FBCMD available');
  AddCommand('WALL',      '<no parameters>~Display items posted on your wall');
  AddCommand('WHOAMI',    '<no parameters>~Display the currently authorized user');

  $targetCommands = array('ALBUMS','APICS','FRIENDS','GROUPS','LIKES','LINKS','NEWS','NOTES','POST','POSTS','STATUSES','TPICS','WALL');
  $asCommands = array('ADDALBUM','ADDPIC','ADDPICD','ALBUMS','APICS','COMMENT','DEL','GRAPHAPI','INFO','LIKE','LOADNOTE','POST','POSTLINK','POSTNOTE','STATUS','TEST','UNLIKE','WHOAMI');
  $depricatedCommands = array('ALLINFO','DELPOST','DFILE','DISPLAY','FEED1','FEED2','FEED3','FEVENTS','FGROUPS','FINFO','FSTATUSID','FLSTATUS','LIMITS','LOADDISP','LOADINFO','NSEND','PICS','PINBOX','PPOST','SAVEDISP','SAVEINFO','UFIELDS','WALLPOST');

  if (isset($fbcmd_include_newCommands)) {
    foreach ($fbcmd_include_newCommands as $c) {
      AddCommand($c[0],$c[1]);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // You can set an environment variable FBCMD to specify the location of
  // your personal files: auth.txt, prefs.php, alias.php, etc...

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

  // If you want to customize your defaults:
  // a) modify your own prefs.php directly, or
  // b) use the SAVEPREF command
  // Do NOT change them here, as they will get blown away on updates

  // STEP ONE Set System Defaults

  // --------------------------------------------------------------------------

  // STEP ONE (part A) Universal Preferences

  // currently in 2.0
  AddPreference('aliasfile',"[datadir]aliases.php");
  AddPreference('apics_filename','[pid].jpg','apf');
  AddPreference('addpicd_ext','jpg','pext');
  AddPreference('appkey','42463270450');
  AddPreference('appsecret','88af69b7ab8d437bff783328781be79b');
  AddPreference('authfile',"[datadir]auth.txt");
  AddPreference('auto_mkdir','1');
  AddPreference('auto_refresh','604800');
  AddPreference('cache_refs','1');
  AddPreference('cachefile',"[datadir]refcache.txt");
  AddPreference('keyfile',"[datadir]sessionkeys.txt",'key');
  AddPreference('last_length','10');
  AddPreference('last_save','1');
  AddPreference('lastfile',"[datadir]last.txt");
  AddPreference('launch_exec','');
  AddPreference('mkdir_mode','0777');
  AddPreference('newline_subst','1');
  AddPreference('pic_retry_count','10','pr');
  AddPreference('pic_retry_delay','2','prd');
  AddPreference('pic_size','0','psize');
  AddPreference('pic_skip_exists','1','pskip');
  AddPreference('prefs','');
  AddPreference('prev_length','10');
  AddPreference('prev_save','1');
  AddPreference('prevfile',"[datadir]prev.txt");
  AddPreference('tpics_filename','[pid].jpg','tpf');
  AddPreference('trace','0','t');
  AddPreference('update_branch','master');
  // maybe in 2.0 (TBD)
  //2 AddPreference('event_dateformat','D M d H:i','edf');
  //2 AddPreference('events_attend_mask','15','emask');
  //2 AddPreference('opics_filename','[pid].jpg','of');
  //2 AddPreference('pic_dateformat','M d Y','pdf');
  //2 AddPreference('ppic_size','1','ppsize');
  //2 AddPreference('ppics_filename','[tid].jpg','ppf');
  //2 AddPreference('restatus_comment_new','1');

  // --------------------------------------------------------------------------

  // STEP ONE (part B) Command Argument Defaults

  AddPreference('default_addalbum_message','');
  AddPreference('default_addalbum_name','');
  AddPreference('default_addperm','all');
  AddPreference('default_addpicd_albumid','');
  AddPreference('default_addpicd_dirname','');
  AddPreference('default_addpic_albumid','');
  AddPreference('default_addpic_filename','');
  AddPreference('default_addpic_message','');
  AddPreference('default_apics_albumid','');
  AddPreference('default_apics_savedir','');
  AddPreference('default_as_obj','');
  AddPreference('default_comment_message','');
  AddPreference('default_loadnote_filename','');
  AddPreference('default_loadnote_title','');
  AddPreference('default_graphapi_method','GET');
  AddPreference('default_graphapi_params','');
  AddPreference('default_postlink_link','');
  AddPreference('default_postlink_message','');
  AddPreference('default_postnote_body','');
  AddPreference('default_postnote_title','');
  AddPreference('default_post_caption','');
  AddPreference('default_post_description','');
  AddPreference('default_post_img_url','');
  AddPreference('default_post_link','');
  AddPreference('default_post_message','');
  AddPreference('default_post_name','');
  AddPreference('default_post_src_url','');
  AddPreference('default_showpref_defaults','0');
  AddPreference('default_target_obj','');
  AddPreference('default_tpics_savedir','');
  //2 AddPreference('default_count','');
  //2 AddPreference('default_loop','');
  //2 AddPreference('default_ppics_savedir',false);
  //2 AddPreference('default_tagpic_pid','');
  //2 AddPreference('default_tagpic_target','=ME');
  //2 AddPreference('default_tagpic_x','50');
  //2 AddPreference('default_tagpic_y','50');

  // --------------------------------------------------------------------------

  // STEP ONE (part C) Output Parameters

  AddPreference('output_json_flat','0');
  AddPreference('output_php_flat','0');
  AddPreference('output_serial_flat','0');
  AddPreference('output_yaml_flat','0');

  AddPreference('output_header','0','hdr');
  AddPreference('output_pad','2','pad');
  AddPreference('output_rec_crumbs','0','crumb');
  AddPreference('output_rec_space','1');
  AddPreference('output_wrap_env_var','COLUMNS');
  AddPreference('output_wrap_min_width','20');
  AddPreference('output_wrap_width','80','col');

  AddPreference('csv_header','1');
  AddPreference('csv_bookend','"');
  AddPreference('csv_escaped_bookend','""');
  AddPreference('csv_force_bookends','0','csvf');
  AddPreference('csv_newline_subst','\\n');
  AddPreference('csv_separator',',');

  // --------------------------------------------------------------------------

  // STEP ONE (part D) Command Output Defaults

  // the following will override other settings if set
  AddPreference('output_format','','o');
  AddPreference('output_show','','oshow');
  AddPreference('output_col','','ocol');
  AddPreference('output_rec','','orec');
  AddPreference('output_flat','','oflat');

  $noOutputCommands = array('ADDPERM','AS','AUTH','COUNT','GO','HELP','HOME','LOOP','REFRESH','RESET','SAVEPREF','TARGET','TEST','UPDATE','USAGE');

  foreach ($fbcmdCommandList as $cmd) {
    if (!in_array($cmd,$noOutputCommands)) {
      $c = strtolower($cmd);
      AddPreference("output_format_{$c}",'rec');
      AddPreference("output_show_{$c}",'all');
      AddPreference("output_col_{$c}",'!default');
      AddPreference("output_rec_{$c}",'!default');
    }
  }

  AddPreference('output_col_default','index:6,name/message/story:0');
  AddPreference('output_col_id','index:6,id:20,name:0');
  AddPreference('output_col_fromname','index:6,from.name:20,message/story:0');
  AddPreference('output_rec_default','index:6,key:25,value');
  AddPreference('output_rec_bigindex','index:13,key:10,value');

  // --------------------------------------------------------------------------

  // STEP ONE (part E) Set Command Specific Output Defaults

  AddPreference('output_format_addpicd','col');
  AddPreference('output_format_albums','col');
  AddPreference('output_format_apics','col');
  AddPreference('output_format_friends','col');
  AddPreference('output_format_groups','col');
  AddPreference('output_format_likes','col');
  AddPreference('output_format_match','col');
  AddPreference('output_format_mutual','col');
  AddPreference('output_format_news','col');
  AddPreference('output_format_posts','col');
  AddPreference('output_format_statuses','col');
  AddPreference('output_format_tpics','col');
  AddPreference('output_format_wall','col');
  AddPreference('output_format_whoami','col');

  AddPreference('output_col_addpicd','filename,id:18,post_id:33');
  AddPreference('output_col_apics','!id');
  AddPreference('output_col_match','index:12,id:18,name');
  AddPreference('output_col_news','!fromname');
  AddPreference('output_col_tpics','!id');
  AddPreference('output_col_wall','!fromname');
  AddPreference('output_col_whoami','id:20,name');

  AddPreference('output_rec_addalbum','!bigindex');
  AddPreference('output_rec_addpic','!bigindex');
  AddPreference('output_rec_alias','key:20,value');
  AddPreference('output_rec_comment','!bigindex');
  AddPreference('output_rec_links','index:6,key:7,value');
  AddPreference('output_rec_notes','index:6,key:7,value');
  AddPreference('output_rec_post','!bigindex');
  AddPreference('output_rec_showperm','key:30,value');
  AddPreference('output_rec_status','!bigindex');

  AddPreference('output_show_links','index,message,link,name');
  AddPreference('output_show_notes','index,subject,message');

////////////////////////////////////////////////////////////////////////////////

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

  if ($fbcmdPrefs['newline_subst']) {
    for ($j=1; $j <= ParamCount(); $j++) {
      $fbcmdParams[$j] = str_replace("\\n","\n",$fbcmdParams[$j]);
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

  // create the Facebook Object

  require_once('facebook-php-sdk/src/facebook.php');

  $facebook = new Facebook(array(
    'appId'  => $fbcmdPrefs['appkey'],
    'secret' => $fbcmdPrefs['appsecret'],
    'fileUpload' => true));

////////////////////////////////////////////////////////////////////////////////

  if (in_array($fbcmdCommand,$depricatedCommands)) {
    FbcmdFatalError("{$fbcmdCommand} has been deprecated:\n  visit http://fbcmd.dtompkins.com/commands/" . strtolower($fbcmdCommand) . " for more information");
  }

////////////////////////////////////////////////////////////////////////////////

  if (in_array($fbcmdCommand,$notYet)) {
    FbcmdFatalError("{$fbcmdCommand} has not been added to version 2.0 yet\n  (feel free to nag Dave if you think this should be a priority)\n");
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
      LaunchBrowser('http://fbcmd.dtompkins.com/preferences/');
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
    for ($j=1; $j <= 4; $j++) {
      if (ParamCount() >= $j) {
        $execCmd .= " \"{$fbcmdParams[$j]}\"";
      }
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

  $fbcmdAliasFileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs['aliasfile']);
  if (file_exists($fbcmdAliasFileName)) {
    include($fbcmdAliasFileName);
  }

////////////////////////////////////////////////////////////////////////////////

  $urlAuth = "http://www.facebook.com/code_gen.php?v=1.0&api_key={$fbcmdPrefs['appkey']}";
  $urlAccess = "https://www.facebook.com/dialog/oauth?client_id={$fbcmdPrefs['appkey']}&redirect_uri=http://www.facebook.com/connect/login_success.html";

  AddGoDestination('[objname]',   'The page for [objname], can be # (eg: from prev)');
  AddGoDestination('access',      'Allow fbcmd to (initially) access your account',$urlAccess);
  AddGoDestination('app',         'The fbcmd page on facebook','http://facebook.com/fbcmd');
  AddGoDestination('auth',        'Authorize fbcmd for permanent access',$urlAuth);
  AddGoDestination('contribute',  'The fbcmd contact page','http://fbcmd.dtompkins.com/contribute');
  AddGoDestination('editapps',    'The facebook edit applications page','http://www.facebook.com/editapps.php');
  AddGoDestination('faq',         'The fbcmd FAQ','http://fbcmd.dtompkins.com/faq');
  AddGoDestination('github',      'The source repository at github','http://github.com/dtompkins/fbcmd');
  AddGoDestination('group',       'The fbcmd discussion group','http://groups.google.com/group/fbcmd');
  AddGoDestination('help',        'the fbcmd help page','http://fbcmd.dtompkins.com/help');
  AddGoDestination('home',        'The fbcmd home page','http://fbcmd.dtompkins.com');
  AddGoDestination('inbox',       'Your facebook inbox','http://www.facebook.com/inbox');
  AddGoDestination('install',     'The fbcmd installation page','http://fbcmd.dtompkins.com/installation');
  AddGoDestination('me',          'Your facebook profile','http://fbcmd.dtompkins.com/me');
  AddGoDestination('news',        'Your facebook home page','http://www.facebook.com/home.php');
  AddGoDestination('update',      'The fbcmd update page','http://fbcmd.dtompkins.com/update');


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

  $fbcmdOldKeyFileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs['keyfile']);
  $fbcmdAuthFileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs['authfile']);

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'RESET') {
    ValidateParamCount(0);
    if (file_exists($fbcmdAuthFileName)) {
      if (!unlink($fbcmdOldKeyFileName)) {
        FbcmdFatalError("Could not delete {$fbcmdAuthFileName}\n");
      }
      print "{$fbcmdAuthFileName} has been deleted\n";
    }
    ShowAuth();
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'AUTH') {
    ValidateParamCount(1);
    GetOldSessionKey($fbcmdParams[1]);
    GenAuthInfoFromSessionKey();
    if (!isset($fbcmdAuthInfo['access_token'])) {
      FbcmdFatalError("Could not obtain oauth access_token");
    }
    SaveDataFile('authfile',$fbcmdAuthInfo);
    $facebook->setAccessToken($fbcmdAuthInfo['access_token']);
    try {
      $fbReturn = $facebook->api('/me');
      TraceReturn();
      if (isset($fbReturn['name'])) {
        print "\nfbcmd [v$fbcmdVersion] AUTH Code accepted.\n\nWelcome to FBCMD, {$fbReturn['name']}!\n\n";
        print "most FBCMD commands require additional permissions.\n";
        print "to grant default permissions, execute: fbcmd addperm\n";
        print "to test your permissions, execute: fbcmd test\n";
      } else {
        FbcmdFatalError("Possible authentication error: could not determine your name");
      }
    } catch (FacebookApiException $e) {
      FbcmdException($e);
    }
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  if ((!file_exists($fbcmdAuthFileName))&&file_exists($fbcmdOldKeyFileName)) {
    ConvertOldKeyFile();
  }

////////////////////////////////////////////////////////////////////////////////

  if (!file_exists($fbcmdAuthFileName)) {
    ShowAuth();
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  $fbcmdAuthInfo = LoadDataFile('authfile');
  if (!isset($fbcmdAuthInfo['access_token'])) {
    FbcmdFatalError("Could not obtain oauth access_token");
  }
  $facebook->setAccessToken($fbcmdAuthInfo['access_token']);

////////////////////////////////////////////////////////////////////////////////

  $allPermissions = 'ads_management,create_event,email,friends_about_me,friends_actions.music,friends_actions.news,friends_actions.video,friends_activities,friends_birthday,friends_checkins,friends_education_history,friends_events,friends_games_activity,friends_groups,friends_hometown,friends_interests,friends_likes,friends_location,friends_notes,friends_online_presence,friends_photos,friends_questions,friends_relationship_details,friends_relationships,friends_religion_politics,friends_status,friends_subscriptions,friends_videos,friends_website,friends_work_history,manage_friendlists,manage_notifications,manage_pages,offline_access,publish_actions,publish_checkins,publish_stream,read_friendlists,read_insights,read_mailbox,read_requests,read_stream,rsvp_event,user_about_me,user_actions.music,user_actions.news,user_actions.video,user_activities,user_birthday,user_checkins,user_education_history,user_events,user_games_activity,user_groups,user_hometown,user_interests,user_likes,user_location,user_notes,user_online_presence,user_photos,user_questions,user_relationship_details,user_relationships,user_religion_politics,user_status,user_subscriptions,user_videos,user_website,user_work_history,xmpp_login';

  if ($fbcmdCommand == 'ADDPERM') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_addperm']);
    if (strtoupper($fbcmdParams[1]) == 'ALL') {
      $fbcmdParams[1] = $allPermissions;
    }
    $url = "{$urlAccess}&scope={$fbcmdParams[1]}";
    LaunchBrowser($url);

    print "This command should launch a browser to grant permissions.\n";
    print "in case it doesn't, here is the messy url:\n\n{$url}\n\n";
    print "(note: this grants only you access to your information and nobody else)\n\n";
    print "after granting permssions, execute: fbcmd test\n\n\n";
    return;
  }

////////////////////////////////////////////////////////////////////////////////

  $fbcmdRefCache = LoadDataFile('cachefile','cache_refs');
  AutoRefresh();
  $fbcmdPrev = LoadDataFile('prevfile','prev_save');
  $fbcmdLast = LoadDataFile('lastfile','last_save');

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'AS') {
    ValidateParamCount(2,99);
    SetDefaultParam(1,$fbcmdPrefs['default_as_obj']);
    $asId = $fbcmdParams[1];
    RemoveParams(0,1);
    if (!in_array($fbcmdCommand,$asCommands)) {
      FbcmdFatalError("AS does not support the command {$fbcmdCommand}.\nSupported commands: " . implode(',',$asCommands));
    }
    $newtoken = '';
    if (Resolve($asId,true,'number,prev,alias,accounts,username')) {
      try {
        $fbReturn = $facebook->api('/me/accounts');
      } catch (FacebookApiException $e) {
        FbcmdException($e);
      }
      if (isset($fbReturn['data'])) {
        foreach ($fbReturn['data'] as $a) {
          if ((isset($a['id']))&&(isset($a['access_token']))) {
            if ($a['id'] == $resolvedId) {
              $newtoken = $a['access_token'];
            }
          }
        }
      }
    }
    if ($newtoken) {
      $facebook->setAccessToken($newtoken);
    } else {
      FbcmdFatalError("could not get access_token for {$asId}");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LOOP') {
    print "Dave hasn't implemented LOOP yet... but it will be cool!\n";
    //2 ensure doesn't try to work with included commands
    exit;
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'COUNT') {
    print "Dave hasn't implemented COUNT yet... but it will be cool!\n";
    //2 ensure doesn't try to work with included commands
    exit;
  }

////////////////////////////////////////////////////////////////////////////////

  $fbcmdTargetId = 'me';
  $fbcmdExtraOutput = array();

  if ($fbcmdCommand == 'TARGET') {
    ValidateParamCount(2,99);
    SetDefaultParam(1,$fbcmdPrefs['default_target_obj']);
    $target = $fbcmdParams[1];
    RemoveParams(0,1);
    if (!in_array($fbcmdCommand,$targetCommands)) {
      FbcmdFatalError("TARGET does not support the command {$fbcmdCommand}.\nSupported commands: " . implode(',',$targetCommands));
    }
    if (Resolve($target,true)) {
      $fbcmdTargetId = $resolvedId;
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == '') {
    ShowUsage();
  }

////////////////////////////////////////////////////////////////////////////////

  // GLOBAL FQL strings for FLISTS

  //1
  // $fqlFriendId = "SELECT uid2 FROM friend WHERE uid1={$fbUser} AND uid2=uid2";
  // $fqlFriendBaseInfo = "SELECT uid,first_name,last_name,name,username,birthday_date,online_presence,status FROM user WHERE uid IN (SELECT uid2 FROM #fqlFriendId) OR uid={$fbUser}";
  // $keyFriendBaseInfo = 'uid';
  // $fqlFriendListNames = "SELECT flid,name FROM friendlist WHERE owner={$fbUser}";
  // $keyFriendListNames = 'flid';
  // $fqlFriendListMembers = "SELECT flid,uid FROM friendlist_member WHERE flid IN (SELECT flid FROM #fqlFriendListNames)";
  // $fqlPageId = "SELECT page_id FROM page_fan WHERE uid={$fbUser}";
  // $fqlPageNames = "SELECT page_id,name,username FROM page WHERE page_id IN (SELECT page_id FROM #fqlPageId)";
  // $keyPageNames = 'page_id';
  // $fqlGroupNames = "SELECT gid,name FROM group WHERE gid IN (SELECT gid FROM group_member WHERE uid={$fbUser})";
  // $keyGroupNames = 'gid';

  // $OLD_FlistMatchArray = array();
  // $OLD_FlistMatchIdString = '';

////////////////////////////////////////////////////////////////////////////////

  if (isset($fbcmd_include_newCommands)) {
    foreach ($fbcmd_include_newCommands as $c) {
      if ($fbcmdCommand == $c[0]) {
        return;
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ACCOUNTS') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/accounts");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no accounts');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ADDALBUM') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_addalbum_name']);
    SetDefaultParam(2,$fbcmdPrefs['default_addalbum_message']);

    $fbcmdExtraOutput['index'] = 'lastalbum';
    OpenGraphAPI("/me/albums",'POST',array('name' => $fbcmdParams[1], 'message' => $fbcmdParams[2]));
    if (isset($fbReturn['id'])) {
      NewLast('album', $fbReturn['id'], $fbcmdParams[1]);
    } else {
      FbcmdWarning('no return ID');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ADDPIC') {
    ValidateParamCount(1,3);
    SetDefaultParam(1,$fbcmdPrefs['default_addpic_filename']);
    SetDefaultParam(2,$fbcmdPrefs['default_addpic_albumid']);
    SetDefaultParam(3,$fbcmdPrefs['default_addpic_message']);
    if (!file_exists($fbcmdParams[1])) {
      FbcmdFatalError("Could not find file {$fbcmdParams[1]}");
    }
    $albumId = GetAlbumId($fbcmdParams[2]);
    $fbcmdExtraOutput['index'] = 'lastpic';
    OpenGraphAPI("/{$albumId}/photos",'POST',array('source' => '@' . $fbcmdParams[1], 'message' => $fbcmdParams[3]));
    if (isset($fbReturn['id'])) {
      NewLast('pic', $fbReturn['id'], "[{$fbcmdParams[1]}] {$fbcmdParams[3]}");
      if (isset($fbReturn['post_id'])) {
        newLast('picpost', $fbReturn['post_id'], "[{$fbcmdParams[1]}] {$fbcmdParams[3]}");
      }
    } else {
      FbcmdWarning('no return ID');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ADDPICD') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_addpicd_dirname']);
    SetDefaultParam(2,$fbcmdPrefs['default_addpicd_albumid']);
    $fileList = FileMatches($fbcmdParams[1],$fbcmdPrefs['addpicd_ext']);
    $albumId = GetAlbumId($fbcmdParams[2]);
    if (count($fileList) > 0) {
      foreach ($fileList as $fileName) {
        $fbcmdExtraOutput['filename'] = $fileName;
        OpenGraphAPI("/{$albumId}/photos",'POST',array('source' => '@' . $fileName));
        if (!isset($fbReturn['id'])) {
          FbcmdWarning('no return ID [$fileName]');
        }
      }
    } else {
      FbcmdWarning('no files found');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ALBUMS') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/albums");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no albums');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'ALIAS') {
    ValidateParamCount(array(0,2));
    if (ParamCount() == 0) {
      $fbReturn = $fbcmdAlias;
      ProcessReturn();
      PrintReturn();
    } else {
      if (Resolve($fbcmdParams[2],true)) {
        $fbcmdAlias[$fbcmdParams[1]] = $resolvedId;
        print "{$fbcmdParams[1]} == {$resolvedId}  ({$resolvedText})\n";
        SaveAliasFile();
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'APICS') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_apics_albumid']);
    SetDefaultParam(2,$fbcmdPrefs['default_apics_savedir']);
    $albumId = GetAlbumId($fbcmdParams[1]);
    OpenGraphAPI("/{$albumId}/photos");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no album pics');
    }
    if ($fbcmdParams[2]) {
      if ((isset($fbReturn['data']))&&(is_array($fbReturn['data']))) {
        foreach ($fbReturn['data'] as $pic) {
          SavePhoto(PhotoSrc($pic),$pic,$albumId,'0',$fbcmdParams[2],$fbcmdPrefs['apics_filename']);
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'COMMENT') {
    ValidateParamCount(2);
    SetDefaultParam(2,$fbcmdPrefs['default_comment_message']);
    if (Resolve($fbcmdParams[1],true,'number,prev,alias,last')) {
      $fbcmdExtraOutput['index'] = 'lastcomment';
      OpenGraphAPI("/{$resolvedId}/comments",'POST',array('message' => $fbcmdParams[2]));
      if (isset($fbReturn['id'])) {
        NewLast('comment', $fbReturn['id'], $fbcmdParams[2]);
      } else {
        FbcmdWarning("no return id");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'DEL') {
    ValidateParamCount(1);
    if (Resolve($fbcmdParams[1],true,'number,prev,alias,last,albums')) {
      OpenGraphAPI("/{$resolvedId}",'DELETE');
      if (!$fbReturn) {
        FbcmdWarning("did not delete");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'EVENTS') { //1
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,time());
    // $eventAttend = OLD_ProcessEventMask($fbcmdPrefs['events_attend_mask']);
    // $fqlEventMember = "SELECT eid,rsvp_status FROM event_member WHERE uid={$fbUser} AND rsvp_status IN ({$eventAttend})";
    // $keyEventMember = 'eid';
    // $fqlEvent = "SELECT eid,name,start_time FROM event WHERE eid IN (SELECT eid FROM #fqlEventMember) AND start_time > {$fbcmdParams[1]} ORDER BY start_time";
    // OLD_MultiFQL(array('EventMember','Event'));
    // if (!empty($dataEvent)) {
      // OLD_PrintHeader(OLD_PrintIfPref('event_save','[#]'),'START_TIME','RSVP','EVENT');
      // $eventNum = 0;
      // foreach ($dataEvent as $event) {
        // $eventNum++;
        // OLD_PrintRow(OLD_PrintIfPref('event_save','[' . $eventNum . ']'),date($fbcmdPrefs['event_dateformat'],$event['start_time']),$indexEventMember[$event['eid']]['rsvp_status'],$event['name']);
      // }
      // OLD_SaveEventData($dataEvent);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'FINBOX') { //1
    // ValidateParamCount(1);
    // OLD_GetFlistIds($fbcmdParams[1],true);
    // $matchInRecipients = "('" . implode("' in recipients OR '",$OLD_FlistMatchArray) . "' in recipients)";
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,$fbcmdPrefs['default_inbox_count']);
    // $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE (folder_id = 0 OR folder_id = 1 OR folder_id = 4) and $matchInRecipients";
    // $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
    // $keyMessageNames = 'id';
    // OLD_MultiFQL(array('Thread','MessageNames'));
    // if (!empty($dataThread)) {
      // OLD_PrintFolderHeader();
      // $threadNum = 0;
      // foreach ($dataThread as $t) {
        // OLD_PrintFolderObject(++$threadNum,$t);
      // }
      // OLD_SaveMailData($dataThread);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'FLAST') { //1
    // ValidateParamCount(1,2);
    // SetDefaultParam(1,$fbcmdPrefs['default_flast_flist']);
    // SetDefaultParam(2,$fbcmdPrefs['default_flast_count']);
    // OLD_GetFlistIds($fbcmdParams[1],true);
    // OLD_PrintHeader(OLD_PrintIfPref('show_id','ID'),'NAME',OLD_PrintIfPref('status_show_date','TIME'),'STATUS');
    // do {
      // $curChunkIds = OLD_GetNextChunkIds();
      // if ($curChunkIds) {
        // $results = OLD_MultiFQLById($curChunkIds,"SELECT uid,message,time FROM status WHERE uid=[id] ORDER BY time DESC LIMIT {$fbcmdParams[2]}");
        // foreach ($curChunkIds as $id) {
          // if ($results[$id]) {
            // foreach ($results[$id] as $status) {
              // OLD_PrintRow(OLD_PrintIfPref('show_id',$id),OLD_ProfileName($id),OLD_PrintIfPref('status_show_date',date($fbcmdPrefs['status_dateformat'],$status['time'])),$status['message']);
            // }
          // }
        // }
      // }
    // } while ($curChunkIds);
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'FONLINE') { //1
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,$fbcmdPrefs['default_fonline_flist']);
    // OLD_GetFlistIds($fbcmdParams[1]);
    // OLD_PrintHeader(OLD_PrintIfPref('show_id','ID'),'NAME','ONLINE_PRESENCE');
    // foreach ($OLD_FlistMatchArray as $id) {
      // if (($indexFriendBaseInfo[$id]['online_presence'] == 'active')||(($indexFriendBaseInfo[$id]['online_presence'] == 'idle')&&($fbcmdPrefs['online_idle']))) {
        // OLD_PrintRow(OLD_PrintIfPref('show_id',$id),OLD_ProfileName($id),$indexFriendBaseInfo[$id]['online_presence']);
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'FQL') { //1
    // ValidateParamCount(1,2);
    // $fql = $fbcmdParams[1];
    // $fql = str_replace('[me]', $fbUser, $fql);
    // SetDefaultParam(2,'');
    // if ($fbcmdParams[2]) {
      // OLD_GetFlistIds($fbcmdParams[2]);
      // $fql = str_replace('[flist]', "({$OLD_FlistMatchIdString})", $fql);
    // }
    // try {
      // $fbReturn = $fbObject->api_client->fql_query($fql);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // if (!empty($fbReturn)) {
      // OLD_PrintHeader('INDEX','FIELD','VALUE');
      // for ($i = 0; $i < count($fbReturn); $i++) {
        // OLD_PrintRecursiveObject(array($i+1),'',$fbReturn[$i]);
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'FRIENDS') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/friends");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no friends :(');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'FSTATUS') { //1
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,$fbcmdPrefs['default_fstatus_flist']);
    // OLD_GetFlistIds($fbcmdParams[1]);
    // $header = array();
    // OLD_PrintHeader(OLD_PrintIfPref('show_id','ID'),'NAME',OLD_PrintIfPref('status_show_date','TIME'),'STATUS');
    // foreach ($OLD_FlistMatchArray as $id) {
      // $status = $indexFriendBaseInfo[$id]['status'];
      // if ($status) {
        // if ($status['message']) {
          // OLD_PrintRow(OLD_PrintIfPref('show_id',$id),OLD_ProfileName($id),OLD_PrintIfPref('status_show_date',date($fbcmdPrefs['status_dateformat'],$status['time'])),$status['message']);
        // } else {
          // if ($fbcmdPrefs['print_blanks']) {
            // OLD_PrintRow(OLD_PrintIfPref('show_id',$id),OLD_ProfileName($id),OLD_PrintIfPref('status_show_date',''),'[blank]');
          // }
        // }
      // } else {
        // if ($fbcmdPrefs['print_blanks']) {
          // OLD_PrintRow(OLD_PrintIfPref('show_id',$id),OLD_ProfileName($id),OLD_PrintIfPref('status_show_date',''),'[n/a]');
        // }
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'FSTREAM') { //1
    // ValidateParamCount(0,2);
    // SetDefaultParam(1,$fbcmdPrefs['default_fstream_flist']);
    // SetDefaultParam(2,$fbcmdPrefs['default_fstream_count']);
    // OLD_GetFlistIds($fbcmdParams[1],true);
    // if (strtoupper($fbcmdParams[2]) == 'NEW') {
      // OLD_CheckStreamTimeStamp();
      // $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id IN ({$OLD_FlistMatchIdString}) AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    // } else {
      // $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id IN ({$OLD_FlistMatchIdString}) LIMIT {$fbcmdParams[2]}";
    // }
    // $queries = array('Stream');
    // if ($fbcmdPrefs['stream_show_comments']) {
      // $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id, comments.comment_list.fromid FROM #fqlStream)';
      // $keyStreamNames = 'id';
      // $queries[] = 'StreamNames';
    // }
    // OLD_MultiFQL($queries);
    // if (!empty($dataStream)) {
      // OLD_PrintPostHeader();
      // $postNum = 0;
      // foreach ($dataStream as $a) {
        // OLD_PrintPostObject(++$postNum,$a);
      // }
      // OLD_SavePostData($dataStream);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'FULLPOST') { //1
    // ValidateParamCount(1);
    // $curPostId = OLD_GetPostId($fbcmdParams[1],true);
    // if ($curPostId) {
      // $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE post_id='{$curPostId}'";
      // $fqlComments = "SELECT fromid, time, text FROM comment WHERE post_id='{$curPostId}' ORDER BY time";
      // $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id FROM #fqlStream) OR id IN (SELECT fromid FROM #fqlComments)';
      // $keyStreamNames = 'id';
      // OLD_MultiFQL(array('Stream','Comments','StreamNames'));
      // if (!empty($dataStream)) {
        // OLD_PrintPostHeader();
        // OLD_PrintPostObject($fbcmdParams[1],$dataStream[0],$dataComments);
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'GO') {
    ValidateParamCount(1);
    if (Resolve($fbcmdParams[1],true)) {
      $go = $resolvedId;
      if (strpos($go,'_') !== false) {
        $go = substr($go,strpos($go,'_')+1);
        if (strpos($go,'_') !== false) {
          $go = substr($go,0,strpos($go,'_'));
        }
      }
      LaunchBrowser("http://www.facebook.com/{$go}");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'GRAPHAPI') {
    ValidateParamCount(1,3);
    SetDefaultParam(2,$fbcmdPrefs['default_graphapi_method']);
    SetDefaultParam(3,$fbcmdPrefs['default_graphapi_params']);
    if ($fbcmdParams[3]) {
      $code = "\$args = " . $fbcmdParams[3] . ";";
      if (eval($code) === false) {
        FbcmdFatalError("bad params syntax\n");
      }
      eval($code);
    } else {
      $args = '';
    }
    OpenGraphAPI($fbcmdParams[1],$fbcmdParams[2],$args);
    if ($fbReturnType == 'array') {
      ReturnDataToPrev();
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'GROUPS') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/groups");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no groups');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'INBOX') { //1
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,$fbcmdPrefs['default_inbox_count']);
    // if (strtoupper($fbcmdParams[1]) == 'UNREAD') {
      // $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 0 AND unread > 0";
    // } else {
      // if (strtoupper($fbcmdParams[1]) == 'NEW') {
        // OLD_CheckMailTimeStamp();
        // $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 0 AND updated_time > {$lastMailData['timestamp']}";
      // } else {
        // $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 0 LIMIT {$fbcmdParams[1]}";
      // }
    // }
    // $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
    // $keyMessageNames = 'id';
    // OLD_MultiFQL(array('Thread','MessageNames'));
    // if (!empty($dataThread)) {
      // OLD_PrintFolderHeader();
      // $threadNum = 0;
      // foreach ($dataThread as $t) {
        // OLD_PrintFolderObject(++$threadNum,$t);
      // }
      // OLD_SaveMailData($dataThread);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'INFO') {
    ValidateParamCount(1,2);
    $obj = $fbcmdParams[1];
    SetDefaultParam(2,'');
    if (Resolve($fbcmdParams[1],false)) {
      $obj = $resolvedId;
    }
    $args = array();
    if ($fbcmdParams[2]) {
      $args = array('fields' => $fbcmdParams[2]);
    }
    OpenGraphAPI($obj,'GET',$args);
    if ($fbReturnType == 'array') {
      ReturnDataToPrev();
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LAST') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,0);
    PrintLast($fbcmdParams[1]);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LIKE') {
    ValidateParamCount(1);
    if (Resolve($fbcmdParams[1],true,'number,prev,alias,last')) {
      OpenGraphAPI("/{$resolvedId}/likes",'POST');
      if (!$fbReturn) {
        FbcmdWarning("did not like");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LIKES') {
    ValidateParamCount(0,1);
    if (ParamCount() == 0) {
      OpenGraphAPI("/{$fbcmdTargetId}/likes");
    } else {
      OpenGraphAPI("/{$fbcmdTargetId}/$fbcmdParams[1]");
    }
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no likes');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'LINKS') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/links");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no links');
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
    OpenGraphAPI("/{$fbcmdTargetId}/notes",'POST',array('message' => $fbFbmlFile, 'subject' => $fbcmdParams[1]));
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'MATCH') {
    ValidateParamCount(1);
    if (Resolve($fbcmdParams[1],true)) {
      NewLast('match', $resolvedId, $resolvedText);
      $fbReturn = array('index' => 'lastmatch', 'id' => $resolvedId, 'name' => $resolvedText);
      ProcessReturn();
      PrintReturn();
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'MSG') { //1
    // ValidateParamCount(1);
    // $curThreadId = OLD_GetThreadId($fbcmdParams[1]);
    // if ($curThreadId) {
      // $fqlThread = "SELECT subject,recipients,message_count,snippet,unread FROM thread WHERE thread_id = {$curThreadId}";
      // $fqlMessage = "SELECT message_id,thread_id,author_id,body,created_time,attachment,viewer_id FROM message WHERE thread_id = {$curThreadId}";
      // $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
      // $keyMessageNames = 'id';
      // OLD_MultiFQL(array('Thread','Message','MessageNames'));
      // if (!empty($dataMessage)) {
        // OLD_PrintHeader(OLD_PrintIfPref('show_id','USER_ID'),'FROM',OLD_PrintIfPref('msg_show_date','DATE'),'MESSAGE');
        // if ($fbcmdPrefs['msg_blankrow']) {
          // OLD_PrintRow('');
        // }
        // if ($dataThread[0]['subject'] != '') {
          // OLD_PrintRow(OLD_PrintIfPref('show_id',''),'Subject',OLD_PrintIfPref('msg_show_date',''),$dataThread[0]['subject']);
          // if ($fbcmdPrefs['msg_blankrow']) {
            // OLD_PrintRow('');
          // }
        // }
        // foreach ($dataMessage as $m) {
          // if ($m['created_time'] == '') { // note: the created_time field appears to be flakey
            // $displayDate = '';
          // } else {
            // $displayDate = date($fbcmdPrefs['msg_dateformat'],$m['created_time']);
          // }
          // OLD_PrintRow(OLD_PrintIfPref('show_id',$m['author_id']),OLD_ProfileName($m['author_id']),OLD_PrintIfPref('msg_show_date',$displayDate),$m['body']);
          // if ($fbcmdPrefs['msg_blankrow']) {
            // OLD_PrintRow('');
          // }
        // }
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'MUTUAL') {
    ValidateParamCount(1);
    if (Resolve($fbcmdParams[1],true,'number,prev,alias,username,friends')) {
      OpenGraphAPI("/{$fbcmdTargetId}/mutualfriends/{$resolvedId}");
      if (!ReturnDataToPrev()) {
        FbcmdWarning('no friends');
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'MYWALL') { //1
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,$fbcmdPrefs['default_mywall_count']);
    // if (strtoupper($fbcmdParams[1]) == 'NEW') {
      // OLD_CheckStreamTimeStamp();
      // $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id={$fbUser} AND target_id={$fbUser} AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    // } else {
      // $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE source_id={$fbUser} AND target_id={$fbUser} LIMIT {$fbcmdParams[1]}";
    // }
    // $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id FROM #fqlStream)';
    // $keyStreamNames = 'id';
    // OLD_MultiFQL(array('Stream','StreamNames'));
    // if (!empty($dataStream)) {
      // OLD_PrintHeader(OLD_PrintIfPref('stream_save','[#]'),OLD_PrintIfPref('stream_show_postid','POST_ID'),OLD_PrintIfPref('show_id','UID'),'NAME',OLD_PrintIfPref('stream_show_date','DATE'),'MESSAGE');
      // if ($fbcmdPrefs['stream_blankrow']) {
        // OLD_PrintRow('');
      // }
      // $postNum = 0;
      // foreach ($dataStream as $a) {
        // $postNum++;
        // OLD_PrintRow(OLD_PrintIfPref('stream_save','[' . $postNum . ']'),OLD_PrintIfPref('stream_show_postid',$a['post_id']),OLD_PrintIfPref('show_id',$a['actor_id']),OLD_ProfileName($a['actor_id']),OLD_PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$a['created_time'])),$a['message']);
        // if ($fbcmdPrefs['stream_blankrow']) {
          // OLD_PrintRow('');
        // }
      // }
      // OLD_SavePostData($dataStream);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'NEWS') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/home");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no news feed');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'NOTES') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/notes");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no notes');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'NOTICES') { //1
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,$fbcmdPrefs['default_notices_type']);
    // if ((strtoupper($fbcmdParams[1]) == 'UNREAD')||(strtoupper($fbcmdParams[1]) == 'MARKREAD')) {
      // $fqlNotification = "SELECT notification_id,sender_id,title_html,title_text,body_html,body_text,href,app_id,created_time,is_unread FROM notification WHERE recipient_id={$fbUser} AND is_hidden = 0 AND is_unread = 1";
    // } else {
      // $fqlNotification = "SELECT notification_id,sender_id,title_html,title_text,body_html,body_text,href,app_id,created_time,is_unread FROM notification WHERE recipient_id={$fbUser} AND is_hidden = 0";
    // }
    // $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT sender_id FROM #fqlNotification)';
    // $keyMessageNames = 'id';
    // $fqlApplicationNames = 'SELECT app_id,display_name FROM application WHERE app_id IN (SELECT app_id FROM #fqlNotification)';
    // $keyApplicationNames = 'app_id';
    // OLD_MultiFQL(array('Notification','MessageNames','ApplicationNames'));

    // if (!empty($dataNotification)) {
      // OLD_PrintNotificationHeader();
      // $threadNum = 0;
      // foreach ($dataNotification as $n) {
        // OLD_PrintNotificationObject(++$threadNum,$n);
      // }
      // if (strtoupper($fbcmdParams[1]) == 'MARKREAD') {
        // $unreadIds = array();
        // foreach ($dataNotification as $n) {
          // $unreadIds[] = $n['notification_id'];
        // }
        // if (count($unreadIds) > 0) {
          // $fbReturn = $fbObject->api_client->call_method('facebook.Notifications.markRead',array('notification_ids' => implode(',',$unreadIds)));
          // TraceReturn();
        // }
      // }
      // OLD_SaveNoticeData($dataNotification);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'NOTIFY') { //1
    // ValidateParamCount(0);
    // try {
      // $fbReturn = $fbObject->api_client->notifications_get();
      // TraceReturn();
    // } catch (Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // OLD_PrintHeader('FIELD','VALUE');
    // OLD_PrintRow('MESSAGES_UNREAD',$fbReturn['messages']['unread']);
    // OLD_PrintRow('POKES',$fbReturn['pokes']['unread']);
    // OLD_PrintRow('SHARES_UNREAD',$fbReturn['shares']['unread']);
    // $fqlNotifyFriends = 'SELECT uid,name FROM user WHERE uid in (' . array_implode_safe(',',$fbReturn['friend_requests']) . ')';
    // $fqlNotifyGroups = 'SELECT gid,name FROM group WHERE gid in (' . array_implode_safe(',',$fbReturn['group_invites']) . ')';
    // $fqlNotifyEvents = 'SELECT eid,name FROM event WHERE eid in (' . array_implode_safe(',',$fbReturn['event_invites']) . ')';
    // OLD_MultiFQL(array('NotifyFriends','NotifyGroups','NotifyEvents'));
    // OLD_NotifyHelper($fbReturn['friend_requests'],$dataNotifyFriends,'FRIEND','REQUESTS');
    // OLD_NotifyHelper($fbReturn['group_invites'],$dataNotifyGroups,'GROUP','INVITES');
    // OLD_NotifyHelper($fbReturn['event_invites'],$dataNotifyEvents,'EVENT','INVITES');
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'OPICS') { //1 can only be done by fql ?
    // ValidateParamCount(1,2);
    // SetDefaultParam(1,$fbcmdPrefs['default_opics_flist']);
    // SetDefaultParam(2,$fbcmdPrefs['default_opics_savedir']);
    // OLD_GetFlistIds($fbcmdParams[1],true);
    // OLD_PrintHeader(OLD_PrintIfPref('show_id','ID'),'NAME',OLD_PrintIfPref('pic_show_albumid','AID'),'PID',OLD_PrintIfPref('pic_show_date','CREATED'),'CAPTION',OLD_PrintIfPref('pic_show_links','LINK'),OLD_PrintIfPref('pic_show_src','SRC'));
    // do {
      // $curChunkIds = OLD_GetNextChunkIds();
      // if ($curChunkIds) {
        // $results = OLD_MultiFQLById($curChunkIds,"SELECT pid,aid,owner,src_small,src_big,src,link,caption,created FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner=[id])");
        // foreach ($curChunkIds as $id) {
          // if ($results[$id]) {
            // foreach ($results[$id] as $pic) {
              // OLD_PrintRow(OLD_PrintIfPref('show_id',$id),OLD_ProfileName($id),OLD_PrintIfPref('pic_show_albumid',$pic['aid']),$pic['pid'],OLD_PrintIfPref('pic_show_date',date($fbcmdPrefs['pic_dateformat'],$pic['created'])),$pic['caption'],OLD_PrintIfPref('pic_show_links',$pic['link']),OLD_PrintIfPref('pic_show_src',PhotoSrc($pic)));
              // if ($fbcmdParams[2]) {
                // SavePhoto(PhotoSrc($pic),$pic,'',$fbcmdParams[2],$fbcmdPrefs['opics_filename']);
              // }
            // }
          // }
        // }
      // }
    // } while ($curChunkIds);
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POST') {
    ValidateParamCount(1,10);
    $args = array();
    $firstParam = strtoupper($fbcmdParams[1]);
    if (strtoupper($fbcmdParams[1]) == 'MP3') {
      FbcmdFatalError("2.0: POST MP3 not done yet / not supported");
    }
    if (strtoupper($fbcmdParams[1]) == 'SRC') {
      ValidateParamCount(2,9);
      SetDefaultParam(2,$fbcmdPrefs['default_post_src_url']);
      $args['source'] = $fbcmdParams[2];
      RemoveParams(1,2);
    }
    if (strtoupper($fbcmdParams[1]) == 'IMG') {
      ValidateParamCount(2,7);
      SetDefaultParam(2,$fbcmdPrefs['default_post_img_url']);
      $args['picture'] = $fbcmdParams[2];
      RemoveParams(1,2);
    }
    ValidateParamCount(1,5);
    SetDefaultParam(1,$fbcmdPrefs['default_post_message']); //2 todo: tagging ?
    SetDefaultParam(2, $fbcmdPrefs['default_post_link']);
    SetDefaultParam(3, $fbcmdPrefs['default_post_name']);
    SetDefaultParam(4, $fbcmdPrefs['default_post_caption']);
    SetDefaultParam(5, $fbcmdPrefs['default_post_description']);

    $args['message'] = $fbcmdParams[1];
    if ($fbcmdParams[2]) $args['link'] = $fbcmdParams[3];
    if ($fbcmdParams[3]) $args['name'] = $fbcmdParams[2];
    if ($fbcmdParams[4]) $args['caption'] = $fbcmdParams[4];
    if ($fbcmdParams[5]) $args['description'] = $fbcmdParams[5];

    $fbcmdExtraOutput['index']  = 'lastpost';
    OpenGraphAPI("/{$fbcmdTargetId}/feed",'POST',$args);
    if (isset($fbReturn['id'])) {
      NewLast('post', $fbReturn['id'], $fbcmdParams[1]);
    } else {
      FbcmdWarning("no return id");
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POSTS') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/posts");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no posts');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POSTLINK') {
    ValidateParamCount(1,2);
    SetDefaultParam(1,$fbcmdPrefs['default_postlink_link']);
    SetDefaultParam(2,$fbcmdPrefs['default_postlink_message']);
    OpenGraphAPI("/{$fbcmdTargetId}/links",'POST',array('link' => $fbcmdParams[1], 'message' => $fbcmdParams[2]));
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'POSTNOTE') {
    ValidateParamCount(2);
    SetDefaultParam(1,$fbcmdPrefs['default_postnote_title']);
    SetDefaultParam(2,$fbcmdPrefs['default_postnote_body']);
    OpenGraphAPI("/{$fbcmdTargetId}/notes",'POST',array('message' => $fbcmdParams[2], 'subject' => $fbcmdParams[1]));
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'PPICS') { //1
    // ValidateParamCount(0,2);
    // SetDefaultParam(1,$fbcmdPrefs['default_ppics_flist']);
    // SetDefaultParam(2,$fbcmdPrefs['default_ppics_savedir']);
    // OLD_GetFlistIds($fbcmdParams[1]);
    // $fql = "SELECT uid,pic,pic_big,pic_small,pic_square FROM user WHERE uid IN ({$OLD_FlistMatchIdString}) ORDER BY last_name";
    // try {
      // $fbReturn = $fbObject->api_client->fql_query($fql);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // if (!empty($fbReturn)) {
      // OLD_PrintHeader(OLD_PrintIfPref('show_id','UID'),'NAME','SRC');
      // foreach ($fbReturn as $user) {
        // if ((OLD_UserPhotoSrc($user))||($fbcmdPrefs['print_blanks'])) {
          // OLD_PrintRow(OLD_PrintIfPref('show_id',$user['uid']),OLD_ProfileName($user['uid']),OLD_UserPhotoSrc($user));
        // }
        // if (($fbcmdParams[2])&&(OLD_UserPhotoSrc($user))) {
          // SavePhoto(OLD_UserPhotoSrc($user),null,$user['uid'],$fbcmdParams[2],$fbcmdPrefs['ppics_filename'],false);
        // }
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'RECENT') { //1
    // ValidateParamCount(0,2);
    // SetDefaultParam(1,$fbcmdPrefs['default_recent_flist']);
    // SetDefaultParam(2,$fbcmdPrefs['default_recent_count']);
    // OLD_GetFlistIds($fbcmdParams[1],true);
    // $fql = "SELECT uid,message,time FROM status WHERE uid in ({$OLD_FlistMatchIdString}) ORDER BY time DESC LIMIT {$fbcmdParams[2]}";
    // try {
      // $fbReturn = $fbObject->api_client->fql_query($fql);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // if ($fbReturn) {
      // OLD_PrintHeader(OLD_PrintIfPref('show_id','ID'),'NAME',OLD_PrintIfPref('status_show_date','TIME'),'STATUS');
      // foreach ($fbReturn as $status) {
        // OLD_PrintRow(OLD_PrintIfPref('show_id',$status['uid']),OLD_ProfileName($status['uid']),OLD_PrintIfPref('status_show_date',date($fbcmdPrefs['status_dateformat'],$status['time'])),$status['message']);
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'PREV') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,0);
    PrintPrev($fbcmdParams[1]);
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'REFRESH') {
    ValidateParamCount(0);
    BuildRefCache();
  }

  ////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'RESTATUS') { //1
    // ValidateParamCount(1);
    // OLD_GetCurrentStatus();
    // if ($userStatus != '') {
      // $fql = "SELECT post_id,comments.count FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} AND attachment=='' LIMIT 1";
      // try {
        // $fbReturn = $fbObject->api_client->fql_query($fql);
        // TraceReturn();
      // } catch(Exception $e) {
        // OLD_FbcmdException($e,'GET-POST');
      // }
      // if (isset($fbReturn[0]['post_id'])) {
        // $postID = $fbReturn[0]['post_id'];
      // } else {
        // FbcmdFatalError("RESTATUS: Could not retrieve previous status post_id");
      // }
      // $deletePost = true;
      // if ($fbcmdPrefs['restatus_comment_new']) {
        // if (isset($fbReturn[0]['comments']['count'])) {
          // if ($fbReturn[0]['comments']['count'] > 0) {
            // $deletePost = false;
          // }
        // } else {
          // FbcmdWarning ("Can not retreive comment count for post_id = {$p}");
        // }
      // }
      // if ($deletePost) {
        // try {
          // $fbReturn = $fbObject->api_client->stream_remove($postID);
          // TraceReturn();
        // } catch (Exception $e) {
          // OLD_FbcmdException($e);
        // }
        // if (!$fbReturn) {
          // FbcmdFatalError("RESTATUS: Could not remove previous status");
        // }
      // }
    // }
    // if ($fbcmdPrefs['status_tag']) {
      // $statusText = OLD_TagText($fbcmdParams[1]);
    // } else {
      // $statusText = $fbcmdParams[1];
    // }
    // try {
      // $fbReturn = $fbObject->api_client->call_method('facebook.users.setStatus',array('status' => $statusText,'status_includes_verb' => true));
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'RSVP') { //1
    // ValidateParamCount(2);
    // $eid = OLD_GetEventId($fbcmdParams[1]);
    // $rsvp = $fbcmdParams[2];
    // if (strtoupper($rsvp) == 'YES') {
      // $rsvp = 'attending';
    // }
    // if (strtoupper($rsvp) == 'NO') {
      // $rsvp = 'declined';
    // }
    // if (strtoupper($rsvp) == 'MAYBE') {
      // $rsvp = 'unsure';
    // }
    // try {
      // $fbReturn = $fbObject->api_client->events_rsvp($eid,$rsvp);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'SENTMAIL') { //1
    // ValidateParamCount(0,1);
    // SetDefaultParam(1,$fbcmdPrefs['default_sentmail_count']);
    // if (strtoupper($fbcmdParams[1]) == 'UNREAD') {
      // $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 1 AND unread > 0";
    // } else {
      // if (strtoupper($fbcmdParams[1]) == 'NEW') {
        // OLD_CheckMailTimeStamp();
        // $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 1 AND updated_time > {$lastMailData['timestamp']}";
      // } else {
        // $fqlThread = "SELECT thread_id,folder_id,subject,recipients,updated_time,parent_message_id,parent_thread_id,message_count,snippet,snippet_author,object_id,unread,viewer_id FROM thread WHERE folder_id = 1 LIMIT {$fbcmdParams[1]}";
      // }
    // }
    // $fqlMessageNames = 'SELECT id,name FROM profile WHERE id IN (SELECT recipients FROM #fqlThread)';
    // $keyMessageNames = 'id';
    // OLD_MultiFQL(array('Thread','MessageNames'));
    // if (!empty($dataThread)) {
      // OLD_PrintFolderHeader();
      // $threadNum = 0;
      // foreach ($dataThread as $t) {
        // OLD_PrintFolderObject(++$threadNum,$t);
      // }
      // OLD_SaveMailData($dataThread);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'SFILTERS') { //1
    // ValidateParamCount(0);
    // $fql = "SELECT filter_key,name,rank,type FROM stream_filter WHERE uid={$fbUser} ORDER BY rank";
    // try {
      // $fbReturn = $fbObject->api_client->fql_query($fql);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // if ($fbReturn) {
      // OLD_PrintHeader('KEY','RANK','NAME','TYPE');
      // foreach ($fbReturn as $filter) {
        // OLD_PrintRow($filter['filter_key'],$filter['rank']+1,$filter['name'],$filter['type']);
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'SHOWPERM') {
    ValidateParamCount(0);
    OpenGraphAPI('/me/permissions');
  }

///////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'SHOWPREF') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_showpref_defaults']);
    $fbReturn = array();
    foreach ($fbcmdPrefs as $k => $v) {
      if ($k != 'prefs') {
        if (($fbcmdParams[1])||(
          (substr($k,0,8) != 'default_')&&
          (substr($k,0,13) != 'output_format')&&
          (substr($k,0,11) != 'output_show')&&
          (substr($k,0,10) != 'output_col')&&
          (substr($k,0,10) != 'output_rec'))) {
            $fbReturn[$k] = var_export($v,true);
        }
      }
    }
    ProcessReturn();
    PrintReturn();
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'STATUS') { //2 just set for now
    ValidateParamCount(1);
    $fbcmdExtraOutput['index'] = 'laststatus';
    OpenGraphAPI("/{$fbcmdTargetId}/feed",'POST',array('message' => $fbcmdParams[1]));
    if (isset($fbReturn['id'])) {
      NewLast('status', $fbReturn['id'], $fbcmdParams[1]);
    } else {
      FbcmdWarning("no return id");
    }
  }
    // if (ParamCount() == 0) {
      // OLD_GetCurrentStatus();
      // if ($userStatus == 'unknown_status') {
        // FbcmdFatalError("STATUS: unknown_status:\n  have you granted permission to read your status? try: fbcmd addperm");
      // } else {
        // if ($userStatus == '') {
          // print "$userName [BLANK]\n";
        // } else {
          // print "$userName $userStatus\n";
        // }
      // }
    // } else {
      // if ($fbcmdPrefs['status_tag']) {
        // $statusText = OLD_TagText($fbcmdParams[1]);
      // } else {
        // $statusText = $fbcmdParams[1];
      // }
      // try {
        // $fbReturn = $fbObject->api_client->call_method('facebook.users.setStatus',array('status' => $statusText,'status_includes_verb' => true));
        // TraceReturn();
      // } catch(Exception $e) {
        // OLD_FbcmdException($e);
      // }
    // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'STATUSES') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/statuses");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no statuses');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'STREAM') { //1
    // ValidateParamCount(0,2);
    // SetDefaultParam(1,$fbcmdPrefs['default_stream_filter']);
    // SetDefaultParam(2,$fbcmdPrefs['default_stream_count']);
    // if (is_numeric($fbcmdParams[1])) {
      // if ($fbcmdParams[1] > 0) {
        // $fbcmdParams[1] -= 1;
      // }
      // $filterKeyQuery = "SELECT filter_key FROM stream_filter WHERE uid={$fbUser} AND rank={$fbcmdParams[1]}";
    // } else {
      // if (substr($fbcmdParams[1],0,1) == $fbcmdPrefs['prefix_filter']) {
        // $filterKeyQuery = "'" . OLD_GetFilterByName($fbcmdParams[1]) . "'";
      // } else {
        // $filterKeyQuery = "'{$fbcmdParams[1]}'";
      // }
    // }
    // if (strtoupper($fbcmdParams[2]) == 'NEW') {
      // OLD_CheckStreamTimeStamp();
      // $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE filter_key IN ({$filterKeyQuery}) AND {$fbcmdPrefs['stream_new_from']} > {$lastPostData['timestamp']}";
    // } else {
      // $fqlStream = "SELECT post_id,viewer_id,app_id,source_id,created_time,updated_time,actor_id,target_id,message,app_data,attachment,comments,likes,permalink FROM stream WHERE filter_key IN ({$filterKeyQuery}) LIMIT {$fbcmdParams[2]}";
    // }
    // $fqlStreamNames = 'SELECT id,name FROM profile WHERE id IN (SELECT actor_id, target_id, comments.comment_list.fromid FROM #fqlStream)';
    // $keyStreamNames = 'id';
    // OLD_MultiFQL(array('Stream','StreamNames'));
    // if (!empty($dataStream)) {
      // OLD_PrintPostHeader();
      // $postNum = 0;
      // foreach ($dataStream as $a) {
        // OLD_PrintPostObject(++$postNum,$a);
      // }
      // OLD_SavePostData($dataStream);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  // if ($fbcmdCommand == 'TAGPIC') { //1
    // ValidateParamCount(array(2,4));
    // SetDefaultParam(1,$fbcmdPrefs['default_tagpic_pid']);
    // SetDefaultParam(2,$fbcmdPrefs['default_tagpic_target']);
    // SetDefaultParam(3,$fbcmdPrefs['default_tagpic_x']);
    // SetDefaultParam(4,$fbcmdPrefs['default_tagpic_y']);
    // $tagId = null;
    // $OLD_TagText = $fbcmdParams[2];
    // if (strtoupper($OLD_TagText) == '=ME') {
      // $tagId = $fbUser;
      // $OLD_TagText = null;
    // } else {
      // if (is_numeric($OLD_TagText)) {
        // $tagId = $OLD_TagText;
        // $OLD_TagText = null;
      // } else {
        // OLD_MultiFQL(array('FriendId','FriendBaseInfo'));
        // foreach ($dataFriendBaseInfo as $friend) {
          // if (strtoupper($OLD_TagText) == strtoupper($friend['name'])) {
            // $tagId = $friend['uid'];
            // $OLD_TagText = null;
            // break;
          // }
        // }
      // }
    // }
    // try {
      // $fbReturn = $fbObject->api_client->photos_addTag($fbcmdParams[1],$tagId,$OLD_TagText,$fbcmdParams[3],$fbcmdParams[4],null,null);
      // TraceReturn();
    // } catch (Exception $e) {
      // OLD_FbcmdException($e);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'TEST') { //2 add more tests
    $testPost = "474872245874123_476522569042424";
    try {
      $fbReturn = $facebook->api('/me');
      TraceReturn();
      $testName = "UNKNOWN";
      if (isset($fbReturn['name'])) {
        $testName = $fbReturn['name'];
        print "Test 1 PASSED. Determine your name: Hello, {$testName}\n";
      } else {
        print "Test 1 FAILED. Could not determine your name :(\n";
      }
      $fbReturn = $facebook->api("/{$testPost}/likes",'POST');
      TraceReturn();
      if ($fbReturn == 1) {
        print "Test 2 PASSED. Liked the test post\n";
      } else {
        print "Test 2 FAILED. Could not like the test post\n";
      }
      $fbReturn = $facebook->api('/me/feed','POST', array (
        'message' => 'just successfully installed fbcmd',
        'name' => 'fbcmd (Command Line Interface for Facebook)',
        'link' => 'http://www.facebook.com/cmdlinepage',
        'caption' => 'Command Line Interface for Facebook',
        'description' => 'fbcmd is an open source facebook application.  The project home page is at http://fbcmd.dtompkins.com',
        'picture' => "http://fbcmd.dtompkins.com/attachments/fbcmd75.png"));
      TraceReturn();
      if (isset($fbReturn['id'])) {
        NewLast('post', $fbReturn['id'], 'just successfully installed fbcmd');
        print "Test 3 PASSED. Posted an installation message on your wall\n";
        print "\n If you want to delete the message on your wall, execute:\n  fbcmd del lastpost\n\n";
      } else {
        print "Test 3 FAILED. Could not posted an installation message on your wall\n";
      }
    } catch (FacebookApiException $e) {
      FbcmdException($e);
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'TPICS') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['default_tpics_savedir']);
    OpenGraphAPI("/{$fbcmdTargetId}/photos");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no tagged pics');
    }
    if ($fbcmdParams[1]) {
      if ((isset($fbReturn['data']))&&(is_array($fbReturn['data']))) {
        foreach ($fbReturn['data'] as $pic) {
          SavePhoto(PhotoSrc($pic),$pic,'0',$fbcmdTargetId,$fbcmdParams[1],$fbcmdPrefs['tpics_filename']);
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'UNLIKE') {
    ValidateParamCount(1);
    if (Resolve($fbcmdParams[1],true,'number,prev,alias,last')) {
      OpenGraphAPI("/{$resolvedId}/likes",'DELETE');
      if (!$fbReturn) {
        FbcmdWarning("did not unlike");
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'VERSION') {
    ValidateParamCount(0,1);
    SetDefaultParam(1,$fbcmdPrefs['update_branch']);
    $fbReturn['LOCAL_VERSION'] = $fbcmdVersion;
    $fbReturn['ONLINE_VERSION'] = GetGithubVersion($fbcmdParams[1]);
    $fbReturn['UPDATE_BRANCH'] = $fbcmdPrefs['update_branch'];
    ProcessReturn();
    PrintReturn();
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'WALL') {
    ValidateParamCount(0);
    OpenGraphAPI("/{$fbcmdTargetId}/feed");
    if (!ReturnDataToPrev()) {
      FbcmdWarning('no wall posts');
    }
  }

////////////////////////////////////////////////////////////////////////////////

  if ($fbcmdCommand == 'WHOAMI') {
    ValidateParamCount(0);
    OpenGraphAPI('/me');
    if (!isset($fbReturn['id'])) {
      FbcmdWarning("no id");
    }
  }

////////////////////////////////////////////////////////////////////////////////

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

  function AddPrev($id, $name) {
    global $fbcmdPrev;
    $fbcmdPrev[0][] = array('id' => $id, 'name' => $name);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function AddExtraOutput($arr) {
    global $fbcmdExtraOutput;
    return array_merge($fbcmdExtraOutput,$arr);
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
////////////////////////////////////////////////////////////////////////////////

  function array_push_unique(&$array,$var) {
    if (!in_array($var,$array)) {
      $array[] = $var;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function AutoRefresh() {
    global $fbcmdPrefs;
    global $fbcmdRefCache;
    if (($fbcmdPrefs['cachefile'])&&($fbcmdPrefs['auto_refresh'])) {
      if ((isset($fbcmdRefCache['timestamp']))&&(isset($fbcmdRefCache['friends']))) {
        if (time() - $fbcmdRefCache['timestamp'] > $fbcmdPrefs['auto_refresh']) {
          BuildRefCache();
        } elseif (count($fbcmdRefCache['friends']) == 0) {
          BuildRefCache();
        }
      } else {
        BuildRefCache();
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function BuildRefCache() {
    global $fbcmdRefCache;
    $fbcmdRefCache = array();
    $fbcmdRefCache['timestamp'] = time();
    $fbcmdRefCache['username'] = array();
    GetRefArray('accounts','/me/accounts',true);
    GetRefArray('friends','/me/friends',true);
    GetRefArray('friendlists','/me/friendlists');
    GetRefArray('likes','/me/likes',true);
    GetRefArray('groups','/me/groups');
    GetRefArray('albums','/me/albums');
    SaveDataFile('cachefile',$fbcmdRefCache,'cache_refs');
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

  function ConvertOldKeyFile() { // CONVERT OLD 1.x key file to 2.0 auth file
    global $fbcmdPrefs;
    global $fbcmdOldKeyFileName;
    global $fbcmdAuthFileName;
    global $fbcmdUserSessionKey;
    global $fbcmdUserSecretKey;
    global $fbcmdAuthVersion;
    global $fbcmdAuthInfo;
    print "\nIt appears you have a fbcmd 1.x key file...\n\n";
    $fbcmdUserSessionKey = 'EMPTY';
    $fbcmdUserSecretKey = 'EMPTY';
    $fbcmdKeyFile = file($fbcmdOldKeyFileName,FILE_IGNORE_NEW_LINES);
    if (count($fbcmdKeyFile) >= 2) {
      $fbcmdUserSessionKey = $fbcmdKeyFile[0];
      $fbcmdUserSecretKey = $fbcmdKeyFile[1];
    }
    if (strncmp($fbcmdUserSessionKey,'EMPTY',5) == 0) {
      print "But it's invalid or empty, so I'll delete it\n\n";
      if (!unlink($fbcmdOldKeyFileName)) {
        FbcmdFatalError("Could not delete key file\n");
      }
      ShowAuth();
      return;
    }
    $fbcmdBackupKeyFileName = "{$fbcmdOldKeyFileName}_old";
    print "backing up {$fbcmdOldKeyFileName} -> {$fbcmdBackupKeyFileName} ...\n\n";
    if (!rename($fbcmdOldKeyFileName,$fbcmdBackupKeyFileName)) {
      FbcmdFatalError("Could not rename key file\n");
    }
    print "Generating new Auth token...\n\n";
    GenAuthInfoFromSessionKey();
    print "Saving new Auth file: {$fbcmdAuthFileName} ...\n\n";
    SaveDataFile('authfile',$fbcmdAuthInfo);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function CurlPost($url,$postfields) {
    $ch = curl_init();
    curl_setopt_array ($ch, Facebook::$CURL_OPTS);
    curl_setopt_array ($ch, array(
      CURLOPT_URL => $url,
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => $postfields,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => false
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    if ($result) {
      $ret = json_decode($result,true);
      TraceReturn($ret);
      return $ret;
    }
    FbcmdFatalError("Failed CURL POST:\n$url\n$postfields\n");
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdException(Exception $e, $defaultCommand = true) {
    if ($defaultCommand) {
      global $fbcmdCommand;
      $defaultCommand = $fbcmdCommand;
    }
    $result = $e->getResult();
    $type = $e->getType();
    $code = $e->getCode();
    $msg = $e->getMessage();
    if (isset($result['error']['code'])) {
      $code = $result['error']['code'];
    }
    if (isset($result['error']['message'])) {
      $msg = $result['error']['message'];
    }
    FbcmdFatalError("{$defaultCommand}\n[{$type}:{$code}] {$msg}");
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdFatalError($err) {
    global $fbcmdVersion;
    print "fbcmd [v{$fbcmdVersion}] ERROR: {$err}";
    exit;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function FbcmdWarning($err) {
    global $fbcmdVersion;
    print "fbcmd [v{$fbcmdVersion}] WARNING: {$err}\n";
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

  // function OLD_FlistMatch($flistItem,$isPrefixed,$dataArray,$keyId,$keyMatch,$allowMultipleMatches = true, $forceExactMatch = false) {

    // $matchList = array();
    // $displayMatch = array();
    // $isExact = false;

    // if ($isPrefixed) {
      // $matchString = substr($flistItem,1);
    // } else {
      // $matchString = $flistItem;
    // }
    // $matchStringUC = strtoupper($matchString);
    // if ($matchString == '') {
      // FbcmdWarning("Could not match empty flist entry");
      // return array();
    // }
    // // Check for Exact Match
    // foreach ($dataArray as $element) {
      // if ($matchStringUC == strtoupper($element[$keyId])) {
        // $matchList[] = $matchString;
        // $displayMatch[] = $element[$keyMatch];
        // $isExact = true;
      // }
      // if ($matchStringUC == strtoupper($element[$keyMatch])) {
        // $matchList[] = $element[$keyId];
        // $displayMatch[] = $element[$keyMatch];
        // $isExact = true;
      // }
    // }
    // // now match for imperfect matches, including regular expressions
    // if ((!$isExact)&&(!$forceExactMatch)) {
      // foreach ($dataArray as $element) {
        // if (preg_match("/{$matchString}/i",$element[$keyMatch])) {
          // $matchList[] = $element[$keyId];
          // $displayMatch[] = $element[$keyMatch];
        // }
      // }
    // }
    // if (count($matchList) == 0) {
      // if (is_numeric($matchString)) {
        // $matchList[] = $matchString;
      // } else {
        // FbcmdWarning("Could not match entry: {$flistItem}");
        // return array();
      // }
    // }
    // if ((count($matchList) > 1)&&(!$allowMultipleMatches)) {
      // global $fbcmdCommand;
      // FbcmdWarning("{$fbcmdCommand} does not allow Multiple Matches:");
      // print "flist entry {$flistItem} matched:\n";
      // foreach ($displayMatch as $item) {
        // print "{$item}\n";
      // }
      // return array();
    // }
    // return $matchList;
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GenAuthInfoFromSessionKey() {
    global $fbcmdPrefs;
    global $fbcmdUserSessionKey;
    global $fbcmdUserSecretKey;
    global $fbcmdAuthVersion;
    global $fbcmdAuthInfo;
    $uidFromSessionKey = end(explode("-",$fbcmdUserSessionKey));
    $result = CurlPost("https://graph.facebook.com/oauth/exchange_sessions","client_id={$fbcmdPrefs['appkey']}&client_secret={$fbcmdPrefs['appsecret']}&sessions={$fbcmdUserSessionKey}");
    if (isset($result[0]['access_token'])) {
      $authToken = $result[0]['access_token'];
    } else {
      FbcmdFatalError("could not convert session key to auth token");
    }
    $fbcmdAuthInfo = array (
      'version' => $fbcmdAuthVersion,
      'usersessionkey' => $fbcmdUserSessionKey,
      'usersecretkey' => $fbcmdUserSecretKey,
      'access_token' => $authToken,
      'uid' => $uidFromSessionKey,
    );
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetAlbumId($a) { //, $allowSpecial = false) {
    global $fbcmdTargetId;
    global $resolvedId;
   if (($a)&&(Resolve($a,true,'number,prev,alias,last,albums'))) {
      return $resolvedId;
    } else {
      return $fbcmdTargetId;
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetCommandPref($prefbase) {
    global $fbcmdPrefs;
    global $fbcmdCommand;
    if ($fbcmdPrefs[$prefbase] != '') {
      return $fbcmdPrefs[$prefbase];
    } else {
      return $fbcmdPrefs[$prefbase ."_" . strtolower($fbcmdCommand)];
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_GetCurrentStatus() {
    // global $fbUser;
    // global $fbObject;
    // global $userName;
    // global $userStatus;
    // $fql = "SELECT name,status FROM user WHERE uid={$fbUser}";
    // try {
      // $fbReturn = $fbObject->api_client->fql_query($fql);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // $userName = 'unknown_name';
    // $userStatus = 'unknown_status';
    // if ($fbReturn) {
      // if (isset($fbReturn[0]['name'])) {
        // $userName = $fbReturn[0]['name'];
      // }
      // if (isset($fbReturn[0]['status']['message'])) {
        // $userStatus = $fbReturn[0]['status']['message'];
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_GetEventId($a) {
    // global $lastEventData;
    // global $userStatus;
    // global $fbUser;
    // global $fbObject;

    // if ($a < 1001) {
      // $lastEventData = LoadDataFile('eventfile','event_save');
      // if (isset($lastEventData['ids'][$a])) {
        // return $lastEventData['ids'][$a];
      // } else {
        // FbcmdWarning ("Invalid Event ID: {$a}");
        // return false;
      // }
    // } else {
      // return $a;
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_GetFilterByName($filtName) {
    // global $fbUser;
    // global $fbObject;
    // $fql = "SELECT filter_key,name,rank,type FROM stream_filter WHERE uid={$fbUser} ORDER BY rank";
    // try {
      // $fbReturn = $fbObject->api_client->fql_query($fql);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // $matchFilterName = OLD_FlistMatch($filtName,true,$fbReturn,'filter_key','name',false);
    // if (count($matchFilterName) == 0) {
      // FbcmdFatalError("Could not resolve filter name {$filtName}");
    // } else {
      // return $matchFilterName[0];
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_GetFlistIds($flistString, $allowPages = false, $allowMultipleMatches = true, $failOnEmpty = true) {

    // global $fbcmdPrefs;
    // global $OLD_FlistMatchArray;
    // global $OLD_FlistMatchIdString;

    // $unknownNames = array();

    // $OLD_FlistMatchArray = array();

    // $flistFQL = array('FriendId','FriendBaseInfo');
    // $flistItems = explode(',',$flistString);

    // // Pre-process to see if Friend Lists or Pages or Groups are required
    // foreach ($flistItems as $item) {
      // if (substr($item,0,1) == $fbcmdPrefs['prefix_friendlist']) {
        // array_push_unique($flistFQL,'FriendListNames');
        // array_push_unique($flistFQL,'FriendListMembers');
      // }
      // if ((substr($item,0,1) == $fbcmdPrefs['prefix_page'])||(strtoupper($item)=='=PAGES')) {
        // array_push_unique($flistFQL,'PageId');
        // array_push_unique($flistFQL,'PageNames');
      // }
      // if (substr($item,0,1) == $fbcmdPrefs['prefix_group']) {
        // array_push_unique($flistFQL,'GroupNames');
      // }
      // if (substr($item,0,1) == $fbcmdPrefs['prefix_tag']) {
        // array_push_unique($flistFQL,'PageId');
        // array_push_unique($flistFQL,'PageNames');
        // array_push_unique($flistFQL,'GroupNames');
      // }
    // }

    // OLD_MultiFQL($flistFQL);

    // global $dataFriendId;
    // global $dataFriendBaseInfo;
    // global $indexFriendBaseInfo;
    // global $fbUser;
    // global $flistChunkCounter;
    // $flistChunkCounter = 0;

    // foreach ($flistItems as $item) {

      // $itemUC = strtoupper($item);

      // // =KEYWORDS /////////////////////////////////////////////////////////////

      // if (substr($item,0,1) == '=') {
        // if ($itemUC == '=ME') {
          // array_push_unique($OLD_FlistMatchArray,$fbUser);
          // continue;
        // }
        // if ($itemUC == '=ALL') {
          // foreach ($dataFriendId as $fid) {
            // array_push_unique($OLD_FlistMatchArray,$fid['uid2']);
          // }
          // continue;
        // }
        // if (substr($itemUC,0,5) == '=BDAY') {
          // $matchTime = time();
          // if(preg_match("/=BDAY\+(\d+)?$/",$itemUC,$matches)) {
            // if(isset($matches[1])) {
              // $matchTime += 24*60*60 * $matches[1];
            // } else {
              // $matchTime += 24*60*60;
            // }
          // }
          // if(preg_match("/=BDAY-(\d+)?$/",$itemUC,$matches)) {
            // if(isset($matches[1])) {
              // $matchTime -= 24*60*60 * $matches[1];
            // } else {
              // $matchTime -= 24*60*60;
            // }
          // }
          // if(preg_match("/=BDAY=(.+)$/",$itemUC,$matches)) {
            // $matchTime = strtotime($matches[1]);
            // if (!$matchTime) {
              // FbcmdWarning("Bad BDAY Syntax: [{$item}] using today");
              // $matchTime = time();
            // }
          // }
          // $matchDate = date('m/d',$matchTime);
          // foreach ($dataFriendBaseInfo as $fbi) {
            // if (substr($fbi['birthday_date'],0,5) == $matchDate) {
              // array_push_unique($OLD_FlistMatchArray,$fbi['uid']);
            // }
          // }
          // continue;
        // }
        // if ($itemUC == '=ONLINE') {
          // foreach ($dataFriendBaseInfo as $fbi) {
            // if (($fbi['online_presence'] == 'active')||(($fbi['online_presence'] == 'idle')&&($fbcmdPrefs['online_idle']))) {
              // array_push_unique($OLD_FlistMatchArray,$fbi['uid']);
            // }
          // }
          // continue;
        // }
        // if ($itemUC == '=PAGES') {
          // if (!$allowPages) {
            // global $fbcmdCommand;
            // FbcmdWarning("{$fbcmdCommand} does not support pages: {$item} ignored");
          // } else {
            // global $dataPageId;
            // foreach ($dataPageId as $page_id) {
              // array_push_unique($OLD_FlistMatchArray,$page_id['page_id']);
            // }
          // }
          // continue;
        // }
        // FbcmdWarning("Unknown flist entry: {$item}");
        // continue;
      // }

      // // _FRIEND LIST //////////////////////////////////////////////////////////

      // if (substr($item,0,1) == $fbcmdPrefs['prefix_friendlist']) {
        // global $dataFriendListNames;
        // global $dataFriendListMembers;
        // $flidMatches = OLD_FlistMatch($item,true,$dataFriendListNames,'flid','name',$allowMultipleMatches);
        // if (count($flidMatches)) {
          // foreach ($dataFriendListMembers as $flm) {
            // // http://bugs.developers.facebook.com/show_bug.cgi?id=5977
            // // if (in_array($flm[0],$flidMatches)) {
              // // array_push_unique($OLD_FlistMatchArray,$flm[1]);
            // // }
            // if (in_array($flm['flid'],$flidMatches)) {
              // array_push_unique($OLD_FlistMatchArray,$flm['uid']);
            // }
          // }
        // }
        // continue;
      // }

      // // !USERNAME /////////////////////////////////////////////////////////////

      // if (substr($item,0,1) == $fbcmdPrefs['prefix_username']) {
        // $uidMatches = OLD_FlistMatch($item,true,$dataFriendBaseInfo,'uid','username',$allowMultipleMatches);
        // array_merge_unique($OLD_FlistMatchArray,$uidMatches);
        // continue;
      // }

      // // +PAGES ////////////////////////////////////////////////////////////////

      // if (substr($item,0,1) == $fbcmdPrefs['prefix_page']) {
        // if (!$allowPages) {
          // global $fbcmdCommand;
          // FbcmdWarning("{$fbcmdCommand} does not support pages: {$item} ignored");
        // } else {
          // global $dataPageNames;
          // $pidMatches = OLD_FlistMatch($item,true,$dataPageNames,'page_id','name',$allowMultipleMatches);
          // array_merge_unique($OLD_FlistMatchArray,$pidMatches);
        // }
        // continue;
      // }

      // // ~GROUPS ///////////////////////////////////////////////////////////////

      // if (substr($item,0,1) == $fbcmdPrefs['prefix_group']) {
        // global $dataGroupNames;
        // global $fbObject;
        // $gidMatches = OLD_FlistMatch($item,true,$dataGroupNames,'gid','name',false);
        // if (isset($gidMatches[0])) {
          // $fql = "SELECT uid FROM group_member WHERE gid={$gidMatches[0]}";
          // try {
            // $fbReturn = $fbObject->api_client->fql_query($fql);
            // TraceReturn();
          // } catch(Exception $e) {
            // OLD_FbcmdException($e);
          // }
          // if (!empty($fbReturn)) {
            // foreach ($fbReturn as $u) {
              // $OLD_FlistMatchArray[] = $u['uid'];
            // }
          // } else {
            // FbcmdWarning("Could Not get Group Members for GROUP {$gidMatches[0]}");
          // }
        // }
        // continue;
      // }

      // // @TAG FORMAT ///////////////////////////////////////////////////////////

      // if (substr($item,0,1) == $fbcmdPrefs['prefix_tag']) {
        // $tagList = OLD_MatchTag(substr($item,1),$allowPages,false);
        // if ($tagList) {
          // array_merge_unique($OLD_FlistMatchArray,array($tagList[0][0]));
        // }
        // continue;
      // }

      // // REGULAR NAMES /////////////////////////////////////////////////////////

      // $uidMatches = OLD_FlistMatch($item,false,$dataFriendBaseInfo,'uid','name',$allowMultipleMatches);
      // array_merge_unique($OLD_FlistMatchArray,$uidMatches);
    // }
    // if (count($OLD_FlistMatchArray) == 0) {
      // if ($failOnEmpty) {
        // if (substr(strtoupper($flistString),0,5) == '=BDAY') {
          // print "No Friends With Birthday Matches\n";
          // exit;
        // } else {
          // FbcmdFatalError("Empty flist: {$flistString}");
        // }
      // } else {
        // $OLD_FlistMatchIdString = '';
      // }
    // } else {
      // $OLD_FlistMatchIdString = implode(',',$OLD_FlistMatchArray);
    // }

    // foreach ($OLD_FlistMatchArray as $id) {
      // if (OLD_ProfileName($id) == 'unknown') {
        // $unknownNames[] = $id;
      // }
    // }
    // if (count($unknownNames) > 0) {
      // global $fqlFlistNames;
      // global $keyFlistNames;
      // $fqlFlistNames = 'SELECT id,name FROM profile WHERE id IN (' . implode(',',$unknownNames) . ')';
      // $keyFlistNames = 'id';
      // OLD_MultiFQL(array('FlistNames'));
    // }
    // return;
  // }

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

  // function OLD_GetNextChunkIds() {
    // global $fbcmdPrefs;
    // global $flistChunkCounter;
    // global $OLD_FlistMatchArray;

    // if ($flistChunkCounter == -1) {
      // return null;
    // }
    // if ($fbcmdPrefs['flist_chunksize']) {
      // $startPos = $flistChunkCounter * $fbcmdPrefs['flist_chunksize'];
      // $flistChunkCounter++;
      // $len = $fbcmdPrefs['flist_chunksize'];
      // if ($startPos + $len >= count($OLD_FlistMatchArray)) {
        // $flistChunkCounter = -1;
        // $len = count($OLD_FlistMatchArray) - $startPos;
      // }
      // return array_slice($OLD_FlistMatchArray,$startPos,$len);
    // } else {
      // $flistChunkCounter = -1;
      // return $OLD_FlistMatchArray;
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetOldSessionKey($authcode) {
    global $fbcmdPrefs;
    global $fbcmdUserSessionKey;
    global $fbcmdUserSecretKey;

    $api_url = "http://api.facebook.com/restserver.php";

    $api_key = "api_key={$fbcmdPrefs['appkey']}";
    $auth_token = "auth_token={$authcode}";
    $call_id = "call_id=" . microtime(true);
    $format = "format=json-strings";
    $generate_session_secret = "generate_session_secret=";
    $method = "method=facebook.auth.getSession";
    $session_key = "session_key=";
    $v = "v=1.0";

    $sig = "sig=" . md5("{$api_key}{$auth_token}{$call_id}{$format}{$generate_session_secret}{$method}{$session_key}{$v}{$fbcmdPrefs['appsecret']}");

    $url = "{$api_url}?{$method}&{$format}&{$session_key}&{$api_key}&{$v}";
    $poststring = "{$auth_token}&{$generate_session_secret}&{$call_id}&{$sig}";

    $result = CurlPost($url,$poststring);
    if (isset($result['session_key'])&&(isset($result['session_key']))) {
      $fbcmdUserSessionKey = $result['session_key'];
      $fbcmdUserSecretKey = $result['secret'];
      return;
    } else {
      FbcmdFatalError("could not get session key from auth code");
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_GetPostId($p, $allowSpecial = false) {
    // global $lastPostData;
    // global $userStatus;
    // global $fbUser;
    // global $fbObject;

    // if (($p == 0)||(strtoupper($p) == 'LAST')||(strtoupper($p) == 'CURSTATUS')) {
      // if ($allowSpecial) {
        // if (strtoupper($p) == 'CURSTATUS') {
          // OLD_GetCurrentStatus();
          // if ($userStatus == '') {
            // FbcmdFatalError("CURSTATUS: Your status is blank");
          // }
          // $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} AND attachment=='' LIMIT 1";
        // } else {
          // $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} LIMIT 1";
        // }
        // try {
          // $fbReturn = $fbObject->api_client->fql_query($fql);
          // TraceReturn();
        // } catch(Exception $e) {
          // OLD_FbcmdException($e,'GET-POST');
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
      // if ($p < 1001) {
        // $lastPostData = LoadDataFile('postfile','stream_save');
        // if (isset($lastPostData['ids'][$p])) {
          // return $lastPostData['ids'][$p];
        // } else {
          // FbcmdWarning ("Invalid Post ID: {$p}");
          // return false;
        // }
      // } else {
        // return $p;
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function GetRefArray($refname, $apicall, $username = false, $fld = 'name') {
    global $facebook;
    global $fbcmdRefCache;
    $fbcmdRefCache[$refname] = array();
    //2 TODO: GET PAGING WORKING (SEEMS TO FAIL ON ACCOUNTS).. will only get 1st 5000 entries
    if ($username) {
      $args = array( 'fields' => 'id,name,username');
    } else {
      $args = array();
    }
    try {
      $fbReturn = $facebook->api($apicall,'GET',$args);
    } catch (FacebookApiException $e) {
      FbcmdException($e);
    }
    if (isset($fbReturn['data'])) {
      foreach ($fbReturn['data'] as $a) {
        if ((isset($a['id']))&&(isset($a[$fld]))) {
          $id = $a['id'];
          $name = $a[$fld];
          if (isset($fbcmdRefCache[$refname][$name])) {
            $j = 2;
            while (isset($fbcmdRefCache[$refname]["{$name} ({$j})"])) {
              $j++;
            }
            $name = "{$name} ({$j})";
          }
          $fbcmdRefCache[$refname][$name] = $id;
        } else {
          FbcmdWarning("Bad Entry [{$apicall}] " . var_export($a,true) . "\n");
        }
        if ($username) {
          if ((isset($a['id']))&&(isset($a['username']))) {
            if (!isset($fbcmdRefCache['username'][$a['username']])) {
              $fbcmdRefCache['username'][$a['username']] = $id;
            }
          }
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_GetThreadId($p) { //, $allowSpecial = false) {
    // global $lastMailData;
    // global $userStatus;
    // global $fbUser;
    // global $fbObject;

    // // if (($p == 0)||(strtoupper($p) == 'LAST')||(strtoupper($p) == 'CURSTATUS')) {
      // // if ($allowSpecial) {
        // // if (strtoupper($p) == 'CURSTATUS') {
          // // OLD_GetCurrentStatus();
          // // if ($userStatus == '') {
            // // FbcmdFatalError("CURSTATUS: Your status is blank");
          // // }
          // // $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} AND attachment=='' LIMIT 1";
        // // } else {
          // // $fql = "SELECT post_id FROM stream WHERE source_id={$fbUser} AND actor_id={$fbUser} LIMIT 1";
        // // }
        // // try {
          // // $fbReturn = $fbObject->api_client->fql_query($fql);
          // // TraceReturn();
        // // } catch(Exception $e) {
          // // OLD_FbcmdException($e,'GET-POST');
        // // }
        // // if (isset($fbReturn[0]['post_id'])) {
          // // return $fbReturn[0]['post_id'];
        // // } else {
          // // FbcmdFatalError("GETPOST: Could not retrieve post_id = {$p}");
        // // }
      // // } else {
        // // global $fbcmdCommand;
        // // FbcmdWarning ("{$fbcmdCommand} does not support post_id = {$p}");
      // // }
    // // } else {
      // if ($p < 1001) {
        // $lastMailData = LoadDataFile('mailfile','mail_save');
        // if (isset($lastMailData['ids'][$p])) {
          // return $lastMailData['ids'][$p];
        // } else {
          // FbcmdWarning ("Invalid Thread ID: {$p}");
          // return false;
        // }
      // } else {
        // return $p;
      // }
    // // }
  // }

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

  function LoadDataFile($prefFile, $prefSave = 1) {
    global $fbcmdPrefs;
    global $fbcmdBaseDir;
    $fileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs[$prefFile]);
    $loadData = array();
    if (($prefSave == 1)||($fbcmdPrefs[$prefSave])) {
      if (!file_exists($fileName)) {
        if ($prefSave == 1) {
          FbcmdWarning("Could not locate {$prefFile} [{$fileName}]");
        }
      } else {
        $contents = @file_get_contents($fileName);
        if ($contents == false) {
          FbcmdWarning("Could not read file {$fbcmdParams[1]}");
        }
        $loadData = unserialize($contents);
      }
    }
    return($loadData);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_MatchTag($tag, $allowPages = true, $allowGroups = true) {
    // global $fbcmdPrefs;
    // global $dataFriendBaseInfo;
    // global $dataPageNames;
    // global $dataGroupNames;
    // $matchOrder = explode(',',$fbcmdPrefs['status_tag_order']);
    // $matchList = array();
    // foreach ($matchOrder as $order) {
      // $matchParams = explode(':',$order);
      // if ($matchParams[0] == 'friends') {
        // $matchList = OLD_TagFieldMatch($tag, $dataFriendBaseInfo, $matchParams[1], 'uid', $matchParams[2]);
      // }
      // if (($matchParams[0] == 'pages')&&($allowPages)) {
        // $matchList = OLD_TagFieldMatch($tag, $dataPageNames, $matchParams[1], 'page_id', $matchParams[2]);
      // }
      // if (($matchParams[0] == 'groups')&&($allowGroups)) {
        // $matchList = OLD_TagFieldMatch($tag, $dataGroupNames, $matchParams[1], 'gid', $matchParams[2]);
      // }
      // if (count($matchList) > 0) {
        // break;
      // }
    // }
    // if (count($matchList) == 1) {
      // return ($matchList);
    // } else {
      // if (count($matchList) == 0) {
        // FbcmdWarning("Tag [{$tag}] had no matches");
      // } else {
        // FbcmdWarning("Tag [{$tag}] had multiple matches:");
        // foreach ($matchList as $item) {
          // if ($item[1] != $item[2]) {
            // print "  {$item[1]} ({$item[2]})\n";
          // } else {
            // print "  {$item[1]}\n";
          // }
        // }
      // }
      // return (false);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_MultiFQL($queryList) {

    // // This Function wraps the MultiQuery() API function in a non-obvious but convenient way:
    // //
    // // OLD_MultiFQL(array('Query1','Query2'))
    // //
    // // requires the global variables:
    // //
    // // $fqlQuery1 = "SELECT ...."
    // // $fqlQuery2 = "SELECT x FROM y WHERE x IN (SELECT z FROM #fqlQuery1)"
    // //
    // // and generates the global variables $dataQuery1 and $dataQuery2
    // // also, if $keyQuery1 is defined, then an associative array $indexQuery1 is generated

    // global $fbObject;

    // $queryStrings = array();
    // foreach ($queryList as $queryName) {
      // $queryStrings[] = '"fql' . $queryName . '":"' . $GLOBALS['fql' . $queryName] . '"';
    // }
    // try {
      // $fbOLD_MultiFQLReturn = $fbObject->api_client->fql_multiquery("{" . implode(',',$queryStrings) . "}");
      // TraceReturn($fbOLD_MultiFQLReturn);
    // } catch (Exception $e) {
      // OLD_FbcmdException($e,'OLD_MultiFQL');
    // }
    // if ($fbOLD_MultiFQLReturn) {
      // for ($i=0; $i < count($queryList); $i++) {
        // foreach ($fbOLD_MultiFQLReturn as $ret) {
          // if ($ret['name'] == 'fql' . $queryList[$i]) {
            // $GLOBALS['data' . $queryList[$i]] = $ret['fql_result_set'];
            // if (isset($GLOBALS['key' . $queryList[$i]])) {
              // $GLOBALS['index' . $queryList[$i]] = array();
              // if ((is_array($ret['fql_result_set']))&&(count($ret['fql_result_set'] > 0))) {
                // foreach ($ret['fql_result_set'] as $record) {
                  // $GLOBALS['index' . $queryList[$i]][$record[$GLOBALS['key' . $queryList[$i]]]] = $record;
                // }
              // }
            // }
          // }
        // }
      // }
    // } else {
      // FbcmdFatalError('Unexpected: OLD_MultiFQL Empty');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_MultiFQLById($idArray,$selectStatement) {
    // global $fbObject;
    // $queryStrings = array();
    // foreach ($idArray as $id) {
      // $queryStrings[] = '"fql' . $id . '":"' . str_replace('[id]', $id, $selectStatement). '"';
    // }
    // try {
      // $fbReturn = $fbObject->api_client->fql_multiquery("{" . implode(',',$queryStrings) . "}");
      // TraceReturn();
    // } catch (Exception $e) {
      // OLD_FbcmdException($e,'MULTI-FQL-ID');
    // }
    // $results = array();
    // if ($fbReturn) {
      // foreach ($fbReturn as $ret) {
        // if($ret['fql_result_set']) {
          // $id = substr($ret['name'],3);
          // $results[$id] = $ret['fql_result_set'];
        // }
      // }
    // }
    // foreach ($idArray as $id) {
      // if (!isset($results[$id])) {
        // $results[$id] = null;
      // }
    // }
    // return $results;
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function NewLast($type,$id,$text) {

    global $fbcmdPrefs;
    global $fbcmdLast;

    if ($fbcmdPrefs['last_save']) {
      if (!isset($fbcmdLast)) {
        $fbcmdLast = array();
      }
      if (!isset($fbcmdLast[$type])) {
        $fbcmdLast[$type] = array();
      }
      $k = $fbcmdPrefs['last_length'];
      while ($k > 0) {
        if (isset($fbcmdLast[$type][$k-1])) {
          $fbcmdLast[$type][$k] = $fbcmdLast[$type][$k-1];
        }
        $k--;
      }
      $fbcmdLast[$type][0] = array('id' => $id, 'name' => $text);
      SaveDataFile('lastfile',$fbcmdLast,'last_save');
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_NotifyHelper($notifyArray,$dataNotify,$baseType,$inviteType) {
    // if ($notifyArray) {
      // OLD_PrintRow($baseType . '_' . $inviteType,count($notifyArray));
      // for ($j=0; $j < count($dataNotify); $j++) {
        // OLD_PrintRow($baseType . '_'  . ($j+1), $dataNotify[$j]['name']);
      // }
    // } else {
      // OLD_PrintRow($baseType . '_' . $inviteType,'0');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function OpenGraphAPI($path, $method = 'GET', $params = '') {
    global $fbReturn;
    global $facebook;
    //todo auto-params for paging
    try {
      $fbReturn = $facebook->api($path, $method, $params);
      TraceReturn();
      ProcessReturn();
      PrintReturn();
    } catch (FacebookApiException $e) {
      FbcmdException($e);
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
          FbcmdWarning("Ignoring Parameter {$i}: Unknown Switch [{$switchKey}]\n");
        }
      } else {
        if ($fbcmdCommand == '') {
          if (substr($curArg,0,1) == '@') {
            $nextArg = substr($curArg,1);
            $curArg = 'TARGET';
          } else {
            $nextArg = '';
          }
          $fbcmdCommand = strtoupper($curArg);
          $fbcmdParams[] = $fbcmdCommand;
          if ($nextArg) {
            $fbcmdParams[] = $nextArg;
          }
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
    if (isset($obj['images'][$fbcmdPrefs['pic_size']]['source'])) {
      return $obj['images'][$fbcmdPrefs['pic_size']]['source'];
    } else {
      return $obj['source'];
    }
    // $fieldName = 'src_big';
    // if ($fbcmdPrefs['pic_size'] == 0) {
      // $fieldName = 'src_small';
    // }
    // if ($fbcmdPrefs['pic_size'] == 2) {
      // $fieldName = 'src';
    // }
    // if (isset($obj[$fieldName])) {
      // return $obj[$fieldName];
    // } else {
      // return '';
    // }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintAttachmentData($base,$post,$field,$display) {
    // global $fbcmdPrefs;
    // if (isset($post['attachment'][$field])) {
      // if ($post['attachment'][$field] != '') {
        // OLD_PrintRow($base,$display,htmlspecialchars_decode(strip_tags($post['attachment'][$field])));
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function preg_in_array($v,$arr) {
    if (in_array($v,$arr)) return true;
    foreach ($arr as $a) {
      if (preg_match('/^'.$a.'$/',$v)) return true;
    }
    return false;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintCsvRow($rowIn) {
    // global $fbcmdPrefs;
    // $rowOut = array();
    // foreach ($rowIn as $col) {
      // $bookend = false;
      // if (strpos($col,$fbcmdPrefs['csv_bookend'])) {
        // $col = str_replace($fbcmdPrefs['csv_bookend'],$fbcmdPrefs['csv_escaped_bookend'],$col);
        // $bookend = true;
      // }
      // if ((strpos($col,$fbcmdPrefs['csv_separator']))||($fbcmdPrefs['csv_force_bookends'])) {
        // $bookend = true;
      // }
      // if ($bookend) {
        // $col = $fbcmdPrefs['csv_bookend'] . $col . $fbcmdPrefs['csv_bookend'];
      // }
      // if ($fbcmdPrefs['print_linefeed_subst']) {
        // $col = str_replace("\n", $fbcmdPrefs['print_linefeed_subst'], $col);
      // }

      // $rowOut[] = $col;
    // }
    // print implode($fbcmdPrefs['csv_separator'],$rowOut) . "\n";
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintFinish() {
    // global $fbcmdPrefs;
    // global $printMatrix;
    // if ($fbcmdPrefs['print_csv']) {
      // return;
    // }
    // if (isset($printMatrix)) {
      // $columnWidth = array();
      // if (count($printMatrix) > 0) {
        // foreach ($printMatrix as $row) {
          // while (count($row) > count($columnWidth)) {
            // $columnWidth[] = 0;
          // }
          // for ($i=0; $i<count($row); $i++) {
            // if (strlen($row[$i])>$columnWidth[$i]) {
              // $columnWidth[$i]=strlen($row[$i]);
            // }
          // }
        // }
        // for ($i=0; $i<count($columnWidth)-1; $i++) {
          // $columnWidth[$i] += $fbcmdPrefs['print_col_padding'];
        // }

        // if ($fbcmdPrefs['print_wrap']) {
          // $consoleWidth = $fbcmdPrefs['print_wrap_width'];
          // if ($fbcmdPrefs['print_wrap_env_var']) {
            // if (getenv($fbcmdPrefs['print_wrap_env_var'])) {
              // $consoleWidth = getenv($fbcmdPrefs['print_wrap_env_var']);
            // }
          // }
          // $colToWrap = count($columnWidth) - 1;
          // $wrapWidth = $consoleWidth - array_sum($columnWidth) + $columnWidth[$colToWrap] - 1;
          // if ($wrapWidth < $fbcmdPrefs['print_wrap_min_width']) {
            // $wrapWidth = $columnWidth[$colToWrap]+1;
          // }
          // $backupMatrix = $printMatrix;
          // $printMatrix = array();
          // foreach ($backupMatrix as $row) {
            // if (isset($row[$colToWrap])) {
              // $rightCol = array_pop($row);
              // $wrapped = wordwrap($rightCol,$wrapWidth,"\n",$fbcmdPrefs['print_wrap_cut']);
              // $newRows = explode("\n",$wrapped);
              // foreach ($newRows as $nr) {
                // $addRow = $row;
                // array_push($addRow,$nr);
                // $printMatrix[] = OLD_CleanColumns($addRow);
              // }
            // } else {
              // $printMatrix[] = $row;
            // }
          // }
        // } else {
          // if ($fbcmdPrefs['print_linefeed_subst']) {
            // $colToWrap = count($columnWidth) - 1;
            // for ($j=0; $j < count($printMatrix); $j++) {
              // if (isset($printMatrix[$j][$colToWrap])) {
                // $printMatrix[$j][$colToWrap] = str_replace("\n", $fbcmdPrefs['print_linefeed_subst'], $printMatrix[$j][$colToWrap]);
              // }
            // }
          // }
        // }

        // foreach ($printMatrix as $row) {
          // for ($i=0; $i<count($row); $i++) {
            // if ($i < count($row)-1) {
              // print str_pad($row[$i], $columnWidth[$i], ' ');
            // } else {
              // print $row[$i];
            // }
          // }
          // print "\n";
        // }
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintFolderHeader() {
    // global $fbcmdPrefs;
    // $threadInfo = array();
    // if ($fbcmdPrefs['mail_save']) {
      // $threadInfo[] = '[#]';
    // }
    // if ($fbcmdPrefs['folder_show_threadid']) {
      // $threadInfo[] = 'THREAD_ID';
    // }
    // OLD_PrintHeader($threadInfo,'FIELD','VALUE');
    // if ($fbcmdPrefs['folder_blankrow']) {
      // OLD_PrintRow('');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintFolderObject($threadNum, $thread) {
    // global $fbcmdPrefs;
    // global $fbUser;

    // $threadInfo = array();
    // if ($fbcmdPrefs['mail_save']) {
      // $showThreadNum = '[' . $threadNum . ']';
      // if ($thread['unread']) {
        // $showThreadNum .= '*';
      // }
      // $threadInfo[] = $showThreadNum;
    // }
    // if ($fbcmdPrefs['folder_show_threadid']) {
      // $threadInfo[] = $thread['thread_id'];
    // }

    // $subjectShow = $thread['subject'];
    // if ($subjectShow == '') {
      // $subjectShow = '[no subject]';
    // }
    // OLD_PrintRow($threadInfo,'subject',$subjectShow);

    // $recipientsList = array();
    // foreach ($thread['recipients'] as $r) {
      // if ($r != $fbUser) {
        // $recipientsList[] = OLD_ProfileName($r);
      // }
    // }
    // $recipientsShow = implode(',',$recipientsList);
    // OLD_PrintRow($threadInfo,':to/from',$recipientsShow);


    // if ($fbcmdPrefs['folder_show_date']) {
      // OLD_PrintRow($threadInfo,':date', date($fbcmdPrefs['folder_dateformat'],$thread['updated_time']));
    // }


    // if ($fbcmdPrefs['folder_show_snippet']) {
      // $snippetShow = str_replace("\n", ' ', $thread['snippet']);
      // if (count($recipientsList) > 1) {
        // $snippetShow = OLD_ProfileName($thread['snippet_author']) . " :: " . $snippetShow;
      // }
      // OLD_PrintRow($threadInfo,':snippet', $snippetShow);
    // }

    // if ($fbcmdPrefs['folder_blankrow']) {
      // OLD_PrintRow('');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintHeader() {
    // global $fbcmdPrefs;
    // if ($fbcmdPrefs['print_header']) {
      // OLD_PrintRow(func_get_args());
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintHeaderQuiet() {
    // global $fbcmdPrefs;
    // if ($fbcmdPrefs['quiet']) {
      // return;
    // }
    // if ($fbcmdPrefs['print_header']) {
      // OLD_PrintRow(func_get_args());
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintIf($boolValue,$optVar) {
    // if ($boolValue) {
      // return $optVar;
    // } else {
      // return 'SKIP_COLUMN';
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintIfPref($paramName,$optVar) {
    // global $fbcmdPrefs;
    // return OLD_PrintIf($fbcmdPrefs[$paramName],$optVar);
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintNotificationHeader() {
    // global $fbcmdPrefs;
    // $header = array();
    // $header[] = '[#]';
    // $header[] = OLD_PrintIfPref('notices_show_id','NOTIFICATION_ID');
    // $header[] = 'SOURCE';
    // $header[] = 'FIELD';
    // $header[] = 'VALUE';
    // OLD_PrintHeader($header);
    // if ($fbcmdPrefs['notices_blankrow']) {
      // OLD_PrintRow('');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintNotificationObject($threadNum, $n) {
    // global $fbcmdPrefs;
    // global $fbUser;

    // $postInfo = array();
    // $postInfo[] = '[' . $threadNum . ']';
    // if ($n['is_unread']) {
      // $postInfo[0] .= '*';
    // }
    // if ($fbcmdPrefs['notices_show_id']) {
      // $postInfo[] = $n['notification_id'];
    // }
    // $prefix = '';
    // if ($n['sender_id'] != $fbUser) {
      // OLD_PrintRow($postInfo,OLD_ProfileName($n['app_id']),$prefix . 'from',OLD_ProfileName($n['sender_id']));
      // $prefix = ':';
    // }
    // if ($fbcmdPrefs['notices_show_date']) {
      // OLD_PrintRow($postInfo,OLD_ProfileName($n['app_id']),$prefix . 'date',date($fbcmdPrefs['notices_dateformat'],$n['created_time']));
      // $prefix = ':';
    // }
    // if ($n['title_text'] != '') {
      // OLD_PrintRow($postInfo,OLD_ProfileName($n['app_id']),$prefix . 'title',strip_tags($n['title_text']));
      // $prefix = ':';
    // }
    // if ($n['body_text'] != '') {
      // OLD_PrintRow($postInfo,OLD_ProfileName($n['app_id']),$prefix . 'body',strip_tags($n['body_text']));
    // }
    // if ($fbcmdPrefs['notices_blankrow']) {
      // OLD_PrintRow('');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintPostHeader() {
    // global $fbcmdPrefs;
    // $header = array();
    // $header[] = OLD_PrintIfPref('stream_save','[#]');
    // $header[] = OLD_PrintIfPref('stream_show_postid','POST_ID');
    // $header[] = OLD_PrintIfPref('show_id','SOURCE_UID');
    // $header[] = 'NAME';
    // $header[] = OLD_PrintIfPref('stream_show_date','TIME');
    // $header[] = 'TYPE';
    // $header[] = 'MESSAGE';
    // OLD_PrintHeader($header);
    // if ($fbcmdPrefs['stream_blankrow']) {
      // OLD_PrintRow('');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintPostObject($postNum, $post, $commentData = false) {

    // global $fbcmdPrefs;

    // $postInfo = array();

    // if ($fbcmdPrefs['stream_save']) {
      // $postInfo[] = '[' . $postNum . ']';
    // }

    // if ($fbcmdPrefs['stream_show_postid']) {
      // $postInfo[] = $post['post_id'];
    // }

    // $userInfo = array();
    // $userInfo[] = OLD_PrintIfPref('show_id',$post['actor_id']);

    // $userInfo[] = OLD_ProfileName($post['actor_id']);

    // $timeInfo = OLD_PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$post['created_time']));

    // if ($post['attachment']) {
      // $msgType = 'attach post';
      // if (isset($post['attachment']['media'][0])) {
        // if (isset($post['attachment']['media'][0]['type'])) {
          // $msgType = $post['attachment']['media'][0]['type'] . ' post';
        // }
      // }
    // } else {
      // if ($post['app_data']) {
        // $msgType = 'app post';
      // } else {
        // if ($post['target_id']) {
          // $msgType = 'wall post';
        // } else {
          // $msgType = 'status';
        // }
      // }
    // }

    // $msgShow = $post['message'];

    // if ($post['target_id']) {
      // $msgShow = '--> ' . OLD_ProfileName($post['target_id']) . ' :: ' . $post['message'];
    // } else {
      // $msgShow = $post['message'];
    // }
    // if ($msgShow == '') if (isset($post['attachment']['name'])) $msgShow = $post['attachment']['name'];
    // if ($msgShow == '') if (isset($post['attachment']['caption'])) $msgShow = $post['attachment']['caption'];
    // if ($msgShow == '') if (isset($post['attachment']['href'])) $msgShow = $post['attachment']['href'];
    // if ($msgShow == '') if (isset($post['attachment']['description'])) $msgShow = $post['attachment']['description'];

    // OLD_PrintRow($postInfo,$userInfo,$timeInfo,$msgType,$msgShow);

    // if ($fbcmdPrefs['stream_show_appdata']) {
      // if ($post['app_data'] != '') {
        // OLD_PrintRecursiveObject(array($postInfo,$userInfo,$timeInfo),':app',$post['app_data']);
      // }
    // }

    // if ($fbcmdPrefs['stream_show_attachments']) {
      // if ($post['attachment']) {
        // OLD_PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'name',':name');
        // OLD_PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'href',':link');
        // OLD_PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'caption',':caption');
        // OLD_PrintAttachmentData(array($postInfo,$userInfo,$timeInfo),$post,'description',':desc');
      // }
    // }

    // if ($fbcmdPrefs['stream_show_likes']) {
      // if (isset($post['likes']['count'])) {
        // if ($post['likes']['count'] > 0) {
          // if ($post['likes']['count'] == 1) {
            // $likesMessage = '1 person likes this.';
          // } else {
            // $likesMessage = "{$post['likes']['count']} people like this.";
          // }
          // if (isset($post['likes']['friends'])) {
            // if ((is_array($post['likes']['friends']))&&(count($post['likes']['friends']) > 0)) {
              // $likers = array();
              // foreach ($post['likes']['friends'] as $id) {
                // $likers[] = OLD_ProfileName($id);
              // }
              // $likesMessage = $likesMessage . ' (' . implode(',',$likers) . ')';
            // }
          // }
          // OLD_PrintRow($postInfo,$userInfo,$timeInfo,':likes',$likesMessage);
        // }
      // }
    // }

    // if ($commentData) {
      // $shownCount = count($commentData);
      // if (isset($post['comments']['count'])) {
        // $totalCount = $post['comments']['count'];
        // if ($shownCount < $totalCount) {
          // OLD_PrintRow($postInfo,$userInfo,$timeInfo,':comments',"Showing {$shownCount} of {$totalCount} Comments");
        // }
      // }
      // $commentCount = 0;
      // foreach ($commentData as $comment) {
        // $commentCount++;
        // $timeInfo = OLD_PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$comment['time']));
        // OLD_PrintRow($postInfo,$userInfo,$timeInfo,':comment' . $commentCount,OLD_ProfileName($comment['fromid']) . ' :: ' . $comment['text']);
      // }
    // } else {
      // if ($fbcmdPrefs['stream_show_comments']) {
        // if (isset($post['comments']['count'])) {
          // $totalCount = $post['comments']['count'];
          // if ($totalCount > 0) {
            // $shownCount = 0;
            // if (isset($post['comments']['comment_list'])) {
              // if (is_array($post['comments']['comment_list'])) {
                // $shownCount = count($post['comments']['comment_list']);
              // }
            // }
            // if ($shownCount == 0) {
              // OLD_PrintRow($postInfo,$userInfo,$timeInfo,':comments',"{$totalCount} Comments");
            // } else {
              // if ($shownCount < $totalCount) {
                // OLD_PrintRow($postInfo,$userInfo,$timeInfo,':comments',"Showing {$shownCount} of {$totalCount} Comments");
              // }
              // $commentCount = 0;
              // foreach ($post['comments']['comment_list'] as $comment) {
                // $commentCount++;
                // $timeInfo = OLD_PrintIfPref('stream_show_date',date($fbcmdPrefs['stream_dateformat'],$comment['time']));
                // OLD_PrintRow($postInfo,$userInfo,$timeInfo,':comment' . $commentCount,OLD_ProfileName($comment['fromid']) . ' :: ' . $comment['text']);
              // }
            // }
          // }
        // }
      // }
    // }
    // if ($fbcmdPrefs['stream_blankrow']) {
      // OLD_PrintRow('');
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

 // function OLD_PrintQuiet($msg) {
  // global $fbcmdPrefs;
  // if (!$fbcmdPrefs['quiet']) {
      // print $msg;
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

 // function OLD_PrintRow() {
    // global $fbcmdPrefs;
    // global $printMatrix;
    // $rowArray = OLD_array_flatten(func_get_args());
    // $columns = array();
    // foreach ($rowArray as $col) {
      // if (is_array($col)) {
        // foreach ($col as $c) {
          // if ($c != 'SKIP_COLUMN') {
            // $columns[] = $c;
          // }
        // }
      // } else {
        // if ($col != 'SKIP_COLUMN') {
          // $columns[] = $col;
        // }
      // }
    // }
    // $printColumns = OLD_CleanColumns($columns);
    // $printMatrix[] = $printColumns;

    // if ($fbcmdPrefs['print_csv']) {
      // OLD_PrintCsvRow($printColumns);
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintRowQuiet() {
    // global $fbcmdPrefs;
    // if ($fbcmdPrefs['quiet']) {
      // return;
    // }
    // OLD_PrintRow(func_get_args());
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintRecursiveObject ($arrayPrefix, $fieldPrefix, $obj) {
    // global $fbcmdPrefs;
    // if (is_array($obj)) {
      // foreach ($obj as $key=>$value) {
        // if ($fieldPrefix == '') {
          // OLD_PrintRecursiveObject($arrayPrefix,"{$key}",$value);
        // } else {
          // OLD_PrintRecursiveObject($arrayPrefix,"{$fieldPrefix}.{$key}",$value);
        // }
      // }
    // } else {
      // if (($obj)||($fbcmdPrefs['print_blanks'])) {
        // $row = $arrayPrefix;
        // $row[] = $fieldPrefix;
        // $row[] = $obj;
        // OLD_PrintRow($row);
      // }
    // }
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_PrintStart() {
    // global $printMatrix;
    // $printMatrix = array();
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintColRecCommon($colOrRec) {
    global $fbcmdPrefs;
    global $printColFields;
    global $printColWidths;
    global $printCSV;

    $consoleWidth = $fbcmdPrefs['output_wrap_width'];
    if ($fbcmdPrefs['output_wrap_env_var']) { // good behaviour? env over arg?
      if (getenv($fbcmdPrefs['output_wrap_env_var'])) {
        $consoleWidth = getenv($fbcmdPrefs['output_wrap_env_var']);
      }
    }

    $cols = GetCommandPref("output_{$colOrRec}");
    if (substr($cols,0,1) == '!') {
      $cols = $fbcmdPrefs["output_{$colOrRec}_" . substr($cols,1)];
    }

    $colpairs = explode(',',strtolower($cols));
    $numCols = count($colpairs);
    $printColFields = array();
    $printColWidths = array();
    $numZeros = 0;
    for ($j=0; $j < $numCols; $j++ ) {
      if (preg_match ('/^(.+):(\d+)$/',$colpairs[$j],$matches)) {
        $printColFields[$j] = $matches[1];
        $printColWidths[$j] = $matches[2];
      } else {
        $printColFields[$j] = $colpairs[$j];
        $printColWidths[$j] = 0;
      }
      if ($printColWidths[$j] == 0) {
        $numZeros++;
        $zeroPos = $j;
        $printColWidths[$j] = $fbcmdPrefs['output_wrap_min_width'];
      }
    }
    if ($numZeros) {
      if ($numZeros > 1) {
        if (!$printCSV) {
          FbcmdWarning("Bad output_{$colOrRec} [{$cols}] more than one zero col");
        }
      }
      $spaceRemaining = $consoleWidth - 1 - array_sum($printColWidths) - ($numCols-1) * $fbcmdPrefs['output_pad'];
      if ($spaceRemaining > 0) {
        $printColWidths[$zeroPos] += $spaceRemaining;
      }
    }

    if ($fbcmdPrefs['output_header']) {
      if ($printCSV) {
        PrintCsvRow($printColFields);
      } else {
        PrintTxtRowWrap($printColFields);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintCol() {
    global $fbProcessed;
    global $fbReturnType;

    PrintColRecCommon('col');
    if ($fbReturnType == 'array') {
      foreach ($fbProcessed as $o) {
        PrintColObj($o);
      }
    } else {
      PrintColObj($fbProcessed);
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintColObj($o) {
    global $printColFields;
    global $printCSV;
    $cols = array();
    for ($i=0; $i < count($printColFields); $i++) {
      $cols[$i] = '';
      $multicols = explode('/',$printColFields[$i]);
      foreach ($multicols as $c) {
        foreach ($o as $k => $v) {
          if (preg_match ('/^' . $c . '$/',$k)) {
            if ($cols[$i] == '') {
              if (($k == 'index')&&(!$printCSV)) {
                $cols[$i] = '[' . str_pad($v,4,' ',STR_PAD_LEFT) . ']';
              } else {
                $cols[$i] = $v;
              }
            }
          }
        }
      }
    }
    if (strlen(implode($cols)) > 0) {
      if ($printCSV) {
        PrintCsvRow($cols);
      } else {
        PrintTxtRowWrap($cols);
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintCsvTable() {
    global $fbProcessed;
    global $fbReturnType;
    global $printColFields;
    global $printUniqueFields;
    global $fbcmdPrefs;

    ProcessUniqueFields();
    $printColFields = $printUniqueFields;

    if ($fbcmdPrefs['csv_header']) {
      PrintCsvRow($printColFields);
    }
    if ($fbReturnType == 'array') {
      foreach ($fbProcessed as $o) {
        PrintColObj($o);
      }
    } else {
      PrintColObj($fbProcessed);
    }
    exit;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintCsvRow($rowIn) {
    global $fbcmdPrefs;
    $rowOut = array();
    foreach ($rowIn as $col) {
      $bookend = false;
      if (strpos($col,$fbcmdPrefs['csv_bookend']) !== false) {
        $col = str_replace($fbcmdPrefs['csv_bookend'],$fbcmdPrefs['csv_escaped_bookend'],$col);
        $bookend = true;
      }
      if ((strpos($col,$fbcmdPrefs['csv_separator']))||($fbcmdPrefs['csv_force_bookends'])) {
        $bookend = true;
      }
      if ($bookend) {
        $col = $fbcmdPrefs['csv_bookend'] . $col . $fbcmdPrefs['csv_bookend'];
      }
      if ($fbcmdPrefs['csv_newline_subst']) {
        $col = str_replace("\r\n", $fbcmdPrefs['csv_newline_subst'], $col);
        $col = str_replace("\n", $fbcmdPrefs['csv_newline_subst'], $col);
      }

      $rowOut[] = $col;
    }
    print implode($fbcmdPrefs['csv_separator'],$rowOut) . "\n";
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintLast($upto = 0) { //2 todo: prettier print, offset by one
    global $fbcmdLast;
    foreach ($fbcmdLast as $cat => $list) {
      for ($i = 0; $i <= $upto; $i++) {
        if (isset($list[$i])) {
          print "[last{$cat}";
          if ($i > 0) {
            print ".{$i}";
          }
          print "] {$list[$i]['id']} {$list[$i]['name']}\n";
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintPrev($upto = 0) { //2 todo: prettier print, offset by one
    global $fbcmdPrev;
    for ($i = 0; $i <= $upto; $i++) {
      if (isset($fbcmdPrev[$i])) {
        $j = 1;
        while (isset($fbcmdPrev[$i][$j])) {
          if ($i == 0) {
            print '[' . str_pad($j,4,' ',STR_PAD_LEFT) . '] ';
          } else {
            print "[{$i}.{$j}] ";
          }
          print "{$fbcmdPrev[$i][$j]['id']} {$fbcmdPrev[$i][$j]['name']}\n";
          $j++;
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintRec() {
    global $fbProcessed;
    global $fbReturnType;
    global $fbcmdPrefs;
    global $printCSV;

    PrintColRecCommon('rec');
    if ($fbReturnType == 'array') {
      foreach ($fbProcessed as $o) {
        PrintRecObj($o);
        if (($fbcmdPrefs['output_rec_space'])&&(!$printCSV)) {
          print "\n";
        }
      }
    } else {
      PrintRecObj($fbProcessed);
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintRecObj($arr) {
    global $fbcmdPrefs;
    global $printColFields;
    global $printCSV;

    $first = true;
    foreach ($arr as $k => $v) {
      if (!in_array($k,$printColFields)) {
        $cols = array();
        for ($i=0; $i < count($printColFields); $i++) {
          $cols[$i] = '';
          $c = $printColFields[$i];
          if ($c == 'key') {
            if ($fbcmdPrefs['output_rec_crumbs']) {
              if (preg_match ('/^(.*)\\.([^\\.]+)$/',$k,$matches)) {
                if (isset($matches[2])) {
                  $k = str_pad('',substr_count($k,'.'),'.') . $matches[2];
                }
              }
            }
            $cols[$i] = $k;
          } elseif ($c == 'value') {
            $cols[$i] = $v;
          } else {
            if ($first) {
              if (isset($arr[$c])) {
                if (($c == 'index')&&(!$printCSV)) {
                  $cols[$i] = '[' . str_pad($arr[$c],4,' ',STR_PAD_LEFT) . ']';
                } else {
                  $cols[$i] = $arr[$c];
                }
              }
            }
          }
        }
        if (strlen(implode($cols)) > 0) {
          $first = false;
          if ($printCSV) {
            PrintCsvRow($cols);
          } else {
            PrintTxtRowWrap($cols);
          }
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintReturn() {
    global $fbReturn;
    global $fbReturnType;
    global $fbProcessed;
    global $fbcmdPrefs;
    global $printFormat;
    global $printCSV;

    if ($printFormat == 'yaml') {
      if (function_exists('yaml_emit')) {
        print yamil_emit($fbProcessed) . "\n"; // dave can't test this :(
      } else {
        FbcmdWarning('YAML module not installed: using JSON');
        $printFormat = 'json';
      }
    }
    if ($printFormat == 'json') {
      print json_encode($fbProcessed) . "\n";
      return;
    }
    if ($printFormat == 'php') {
      print_r($fbProcessed) . "\n";
      return;
    }
    if ($printFormat == 'serial') {
      print serialize($fbProcessed) . "\n";
    }
    if ($printFormat == 'csv') {
      $printCSV = true;
      PrintCsvTable();
      return;
    }
    if ($printFormat == 'csvcol') {
      $printCSV = true;
      PrintCol();
      return;
    }
    if ($printFormat == 'csvrec') {
      $printCSV = true;
      PrintRec();
      return;
    }
    $printCSV = false;
    if ($printFormat == 'col') {
      PrintCol();
      return;
    }
    if ($printFormat == 'rec') {
      PrintRec();
      return;
    }
    FbcmdFatalError("Invalid output_format: -o={$printFormat}");
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function PrintTxtRowWrap($cols) { // this became rediculously messy -sigh-
    global $fbcmdPrefs;
    global $printColWidths;

    $nextrow = array();
    $wrap = false;
    for ($i=0; $i<count($cols); $i++) {
      $txt = $cols[$i];
      $next = '';
      $max = $printColWidths[$i];
      $len = strlen($txt);
      $nlpos = strpos($txt,"\n");
      if (($nlpos !== false) && ($nlpos < $max)) {
        $wrap = true;
        $next = substr($txt,$nlpos+1);
        $txt = ($nlpos == 0) ? '' : substr($txt,0,$nlpos);
      } elseif ($len > $max) {
        $wrap = true;
        $spos = strpos($txt," ");
        if (($spos === false)||($spos >= $max)) {
          $numkeep = $max;
          while (($numkeep > 0)&&(strpos(".,-_;:()!+?",substr($txt,$numkeep-1,1))===false)) $numkeep--; //2 todo better
          if ($numkeep == 0) $numkeep = $max;
          $next = substr($txt,$numkeep);
          $txt = substr($txt,0,$numkeep);
        } else {
          while ((stripos($txt," ",$spos+1) !== false)&&(stripos($txt," ",$spos+1) <= $max)) {
            $spos = stripos($txt," ",$spos+1);
          }
          $next = substr($txt,$spos+1);
          $txt = substr($txt,0,$spos);
        }
      }
      $nextrow[] = $next;
      if ($i < count($cols) - 1) {
        $max += $fbcmdPrefs['output_pad'];
      }
      print str_pad($txt, $max, ' ');
    }
    print "\n";
    if ($wrap) {
      PrintTxtRowWrap($nextrow);
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_ProcessEventMask($mask) {
    // if ((!is_numeric($mask))||($mask > 15)||($mask <= 0)) {
      // FbcmdWarning("Invalid Event Mask: using 1 ('attending')");
      // $mask = 1;
    // }
    // $eventOptions = array();
    // if ($mask & 1) $eventOptions[] = "'attending'";
    // if ($mask & 2) $eventOptions[] = "'unsure'";
    // if ($mask & 4) $eventOptions[] = "'not_replied'";
    // if ($mask & 8) $eventOptions[] = "'declined'";
    // return implode(',',$eventOptions);
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ProcessUniqueFields() {
    global $printUniqueFields;
    global $fbProcessed;
    global $fbReturnType;

    $printUniqueFields = array();

    if ($fbReturnType == 'array') {
      foreach ($fbProcessed as $o) {
        if ($o) {
          array_merge_unique($printUniqueFields,array_keys($o));
        }
      }
    } else {
      $printUniqueFields = array_keys($fbProcessed);
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ProcessReturn() {
    // fbReturnType: three possibilities
    // 'value', 'obj', 'array'
    global $fbcmdPrefs;
    global $fbReturn;
    global $fbReturnType;
    global $fbProcessed;
    global $printFlat;
    global $printFormat;
    global $fbcmdExtraOutput;

    $printFormat = strtolower(GetCommandPref('output_format'));

    $printFlat = true;
    if (!in_array($printFormat,array('col','rec'))) {
      if ($fbcmdPrefs['output_flat'] != '') {
        $printFlat = $fbcmdPrefs['output_flat'];
      } elseif (isset($fbcmdPrefs['output_flat_' . $printFormat])) {
        $printFlat = $fbcmdPrefs['output_flat_' . $printFormat];
      }
    }

    if (!is_array($fbReturn)) {
      $fbReturnType = 'value';
      $fbProcessed = AddExtraOutput(array('retval' => $fbReturn));
      return;
    }
    if (isset($fbReturn['data'][0])) {
      $fbReturnType = 'array';
      $fbProcessed = array();
      for ($j=0; $j < count($fbReturn['data']); $j++) {
        $i = $j+1;  //2 eventually, will have to add COUNT support
        $fbcmdExtraOutput['index'] = $i;
        $fbProcessed[$i] = ProcessShowFields($fbReturn['data'][$j]);
      }
      return;
    }
    $fbReturnType = 'obj';
    $fbProcessed = ProcessShowFields($fbReturn);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ProcessShowFields($inArr) {
    global $printFields;
    global $fbcmdExtraOutput;
    $show = GetCommandPref('output_show');
    $printFields = explode(',',strtolower($show));
    if (in_array('none',$printFields)) return array();
    $arr = AddExtraOutput($inArr);
    return ProcessShowRecurse($arr);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ProcessShowRecurse($arr, $base = '') {
    global $printFields;
    global $printFlat;
    $ret = array();
    if (is_array($arr)) {
      foreach ($arr as $k => $v) {
        if (is_array($v)) {
          $sub = ProcessShowRecurse($v,"{$base}{$k}.");
          if (count($sub)) {
            if ($printFlat) {
              foreach ($sub as $sk => $sv) {
                $ret[$sk] = $sv;
              }
            } else {
              $ret[$k] = $sub;
            }
          }
        } else {
          if (preg_in_array("{$base}{$k}",$printFields)||(in_array('all',$printFields))) {
            if ($printFlat) {
              $ret["{$base}{$k}"] = $v;
            } else {
              $ret[$k] = $v;
            }
          }
        }
      }
    } else {
      FbcmdWarning("Unexpected: non-array passed to ProcessShowRecurse");
    }
    return $ret;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_ProfileName($id) {
    // global $indexFriendBaseInfo;
    // global $indexPageNames;
    // global $indexStreamNames;
    // global $indexMessageNames;
    // global $indexFlistNames;
    // global $indexApplicationNames;
    // if (isset($indexFriendBaseInfo[$id])) {
      // return $indexFriendBaseInfo[$id]['name'];
    // }
    // if (isset($indexPageNames[$id])) {
      // return $indexPageNames[$id]['name'];
    // }
    // if (isset($indexStreamNames[$id])) {
      // return $indexStreamNames[$id]['name'];
    // }
    // if (isset($indexMessageNames[$id])) {
      // return $indexMessageNames[$id]['name'];
    // }
    // if (isset($indexFlistNames[$id])) {
      // return $indexFlistNames[$id]['name'];
    // }
    // if (isset($indexApplicationNames[$id])) {
      // return $indexApplicationNames[$id]['display_name'];
    // }
    // return 'unknown';
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function RemoveParam($a) {
    global $fbcmdParams;
    global $fbcmdCommand;
    $cur = $a;
    $count = ParamCount();
    if (($a >= 0)&&($a <= $count)) {
      while ($cur < $count) {
        $fbcmdParams[$cur] = $fbcmdParams[$cur + 1];
        $cur++;
      }
      unset($fbcmdParams[$count]);
    } else {
      FbcmdWarning("UNEXPECTED: Can't remove parameter {$a}\n");
    }
    $fbcmdCommand = strtoupper($fbcmdParams[0]);
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function RemoveParams($a, $b=null)
  {
    global $fbcmdParams;
    if ($b == null) {
      $b = $a;
    }
    for ($j=$a; $j <= $b; $j++) {
      RemoveParam($a);
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function Resolve($matchme, $exitOnFalse = true, $types = 'number,prev,alias,last,username,accounts,friends,likes,groups') { //OLD_FlistMatch ($flistItem,$isPrefixed,$dataArray,$keyId,$keyMatch,$allowMultipleMatches = true, $forceExactMatch = false) {
    global $fbcmdAlias;
    global $resolvedId;
    global $resolvedText;
    global $fbcmdLast;
    global $fbcmdPrev;
    global $fbcmdRefCache;

    $resolvedId = '';
    $resolvedText = '';
    $resolvedMatches = array();
    $numMatch = 0;
    $typelist = explode(',',$types);

    $m = strtoupper($matchme);
    if (preg_match('/^([^\\.]+)\\.(.+)$/',$m,$matches)) { // check for left.right syntax
      $mDot = true;
      $mLeft = $matches[1];
      $mRight = $matches[2];
    } else {
      $mDot = false;
    }

    // if (substr($m,0,1) == '#') {
      // $resolvedId = substr($m,1);
      // $resolvedText = substr($m,1);
      // return true;
    // }

    if (in_array('number',$typelist)) {
      if (is_numeric($m)) {
        if (($m > 0)&&($m < 1000)) {
          if (in_array('prev',$typelist)) {
            if (!$mDot) {
              $mLeft = 0;
              $mRight = $m;
            }
            if (isset($fbcmdPrev[$mLeft][$mRight])) {
              $resolvedId = $fbcmdPrev[$mLeft][$mRight]['id'];
              $resolvedText = $fbcmdPrev[$mLeft][$mRight]['name'];
              return true;
            }
          }
        }
        $resolvedId = $m;
        $resolvedText = $m;
        return true;
      }
      if (is_numeric(str_replace('_','',$m))) {
        $resolvedId = $m;
        $resolvedText = $m;
        return true;
      }
    }
    if (in_array('alias',$typelist)) {
      foreach ($fbcmdAlias as $key => $val) {
        if (strtoupper($key) == $m) {
          $resolvedId = $val;
          $resolvedText = "{$key} [alias]";
          return true;
        }
      }
    }
    if (in_array('last',$typelist)) {
      if (!$mDot) {
        $mLeft = $m;
        $mRight = 0;
      }
      if (preg_match('/^LAST(.+)$/',$mLeft,$matches)) {
        $cat = strtolower($matches[1]);
        if (isset($fbcmdLast[$cat][$mRight])) {
          $resolvedId = $fbcmdLast[$cat][$mRight]['id'];
          $resolvedText = $fbcmdLast[$cat][$mRight]['name'];
          return true;
        }
      }
    }
    $ids = array();
    foreach ($typelist as $type) {
      if (isset($fbcmdRefCache[$type])) {
        $lst = $fbcmdRefCache[$type];
        foreach ($lst as $key => $val) {
          if (strtoupper($key) == $m) {
            if (!isset($ids[$val])) {
              $ids[$val] = 1;
              $resolvedId = $val;
              $resolvedText = "{$key} [{$type}]";
              $numMatch++;
              $resolvedMatches[$numMatch] = array('id' => $resolvedId, 'name' => $resolvedText);
            }
          }
        }
      }
    }
    if (count($resolvedMatches) == 0) {
      $m = str_replace('/','\/',$m);
      foreach ($typelist as $type) {
        if (isset($fbcmdRefCache[$type])) {
          $lst = $fbcmdRefCache[$type];
          foreach ($lst as $key => $val) {
            if (preg_match("/{$m}/i",$key)) {
              if (!isset($ids[$val])) {
                $ids[$val] = 1;
                $resolvedId = $val;
                $resolvedText = "{$key} [{$type}]";
                $numMatch++;
                $resolvedMatches[$numMatch] = array('id' => $resolvedId, 'name' => $resolvedText);
              }
            }
          }
        }
      }
    }
    if ($numMatch == 1) {
      return true;
    }
    $resolvedId = '';
    $resolvedText = '';
    if ($numMatch == 0) {
      if ($exitOnFalse) {
        print "\nCould not resolve \"{$matchme}\".  (to update your cache, fbcmd refresh)\n";
        exit;
      }
      return false;
    }
    if ($exitOnFalse) {
      ShiftPrev();
      for ($j=1; $j <= $numMatch; $j++) {
        AddPrev($resolvedMatches[$j]['id'],$resolvedMatches[$j]['name']);
      }
      SaveDataFile('prevfile',$fbcmdPrev,'prev_save');
      PrintPrev();
      print "\nCould not resolve \"{$matchme}\".\n";
      exit;
    }
    return false;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ReturnDataToPrev() {
    global $fbReturn;
    global $fbcmdPrev;
    if ((isset($fbReturn['data']))&&(is_array($fbReturn['data']))) {
      ShiftPrev();
      for ($j=0; $j < count($fbReturn['data']); $j++) {
        if (isset($fbReturn['data'][$j]['id'])) {
          if (isset($fbReturn['data'][$j]['name'])) {
            AddPrev($fbReturn['data'][$j]['id'], $fbReturn['data'][$j]['name']);
          } else {
            AddPrev($fbReturn['data'][$j]['id'], "[no description]");
          }
        }
      }
      if (count($fbcmdPrev[0]) > 1) {
        SaveDataFile('prevfile',$fbcmdPrev,'prev_save');
        return true;
      }
    }
    return false;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SaveAliasFile() {
    global $fbcmdPrefs;
    global $fbcmdAliasFileName;
    global $fbcmdAlias;

    $fileContents = "<?php\n\$fbcmdAlias = " . var_export($fbcmdAlias, true) . ";\n?>\n";
    if (@file_put_contents($fbcmdAliasFileName,$fileContents) == false) {
      FbcmdWarning("Could not generate aliasfile {$fbcmdAliasFileName}");
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SaveDataFile($prefFile, $saveData, $prefSave = 1) {
    global $fbcmdPrefs;
    global $fbcmdBaseDir;
    if (($prefSave == 1)||($fbcmdPrefs[$prefSave])) {
      $fileName = str_replace('[datadir]',$fbcmdBaseDir,$fbcmdPrefs[$prefFile]);
      $result = @file_put_contents($fileName,serialize($saveData));
      if ($result == false) {
        if ($prefSave == 1) {
          FbcmdFatalError("Could not generate {$prefFile} {$fileName}");
        } else {
          FbcmdWarning("Could not generate {$prefFile} {$fileName}");
        }
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function SavePhoto($urlSource, $picObject, $albumId, $tagId, $outputDir, $fileFormat, $checkSkip = true) { //2 todo: refactor
    global $fbcmdPrefs;
    $photoContents = false;
    $retry=0;

    $fileFormat = str_replace('\\', '/', $fileFormat);
    if ($picObject) {
      $fileFormat = str_replace('[pid]', $picObject['id'], $fileFormat);
      $fileFormat = str_replace('[oid]', $picObject['from']['id'], $fileFormat);
      $fileFormat = str_replace('[oname]', $picObject['from']['name'], $fileFormat);
    }
    if ($albumId) {
      $fileFormat = str_replace('[aid]', $albumId, $fileFormat);
    }
    if ($tagId) {
      $fileFormat = str_replace('[tid]', $tagId, $fileFormat);
      //$fileFormat = str_replace('[tname]', OLD_ProfileName($tagId), $fileFormat);
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
    } else {
      print "$outputFile\n";
    }

    return true;
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
        $fileContents .= "  \$fbcmdPrefs['{$switchKey}'] = " . var_export($switchValue,true) . ";\n";
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
      if (($fbcmdParams[$n] == '0')||(strtolower($fbcmdParams[$n]) == 'default')) {
        $fbcmdParams[$n] = $value;
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ShiftPrev() {
    global $fbcmdPrefs;
    global $fbcmdPrev;

    $k = $fbcmdPrefs['prev_length'];
    while ($k > 0) {
      if (isset($fbcmdPrev[$k-1])) {
        $fbcmdPrev[$k] = $fbcmdPrev[$k-1];
      }
      $k--;
    }
    $fbcmdPrev[0] = array();
    $fbcmdPrev[0][] = array('id' => 0, 'name' => '0');
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function ShowAuth() {
    global $fbcmdPrefs, $urlAccess, $urlAuth, $fbcmdVersion;
    print "\n";
    print "Welcome to fbcmd! [version $fbcmdVersion]\n\n";
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
    global $notYet;

    print "\n";
    print "fbcmd [v{$fbcmdVersion}] Facebook Command Line Interface\n\n";

    print "syntax:\n\n";

    print "  fbcmd COMMAND required_parameter(s) [optional_parameter(s)] -switch=value\n\n";

    print "commands: (can be in lower case)\n\n";

    print "=====================================================================\n";
    print "NOT SUPPORTED YET IN 2.0 (may be depricated or merged into others)\n"  ;
    print "=====================================================================\n\n";


    foreach ($fbcmdCommandList as $cmd) {
      if (in_array($cmd,$notYet)) {
        ShowUsageCmd($cmd);
      }
    }

    print "=====================================================================\n";
    print "SUPPORT IN 2.0\n";
    print "=====================================================================\n\n";

    foreach ($fbcmdCommandList as $cmd) {
      if (!in_array($cmd,$notYet)) {
        ShowUsageCmd($cmd);
      }
    }

    print "examples:\n\n";

    print "  fbcmd status \"is excited to play with fbcmd\"\n";
    print "  fbcmd status \"this\\nis\\na\\nmulti-line\\nstatus\"\n";
    print "  fbcmd match john\n";
    print "  fbcmd alias jj 3   (# from the result of match)\n";
    print "  fbcmd target jj post \"You're the man, John!\"\n";
    print "  fbcmd info me/friends name,id,birthday\n";

    print "\nfor additional help, examples, parameter usage, preference settings,\n";
    print "visit the FBCMD wiki at:\n\n";
    print "  http://fbcmd.dtompkins.com\n\n";
    exit;
  }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_StreamPostHelper($target_id, $uid, $offset) {
    // global $fbcmdParams;
    // global $fbcmdPrefs;
    // global $fbObject;

    // $firstParam = strtoupper($fbcmdParams[$offset]);

    // if (in_array($firstParam, array('MP3','IMG','FLASH'))) {
      // if ($firstParam == 'MP3') {
        // ValidateParamCount($offset+2, $offset+9);
        // SetDefaultParam($offset+1,$fbcmdPrefs['default_post_message']);
        // SetDefaultParam($offset+2,$fbcmdPrefs['default_postmp3_mp3src']);
        // SetDefaultParam($offset+3,$fbcmdPrefs['default_postmp3_mp3title']);
        // SetDefaultParam($offset+4,$fbcmdPrefs['default_postmp3_mp3artist']);
        // SetDefaultParam($offset+5,$fbcmdPrefs['default_postmp3_mp3album']);
        // $msg = $fbcmdParams[$offset+1];
        // $media = array(array('type' => 'mp3', 'src' => $fbcmdParams[$offset+2], 'title' => $fbcmdParams[$offset+3], 'artist' => $fbcmdParams[$offset+4], 'album' => $fbcmdParams[$offset+5]));
        // $offsetPostData = $offset + 6;
      // }
      // if ($firstParam == 'IMG') {
        // ValidateParamCount($offset+2, $offset+7);
        // SetDefaultParam($offset+1,$fbcmdPrefs['default_post_message']);
        // SetDefaultParam($offset+2,$fbcmdPrefs['default_postimg_imgsrc']);
        // if ($fbcmdPrefs['default_postimg_imglink'] == '0') {
          // SetDefaultParam($offset+3,$fbcmdParams[$offset+2]);
        // } else {
          // SetDefaultParam($offset+3,$fbcmdPrefs['default_postimg_imglink']);
        // }
        // $msg = $fbcmdParams[$offset+1];
        // $media = array(array('type' => 'image', 'src' => $fbcmdParams[$offset+2], 'href' => $fbcmdParams[$offset+3]));
        // $offsetPostData = $offset + 4;
      // }
      // if ($firstParam == 'FLASH') {
        // ValidateParamCount($offset+3, $offset+7);
        // SetDefaultParam($offset+1,$fbcmdPrefs['default_post_message']);
        // SetDefaultParam($offset+2,$fbcmdPrefs['default_postflash_swfsrc']);
        // SetDefaultParam($offset+3,$fbcmdPrefs['default_postflash_imgsrc']);
        // $msg = $fbcmdParams[$offset+1];
        // $media = array(array('type' => 'flash', 'swfsrc' => $fbcmdParams[$offset+2], 'imgsrc' => $fbcmdParams[$offset+3]));
        // $offsetPostData = $offset + 4;
      // }
    // } else {
      // ValidateParamCount($offset,$offset+4);
      // SetDefaultParam($offset,$fbcmdPrefs['default_post_message']);
      // $msg = $fbcmdParams[$offset];
      // $media = '';
      // $offsetPostData = $offset + 1;
    // }

    // SetDefaultParam($offsetPostData, $fbcmdPrefs['default_post_name']);
    // SetDefaultParam($offsetPostData + 1, $fbcmdPrefs['default_post_link']);
    // SetDefaultParam($offsetPostData + 2, $fbcmdPrefs['default_post_caption']);
    // SetDefaultParam($offsetPostData + 3, $fbcmdPrefs['default_post_description']);

    // $attachment = array('name' => $fbcmdParams[$offsetPostData], 'href' => $fbcmdParams[$offsetPostData + 1], 'caption' => $fbcmdParams[$offsetPostData + 2], 'description' => $fbcmdParams[$offsetPostData + 3]);
    // if ($media) {
      // $attachment['media'] = $media;
    // }

    // if (($fbcmdPrefs['sharepost'])&&($fbcmdParams[$offsetPostData + 1])) {
      // $actionLinks = array(array('text' => 'Share', 'href' => 'http://www.facebook.com/share.php?u=' . $fbcmdParams[$offsetPostData + 1]));
    // } else {
      // $actionLinks = null;
    // }

    // try {
      // $fbReturn = $fbObject->api_client->stream_publish($msg, $attachment, $actionLinks, $target_id, $uid);
      // TraceReturn();
    // } catch(Exception $e) {
      // OLD_FbcmdException($e);
    // }
    // return $fbReturn;
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_TagFieldMatch($matchString, $dataToSearch, $matchField, $idField, $partial = false, $nameField = 'name') {
    // $matchList = array();
    // if ($partial) {
      // $matchExp = "/$matchString/i";
    // } else {
      // $matchExp = "/^$matchString/i";
    // }
    // if (isset($dataToSearch)) {
      // foreach ($dataToSearch as $d) {
        // if (isset($d[$matchField])) {
          // if (preg_match($matchExp,$d[$matchField])) {
            // $matchList[] = array($d[$idField],$d[$nameField],$d[$matchField]);
          // }
        // }
      // }
    // }
    // return $matchList;
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  // function OLD_TagText($textToTag) {
    // global $fbcmdPrefs;
    // $textToTag = str_replace('@@','[[AT]]',$textToTag);
    // if (preg_match_all($fbcmdPrefs['status_tag_syntax'], $textToTag, $matches, PREG_SET_ORDER)) {
      // OLD_MultiFQL(array('FriendId','FriendBaseInfo','PageId','PageNames','GroupNames'));
      // foreach ($matches as $pregMatch) {
        // $matchList = OLD_MatchTag($pregMatch[1]);
        // if ($matchList) {
          // $taggedText = "@[{$matchList[0][0]}:{$matchList[0][0]}:{$matchList[0][1]}]";
        // } else {
          // $taggedText = "[[AT]]{$pregMatch[1]}";
        // }
        // $textToTag = str_replace($pregMatch[0],$taggedText,$textToTag);
      // }
    // }
    // $textToTag = str_replace('[[AT]]','@',$textToTag);
    // return $textToTag;
  // }

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  function TraceReturn($obj = false) {
    global $fbcmdPrefs;
    global $fbReturn;
    if ($fbcmdPrefs['trace']) {
      if (!$obj) {
        $obj = $fbReturn;
      }
      print_r ($obj);
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
          if (!mkdir($filePath,octdec($fbcmdPrefs['mkdir_mode']),true)) {
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
