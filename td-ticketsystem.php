<?php
/**
 * Plugin Name: TD Ticket System
 * Plugin URI: http://www.transcendevelopment.com/td-ticket-system/
 * Description: A ticket system for Wordpress to maintain reliable communication with customers without the worry of missing emails.
 * Version: 1.0.5
 * Author: TranscenDevelopment
 * Author URI: http://www.transcendevelopment.com
 * License: GPL2 
 

 /*  Copyright 2014  Mike Ramirez  (email : transcendev@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
global $wpdb;

$installed_ver = get_option( "td_tts_db_version" );
register_activation_hook( __FILE__, 'td_tts_install');

define('DEPT_TABLE', $wpdb->prefix . "td_tts_departments");
define('RESP_TABLE', $wpdb->prefix . "td_tts_responses");
define('MESS_TABLE', $wpdb->prefix . "td_tts_messages");
define('SET_TABLE', $wpdb->prefix . "td_tts_settings");
define('TDTTSICON', '../wp-content/plugins/td-ticket-system/images/icon.png'); 
define('TDTTSAJAXIMG', '../wp-content/plugins/td-ticket-system/images/ajax-loader.gif');
define('TDTTSDIR', plugin_dir_path( __FILE__ ));
define('TDPLUGINURL', plugins_url().'/td-ticket-system/');
define('TDTTSVER', '1.0.5');

$td_tts_menuPos = get_option('td_tts_menuPos') . '.312';
if($td_tts_menuPos == '.312') {$td_tts_menuPos='26.312';}

define('TDTTSMENUPOS', $td_tts_menuPos);

add_action('admin_menu', 'td_tts_regMenuPage');
add_action('init', 'td_tts_loadSomeStuff');
add_action('admin_footer', 'td_tts_ajaxAdminActions');
add_action('wp_head', 'td_tts_userTopLinks');
add_action('wp_footer', 'td_tts_userAjax');
add_action('wp_ajax_td_tts_messageShow', 'td_tts_doMessageShow' );
add_action('wp_ajax_td_tts_adminMessageResponse', 'td_tts_adminMessageResponse');
add_action('wp_ajax_td_tts_adminMessageDelete', 'td_tts_adminMessageDelete');
add_action('wp_ajax_nopriv_td_tts_userMessage', 'td_tts_userSendMessage' );
add_action('wp_ajax_td_tts_userMessage', 'td_tts_userSendMessage' );
add_action('wp_ajax_nopriv_td_tts_userMessagePanel', 'td_tts_userLogIn');
add_action('wp_ajax_td_tts_userMessagePanel', 'td_tts_userLogIn');
add_action('wp_ajax_nopriv_td_tts_UserMessageShow', 'td_tts_UserMessageShow');
add_action('wp_ajax_td_tts_UserMessageShow', 'td_tts_UserMessageShow');
add_action('wp_ajax_nopriv_td_tts_userMessageResponse', 'td_tts_userMessageResponse');
add_action('wp_ajax_td_tts_userMessageResponse', 'td_tts_userMessageResponse');
add_filter('the_content', 'td_tts_hookInPage');

//---------------------------------------------------------//
function td_tts_regMenuPage() {
//---------------------------------------------------------//    
    add_menu_page('The Ticket System', 'Ticket System', 'administrator', 'tdtts10', 'td_tts_mainTTSAdmin', TDTTSICON, TDTTSMENUPOS);
    #add_submenu_page('tdtts10', 'Ticket System Departments', 'Departments', 'administrator', 'tdtts10_2', 'td_tts_departments');
    add_submenu_page('tdtts10', 'Ticket System Settings', 'Templates', 'administrator', 'tdtts10_2', 'td_tts_templates');
    add_submenu_page('tdtts10', 'Ticket System Settings', 'Settings', 'administrator', 'tdtts10_1', 'td_tts_adminfunc');
}
//---------------------------------------------------------//
function td_tts_templates() {
//---------------------------------------------------------//
if (isset($_REQUEST['action'])) {$action = filter_var($_REQUEST['action'], FILTER_SANITIZE_STRING);} else {$action='';}
if (isset($_REQUEST['td_tts_showTemplate'])) {$showTemplate = filter_var($_REQUEST['td_tts_showTemplate'], FILTER_SANITIZE_STRING);} else {$showTemplate = 'td_tts_contactForm';}

$displayBox1 = 'block'; $displayBox2 = 'none'; $displayBox3 = 'none';
$displayBox4 = 'none'; $displayBox5 = 'none'; $displayBox6 = 'none'; 
$td_tts_savedTempMessage='';
if ($action == 'td_tts_saveTemplates') {
    $td_tts_styles          = stripslashes( filter_var($_POST['td_tts_styles'], FILTER_SANITIZE_SPECIAL_CHARS) );
    $td_tts_notification    = stripslashes( filter_var($_POST['td_tts_notification'], FILTER_SANITIZE_SPECIAL_CHARS) );
    $td_tts_responsenote    = stripslashes( filter_var($_POST['td_tts_responsenote'], FILTER_SANITIZE_SPECIAL_CHARS) );
    $td_tts_contactForm     = stripslashes( filter_var($_POST['td_tts_contactForm'], FILTER_SANITIZE_SPECIAL_CHARS) );
    $td_tts_loginForm       = stripslashes( filter_var($_POST['td_tts_loginForm'], FILTER_SANITIZE_SPECIAL_CHARS) );
    $td_tts_successPage     = stripslashes( filter_var($_POST['td_tts_successPage'], FILTER_SANITIZE_SPECIAL_CHARS) );
    if ($showTemplate == 'td_tts_styles') {
        delete_option('td_tts_styles');
        add_option('td_tts_styles', $td_tts_styles, '', 'yes');
        $displayBox1='none'; $displayBox6='block';
    }
    if ($showTemplate == 'td_tts_notification') {
        delete_option('td_tts_notification');
        add_option('td_tts_notification', $td_tts_notification, '', 'yes');
        $displayBox1='none'; $displayBox3='block';
    }
    if ($showTemplate == 'td_tts_responsenote') {
        delete_option('td_tts_responsenote');
        add_option('td_tts_responsenote', $td_tts_responsenote, '', 'yes');
        $displayBox1='none'; $displayBox4='block';
    }
    if ($showTemplate == 'td_tts_contactForm') {
        delete_option('td_tts_contactForm');
        add_option('td_tts_contactForm', $td_tts_contactForm, '', 'yes');
    }
    if ($showTemplate == 'td_tts_loginForm') {
        delete_option('td_tts_loginForm');
        add_option('td_tts_loginForm', $td_tts_loginForm, '', 'yes');
        $displayBox1='none'; $displayBox2='block';
    }
    if ($showTemplate == 'td_tts_successPage') {
        delete_option('td_tts_successPage');
        add_option('td_tts_successPage', $td_tts_successPage, '', 'yes');
        $displayBox1='none'; $displayBox5='block';
    }
    $td_tts_savedTempMessage = 'Template Changes Saved';
} elseif ($action == 'populateDefaultTemplates') {td_tts_populateDefaultTemplates();}

$td_tts_styles          = get_option('td_tts_styles');
$td_tts_notification    = get_option('td_tts_notification');
$td_tts_responsenote    = get_option('td_tts_responsenote');
$td_tts_contactForm     = get_option('td_tts_contactForm');
$td_tts_loginForm       = get_option('td_tts_loginForm');
$td_tts_successPage     = get_option('td_tts_successPage');

echo td_tts_howdoyado();
echo <<<"HTML"
<h1>TD Ticket System - Templates</h1>
<a href="javascript:void(0);" onclick="td_tts_showTemp('td_tts_contactForm');">Contact Form</a> |
<a href="javascript:void(0);" onclick="td_tts_showTemp('td_tts_loginForm');">Login Form</a> |
<a href="javascript:void(0);" onclick="td_tts_showTemp('td_tts_notification');">Email Notification</a> |
<a href="javascript:void(0);" onclick="td_tts_showTemp('td_tts_responsenote');">Response Notification</a> |
<a href="javascript:void(0);" onclick="td_tts_showTemp('td_tts_successPage');">Success Page</a> |
<a href="javascript:void(0);" onclick="td_tts_showTemp('td_tts_styles');">Stylesheet</a>
<div style="padding:5px 0 0 0;color:green">$td_tts_savedTempMessage</div>
<form method="post" name="td_tts_templateForm">
<div id="td_tts_contactFormBox" style="display:$displayBox1">
    <h2>Contact Form</h2>
    <textarea name="td_tts_contactForm" id="td_tts_contactForm" style="width:50%;height:200px;">$td_tts_contactForm</textarea>
</div>
<div id="td_tts_loginFormBox" style="display:$displayBox2">
    <h2>Login Form</h2>
    <textarea name="td_tts_loginForm" id="td_tts_loginForm" style="width:50%;height:200px">$td_tts_loginForm</textarea>
</div>
<div id="td_tts_notificationBox" style="display:$displayBox3">
    <h2>Email Notification</h2>
    <textarea name="td_tts_notification" id="td_tts_notification" style="width:50%;height:200px">$td_tts_notification</textarea>
</div>
<div id="td_tts_responsenoteBox" style="display:$displayBox4">
    <h2>Response Notification</h2>
    <textarea name="td_tts_responsenote" id="td_tts_responsenote" style="width:50%;height:200px">$td_tts_responsenote</textarea>
</div>
<div id="td_tts_successPageBox" style="display:$displayBox5">
    <h2>Success Page</h2>
    <textarea name="td_tts_successPage" id="td_tts_successPage" style="width:50%;height:200px">$td_tts_successPage</textarea>
</div>
<div id="td_tts_stylesBox" style="display:$displayBox6">
    <h2>Stylesheet</h2>
    <textarea name="td_tts_styles" id="td_tts_styles" style="width:50%;height:200px">$td_tts_styles</textarea>
</div>
<div style="width:50%;text-align:right"><input type="submit" value="Save Changes" /></div>
<input type="hidden" name="td_tts_showTemplate" id="td_tts_showTemplate" value="$showTemplate" />
<input type="hidden" name="action" value="td_tts_saveTemplates" />
</form>
<div style="width:35%;border:1px solid #aaa;padding:8px 0 8px 15px;margin-top:25px;">
    <a href="javascript:void(0);" onclick="if(confirm('Are you sure??')){location.href='?page=tdtts10_2&action=populateDefaultTemplates';}else{return false;}">Restore Default Templates</a> - This will overwrite your current templates. 
</div>
HTML;
}
//---------------------------------------------------------//
function td_tts_mainTTSAdmin() {
//---------------------------------------------------------//
    if (isset($_REQUEST['action'])) {$action = filter_var($_REQUEST['action'], FILTER_SANITIZE_STRING);}
        else {$action = '';}
    if (!$action) {td_tts_mainAdminPanel(0);} elseif ($action == 'filterMessages') {td_tts_mainAdminPanel();}
    elseif ($action == 'showMessage') {td_tts_showMessage();}
}
//---------------------------------------------------------//
function td_tts_mainAdminPanel( $recursive ) {
//---------------------------------------------------------//    
global $wpdb;
$setAr = td_tts_getAllSettings();

if ($recursive == 0) { #if recursive is 0 it's not an ajax call, otherwise...
    echo td_tts_linkStyles();
    echo '<div id="td_tts_mainView">';
}
echo td_tts_howdoyado();
echo <<<HTML
<h1>TD Ticket System</h1>
<div style="padding:8px 0 15px 0;">
    <form method="post" name="td_tts_ticketSearch">
        Search&nbsp;<input type="text" size="40" name="keywords" />&nbsp;<button onclick="document.td_tts_ticketSearch.submit();">Search</button>
    </form>
</div>
<div id="td_tts_statusFilterBar">
    <strong>Show Only</strong>
    <a href="admin.php?page=tdtts10">Un-Closed</a>
    <a href="admin.php?page=tdtts10&td_tts_statusFilter=Pending">Pending</a>
    <a href="admin.php?page=tdtts10&td_tts_statusFilter=Open">Open</a>
    <a href="admin.php?page=tdtts10&td_tts_statusFilter=Responded">Responded</a>
    <a href="admin.php?page=tdtts10&td_tts_statusFilter=Closed">Closed</a>
</div>
HTML;

if (isset($_REQUEST['td_tts_statusFilter'])) {
    $filter = filter_var($_REQUEST['td_tts_statusFilter'], FILTER_SANITIZE_STRING);
    $filterBy = " WHERE status = \"$filter\" ";
} else {
    $filterBy = " WHERE status != \"Closed\" ";
}

if (isset($_REQUEST['keywords'])) {
    $search = filter_var($_REQUEST['keywords'], FILTER_SANITIZE_STRING);
    $filterBy .= " AND ( message LIKE \"%$search%\" OR id = \"$search\" or subject LIKE \"%$search%\" ) ";
}

$td_tts_ticketsPerPage = $setAr[4];
if ($td_tts_ticketsPerPage < 1) {$td_tts_ticketsPerPage=10;}
if (empty($_REQUEST['td_tts_startx'])){$td_tts_startx=0;} else {$td_tts_startx=$_REQUEST['td_tts_startx'];}

$bgclass = 'td_tts_bg';
$sqlQueryC = "
    SELECT id
    FROM
"
    . MESS_TABLE
    . $filterBy;
$sqlQuery = "
    SELECT id, name, email, subject, message, status, timestamp, department, modified, ustamp
    FROM
"
    . MESS_TABLE
    . $filterBy .
"
    ORDER BY id DESC
    LIMIT $td_tts_startx, $td_tts_ticketsPerPage
";

$tickets = $wpdb->get_results($sqlQueryC);
$tCount  = count($tickets);
if ($tCount > $td_tts_ticketsPerPage) {
    $pagination = td_tts_pagination( $tCount, $td_tts_ticketsPerPage );
}

if (isset($pagination)) {
echo <<<"HTML"
$pagination
HTML;
}

$messages = $wpdb->get_results($sqlQuery);
foreach ( $messages as $message ) {
    $mID = $message->id; $mName = $message->name; $mEmail = $message->email; $mSubject = $message->subject;
    $mMessage = $message->message; $mStatus = $message->status; $mTimestamp = $message->timestamp; $mDept = $message->department;
    $mMod = $message->modified; $mUtime = $message->ustamp;
    if ($mMod==''){$mMod = 'n/a';}    
echo <<<HTML
    <div class="td_tts_bg">
        <div class="td_tts_ticketBody">
            <div class="td_tts_ticket_headerAdmin">
                <strong><a href="javascript:void(0);" onclick="td_tts_openMessage($mID);">Ticket #$mID : $mSubject</a></strong>
            </div>
            <div class="td_tts_recOn"><strong>Received on:</strong> <span style="font-style:italic">$mTimestamp</span></div>
            <div class="td_tts_upOn"><strong>Updated on:</strong> <span style="font-style:italic">$mMod</span></div>    
        </div>
        <div class="td_tts_statusMess">
            <strong>Status:</strong> $mStatus
        </div>
        <div style="clear:both"></div>
    </div>
HTML;
}
if (isset($pagination)) {
    echo $pagination;
}
echo "</div>";

if ($recursive == 0) { # avoid duplicated items when called via ajax
$ajaxLoader = '<img src="'.TDTTSAJAXIMG.'" />';    
echo <<<HTML
<div id="td_tts_resizeWindow" class="ui-widget-content">
    <div id="tdtts_loading_panel" style="display:none;">
        <div style="text-align:center;padding-top:100px;">
        <h1 id="td_tts_loadingMessage"></h1>
            $ajaxLoader
        </div>
    </div>
    <div style="text-align:right;padding:0 0 5px 0;">
        <button onclick="document.getElementById('td_tts_resizeWindow').style.display='none';">Close</button>
    </div>
    <div id="tdtts_message_panel"></div>
</div>
HTML;
}

}
//---------------------------------------------------------//
function td_tts_pagination( $tCount, $td_tts_ticketsPerPage ) {
//---------------------------------------------------------//
$retVal .= "<div style='width:925px;text-align:right;font-size:12px;line-spacing:1.5px;'>Page ";
if ($tCount > $td_tts_ticketsPerPage) {
    $pageCount = ceil($tCount / $td_tts_ticketsPerPage);
    if ($pageCount > 1) {
        for ($i=1; $i <= $pageCount; $i++) {
            $thisPageStart = $td_tts_ticketsPerPage*($i-1);
            $retVal .= "<a href='admin.php?page=tdtts10&td_tts_startx=$thisPageStart&td_tts_statusFilter=$filter'>$i</a> ";        
        }
    }
}
$retVal .= "</div>";
return $retVal;
}
//---------------------------------------------------------//
function td_tts_adminfunc() {
//---------------------------------------------------------//
if (isset($_POST['action'])) {$action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);} else {$action='';}
if ($action == 'td_tts_save_configs') {td_tts_save_configs();}

global $wpdb;
$setAr = td_tts_getAllSettings();

$menuPos = get_option('td_tts_menuPos');

echo td_tts_linkStyles();
echo td_tts_howdoyado();
echo <<<HTML
<h1>TD Ticket System - Settings</h1>
<div style="float:left;padding:15px 0 0 15px;width:400px;line-height:19px;">
<h2>Implementing TD Ticket System</h2><br />
You can begin using the system
by <a href="post-new.php?post_type=page">creating a new page</a> with the following code in the body:<br />
<textarea rows="1" cols="40">[ticket_system]</textarea>
<br /><br />
<h2>Configuration Settings</h2>
<form method="post" name="tdttsconfigform">

<h3>General Settings</h3>
<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<input type="checkbox" name="emailNotifications" value="1" $setAr[0] />&nbsp;
<strong>Receive email notifications for new and updated tickets</strong>
</div>

<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<strong>Email address to send notifications to</strong>
<br /><input type="text" name="emailto" value="$setAr[1]" size="40" />
</div>

<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<strong>Email address used as the "From" for all outgoing emails</strong><br />
This will use your General Settings Email Address if not filled in with something else.
<br /><input type="text" name="emailfrom" value="$setAr[2]" size="40" />
</div>

<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<input type="checkbox" name="devCredit" value="1" $setAr[3] />&nbsp;<strong>Show TD Ticket System developer credit link</strong>
<br />Not checking this box is akin to slaughtering kittens, so please consider it...for the kittens sake.
</div>

<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<strong>Number of tickets per page in admin panel</strong><br />
Defaults to 10 if left blank.
<br /><input type="text" name="tpp" size="3" value="$setAr[4]" />
</div>

<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<input type="checkbox" name="useRC" value="1" $setAr[6] />&nbsp;
<strong>Use <a href="https://www.google.com/recaptcha/" target="_blank">ReCaptcha</a> on Contact Form</strong>
</div>
<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<strong>If using ReCaptcha - Your Public Key</strong><br />
<input type="text" name="rcPubKey" value="$setAr[7]" size="40" />
</div>
<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<strong>If using ReCaptcha - Your Private Key</strong><br />
<input type="text" name="rcPriKey" value="$setAr[8]" size="40" />
</div>

<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<strong>Wordpress Menu Position</strong><br />
The lower the number the higher the Ticket System will show in your WP admin menu.<br />
<br /><input type="text" name="menuPos" size="3" value="$menuPos" /> Default position is 26 if blank.
</div>

<div style="padding:5px;border:1px solid #c0c0c0;margin-bottom:5px;">
<input type="checkbox" name="likesPizza" value="1" $setAr[5] />&nbsp;
<strong>I like pizza</strong> ... this setting has no current purpose, but our developers
feel it may be an extremely important thing to know in the future.
</div>

<input type="hidden" name="action" value="td_tts_save_configs" />
</form>
<br />
<div style="text-align:center"><button onclick="document.tdttsconfigform.submit();">Save Configuration</button></div>
</div>
<div style="float:left;width:400px;padding:15px 0 0 25px;text-align:center">
	<div style="text-align:center;padding:25px;background:#e1e1e1;border-radius:15px;margin-top:25px;margin-right:25px;">
	<h2>Wanna Support This Project?</h2>
	If you like this plugin and would like to see continued development, please consider
	a donation. Thank you!<br /><br />
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="PBM7V2TGX9AM6">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	<h3>Like this, but need something customized?</h3>
	<a href="http://www.transcendevelopment.com/contact_transcendev.html">Drop me a line</a>
    </div>
</div>
<div style="clear:both"></div>
HTML;
}
//---------------------------------------------------------//
function td_tts_loadSomeStuff() {
//---------------------------------------------------------//
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-draggable');
wp_enqueue_script('jquery-ui-resizable');

$installed_ver = get_option( "td_tts_db_version" );
if ($installed_ver != TDTTSVER) {
    update_option( "td_tts_db_version", TDTTSVER );
}

}
//---------------------------------------------------------//
function td_tts_ajaxAdminActions() {
//---------------------------------------------------------//    
?>
<link href="<? echo TDPLUGINURL ?>includes/smoothness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css" />
<script>
jQuery(function ($) {
    $( "#td_tts_resizeWindow" ).resizable();
    $( "#td_tts_resizeWindow" ).draggable();
});
</script>
<script>
function td_tts_showTemp(x) {
    document.getElementById("td_tts_contactFormBox").style.display='none';
    document.getElementById("td_tts_loginFormBox").style.display='none';
    document.getElementById("td_tts_notificationBox").style.display='none';
    document.getElementById("td_tts_responsenoteBox").style.display='none';
    document.getElementById("td_tts_successPageBox").style.display='none';
    document.getElementById("td_tts_stylesBox").style.display='none';

    document.getElementById('td_tts_showTemplate').value = x;
    document.getElementById(x+"Box").style.display='block';

}
function td_tts_adminMessageDelete(x) {
            var ticket = x;
            document.getElementById("td_tts_loadingMessage").innerHTML='Deleting...';
            document.getElementById("tdtts_loading_panel").style.display='block';
            document.getElementById("td_tts_resizeWindow").style.display='none';
            document.getElementById("tdtts_message_panel").style.display='none';
            var data = {
                    action: 'td_tts_adminMessageDelete',
                    ticket: ticket
            };
            jQuery.post(ajaxurl, data, function(response) {
                   document.getElementById("tdtts_loading_panel").style.display='none';
                   document.getElementById("td_tts_mainView").innerHTML = response;
            });
}
function td_tts_openMessage(x) {
            document.getElementById("td_tts_loadingMessage").innerHTML='Opening Message';
            document.getElementById("tdtts_loading_panel").style.display='block';
            document.getElementById("td_tts_resizeWindow").style.display='block';
            var data = {
                    action: 'td_tts_messageShow',
                    thisMessage: x
            };
            jQuery.post(ajaxurl, data, function(response) {
                   document.getElementById("tdtts_loading_panel").style.display='none';
                   document.getElementById("tdtts_message_panel").style.display='block';
                   document.getElementById("tdtts_message_panel").innerHTML = response;
            });
}
function td_tts_adminMessageResponse(x) {
            var ticket = document.getElementById('thisTicket').value;
            var theMess = document.getElementById('response').value;
            var tStatus = document.getElementById('status').value;
            document.getElementById("td_tts_loadingMessage").innerHTML='Sending Response';
            document.getElementById("tdtts_loading_panel").style.display='block';
            document.getElementById("td_tts_resizeWindow").style.display='none';
            document.getElementById("tdtts_message_panel").style.display='none';
            var data = {
                    action: 'td_tts_adminMessageResponse',
                    ticket: ticket,
                    theMess: theMess,
                    tStatus: tStatus
            };
            jQuery.post(ajaxurl, data, function(response) {
                   document.getElementById("tdtts_loading_panel").style.display='none';
                   document.getElementById("td_tts_mainView").innerHTML = response;
            });
}
</script>
<?php
}
//---------------------------------------------------------//
function td_tts_userTopLinks() {
//---------------------------------------------------------//    
?>    
<link href="<? echo TDPLUGINURL ?>includes/smoothness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css" />
<script>
jQuery(function ($) {
    $( "#td_tts_resizeWindow" ).resizable();
    $( "#td_tts_resizeWindow" ).draggable();
});
</script>
<?  
}
//---------------------------------------------------------//
function td_tts_userAjax() {
//---------------------------------------------------------//
?>
<script type="text/javascript" >
function td_tts_userSendMessage(a,b,c,d,e,f,g) {

            var data = {
                    action: 'td_tts_userMessage',
                    name: a,
                    email: b,
                    subject: c,
                    message: d,
                    returnURL: e,
                    recaptcha_challenge_field: f,
                    recaptcha_response_field: g                    
            };
            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                   document.getElementById("ticketSystemForm").innerHTML = response;
            });
}
function td_tts_userLogIn(a, b, c) {
            var data = {
                    action: 'td_tts_userMessagePanel',
                    email: a,
                    messID: b,
                    returnURL: c
            };
            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                   document.getElementById("ticketSystemForm").innerHTML = response;
            });    
}
function td_tts_UserOpenMessage(x) {
            document.getElementById("td_tts_loadingMessage").innerHTML='Opening Message';
            document.getElementById("tdtts_loading_panel").style.display='block';
            document.getElementById("td_tts_resizeWindow").style.display='block';
            var data = {
                    action: 'td_tts_UserMessageShow',
                    thisMessage: x
            };
            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                   document.getElementById("tdtts_loading_panel").style.display='none';
                   document.getElementById("tdtts_message_panel").style.display='block';
                   document.getElementById("tdtts_message_panel").innerHTML = response;
            });
}
function td_tts_userMessageResponse(x) {
            var ticket = document.getElementById('thisTicket').value;
            var theMess = document.getElementById('response').value;
            document.getElementById("td_tts_loadingMessage").innerHTML='Sending Response';
            document.getElementById("tdtts_loading_panel").style.display='block';
            document.getElementById("td_tts_resizeWindow").style.display='none';
            document.getElementById("tdtts_message_panel").style.display='none';
            var data = {
                    action: 'td_tts_userMessageResponse',
                    ticket: ticket,
                    theMess: theMess
            };
            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                   document.getElementById("tdtts_loading_panel").style.display='none';
                   document.getElementById("td_tts_mainView").innerHTML = response;
            });
}
</script>
<?php
}
//---------------------------------------------------------//
function td_tts_userLogIn() {
//---------------------------------------------------------//
global $wpdb;

$messID         = filter_var($_POST['messID'], FILTER_SANITIZE_STRING);
$returnURL      = filter_var($_POST['returnURL'], FILTER_SANITIZE_STRING);
$email          = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if ((empty($messID)) || (empty($returnURL)) || (empty($email))) {
echo <<<"HTML"
    <h1>Error</h1>
    We couldn't find any matching tickets with the information entered.
    <br /><br />
    <a href="$returnURL?action=td_tts_ticketLogIn">Please try again.</a>
HTML;
die();
}

$thisID='';
$sqlQuery = "
    SELECT id
    FROM
"
    . MESS_TABLE .
"
    WHERE email = \"$email\" AND uid = \"$messID\"
";
$ticketFound = $wpdb->get_results($sqlQuery);
foreach ( $ticketFound as $tid ) {$thisID = $tid->id;}

if ($thisID > 0) {
    session_start();
    $_SESSION['td_tts_userLoggedIn'] = $email;
    td_tts_userMessagePanel(0);
} else {
echo <<<"HTML"
    <h1>Error</h1>
    We couldn't find any matching tickets with the information entered.
    <br /><br />
    <a href="$returnURL?action=td_tts_ticketLogIn">Please try again.</a>
HTML;
die();   
}

die();
}
//---------------------------------------------------------//
function td_tts_userError() {
//---------------------------------------------------------//
echo <<<"HTML"
    <h1>Error</h1>
    We couldn't find any matching tickets with the information entered.
    <br /><br />
    <a href="$returnURL?action=td_tts_ticketLogIn">Please try again.</a>
HTML;
die();
}
//---------------------------------------------------------//
function td_tts_userMessagePanel( $recursive ) {
//---------------------------------------------------------//
global $wpdb;

if(!isset($_SESSION)){
    session_start();
}

if ($recursive == 0) {
    echo td_tts_linkStyles();
    echo '<div id="td_tts_mainView">';
}

if(empty($_SESSION['td_tts_userLoggedIn'])) {td_tts_userError();}
$usersEmail = $_SESSION['td_tts_userLoggedIn'];
$filterBy = " WHERE email = \"$usersEmail\" ";

$sqlQuery = "
    SELECT id, name, email, subject, message, status, timestamp, department, modified, ustamp
    FROM
"
    . MESS_TABLE
    . $filterBy .
"
    ORDER BY id DESC
";
$messages = $wpdb->get_results($sqlQuery);
foreach ( $messages as $message ) {
    $mID = $message->id; $mName = $message->name; $mEmail = $message->email; $mSubject = $message->subject;
    $mMessage = $message->message; $mStatus = $message->status; $mTimestamp = $message->timestamp; $mDept = $message->department;
    $mMod = $message->modified; $mUtime = $message->ustamp;
    if ($mMod==''){$mMod = 'n/a';}    
echo <<<HTML
    <div class="td_tts_bg">
        <div class="td_tts_ticketBody">
             <div class="td_tts_ticket_header">
                <strong><a href="javascript:void(0);" onclick="td_tts_UserOpenMessage($mID);">Ticket #$mID : $mSubject</a></strong>
            </div>
            Received on: <span style="text-style:italic">$mTimestamp</span><br />
            Updated on: <span style="text-style:italic">$mMod</span><br />           
        </div>
        <div class="td_tts_statusMess">
            <strong>Status:</strong> $mStatus
        </div>
        <div style="clear:both"></div>
    </div>
HTML;
}

}
//---------------------------------------------------------//
function td_tts_userMessageResponse() {
//---------------------------------------------------------//    
global $wpdb;
$setAr = td_tts_getAllSettings();

$ticket         = filter_var($_POST['ticket'], FILTER_SANITIZE_STRING);
$theMess        = filter_var($_POST['theMess'], FILTER_SANITIZE_STRING);
$timeZone       = get_option('timezone_string');
                  date_default_timezone_set($timeZone);
$timestamp      = date('D m/d/y h:i a');
$ustamp         = time();

$sqlQuery = "
    SELECT name, email, rURL, subject, uid FROM
"
    . MESS_TABLE .
"
    WHERE id = $ticket
";
$tDetails = $wpdb->get_results($sqlQuery);
foreach ( $tDetails as $td ) {
    $theName = $td->name; $rURL = $td->rURL; $userEmail = $td->email; $subject = $td->subject; $uid = $td->uid;
}

if ($theMess!='') { # if the message is blank, don't enter it as a response.
    $sqlQuery = "INSERT INTO " . RESP_TABLE .
    "
        (tid, message, name, timestamp, ustamp)
        VALUES (\"$ticket\", \"$theMess\", \"$theName\", \"$timestamp\", \"$ustamp\")
    ";
    $response = $wpdb->get_results($sqlQuery);
    
    $status = 'Open';
    $sqlQuery2 = "UPDATE " . MESS_TABLE .
    "
        SET status = \"$status\", modified = \"$timestamp\"
        WHERE id = $ticket
    ";
    $response = $wpdb->get_results($sqlQuery2);
    
$theMessage = html_entity_decode( get_option('td_tts_notification') );
$theMessage = str_replace("[%userMessage%]", $theMess, $theMessage);
$theMessage = str_replace("[%userEmail%]", $userEmail, $theMessage);
$theMessage = str_replace("[%returnURL%]", $rURL, $theMessage);
$theMessage = str_replace("[%message_id%]", $uid, $theMessage);

$subject    = "Response Received : " . html_entity_decode($subject);
$theMessage = html_entity_decode($theMessage);
$subject    = stripslashes($subject);
$theMessage = stripslashes($theMessage);

$to = $userEmail;
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From:" . $setAr[2];
mail($to, $subject, $theMessage ,$headers);    

if ($setAr[0] == 'checked') {
    $emailTo = $setAr[1];
    if (empty($emailTo)) {$emailTo = get_option( admin_email );}
    $notificationMess = "
        A new response has been submitted. Log into your Wordpress admin to respond.<br /><br />
        Message Subject:<br />
        $subject<br /><br />
        Message Details:<br />
        $theMess 
    ";
    $to = $emailTo;
    $subject = "New Response on Ticket $ticket";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From:" . $setAr[2];
    mail($to, $subject, $notificationMess ,$headers);   
}

}

td_tts_userMessagePanel(1);
die();
}
//---------------------------------------------------------//
function td_tts_UserMessageShow() {
//---------------------------------------------------------//
global $wpdb;

        $mResponses='';
        $thisMessage = intval( $_POST['thisMessage'] );
        $sqlQuery = "SELECT * FROM " . MESS_TABLE . " WHERE id = $thisMessage";
        
        $messageDetails = $wpdb->get_results($sqlQuery);
        foreach ( $messageDetails as $md ) {
                $mFrom = $md->name; $mID = $md->id; $mTime = $md->timestamp; $mSubject = $md->subject;
                $mEmail = $md->email; $mMessage = $md->message;
                $showDate = $mTime;
                $mMessage = nl2br($mMessage);
                $mResponses;
                $sqlQuery2 = "SELECT message, name, timestamp FROM " . RESP_TABLE . " WHERE tid = $thisMessage ORDER BY ustamp ASC";
                $respDetails = $wpdb->get_results($sqlQuery2);
                foreach ( $respDetails as $rd ) {
                    $rName = $rd->name; $rMess = $rd->message; $rTime = $rd->timestamp; $rMess = nl2br($rMess);
                    $mResponses .= "
                        <div class=\"td_tts_responseBox\">
                            <div class=\"td_tts_responseHeader\">$rName responded on $rTime</div>
                            $rMess
                        </div>
                    ";                    
                }

$showThisMessage = td_tts_linkStyles();
$showThisMessage .= <<<"HTML"
         <div class="td_tts_readMessageLabel"><div class="td_tts_contentPadding"><strong>Date</strong></div></div>
         <div class="td_tts_readMessageValue"><div class="td_tts_contentPadding">$showDate</div></div>
         <div style="clear:both"></div>
         <div class="td_tts_readMessageLabel"><div class="td_tts_contentPadding"><strong>Subject</strong></div></div>
         <div class="td_tts_readMessageValue"><div class="td_tts_contentPadding">$mSubject</div></div>
         <div style="clear:both"></div>
         <div id="td_tts_messageBox">
            $mMessage
         </div>
         $mResponses
         <div style="padding:15px 0 0 0;">
            <strong>Your Response:</strong><br /><br />
            <textarea style="width:100%" rows="10" id="response"></textarea>
            <input type="hidden" id="thisTicket" value="$mID" />
            <div style="text-align:center">
                <button onclick="td_tts_userMessageResponse();">Submit Reply</button>
            </div>
         </div>
HTML;
            echo $showThisMessage;        
        }

die(); 
}
//---------------------------------------------------------//
function td_tts_userSendMessage() {
//---------------------------------------------------------//
global $wpdb;

$setAr = td_tts_getAllSettings();
$eNote = $setAr[0]; $emailTo = $setAr[1]; $emailFrom= $setAr[2];
if (!$emailFrom) {
    $emailFrom = get_option( admin_email );
}

if ($setAr[6] == 'checked') {
    require_once(TDTTSDIR.'includes/recaptchalib.php');
    $privatekey = $setAr[8];
    $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
echo <<<"HTML"
    <h1>Error</h1>
    The reCAPTCHA code wasn't entered correctly.<br />
    Please <a href="$returnURL">try again</a>.
HTML;
    die();
  }
}

$name       = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email      = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$subj       = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
$message    = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
$returnURL  = filter_var($_POST['returnURL'], FILTER_VALIDATE_URL);
$rand_id    = mt_rand();
$subTime    = time();
$timeZone   = get_option('timezone_string');
              date_default_timezone_set($timeZone);
$timestamp  = date('D m/d/y h:i a');

if ((!$name) || (!$email) || (!$subj) || (!$message) || (!$returnURL)) {
echo <<<"HTML"
    <h1>Error</h1>
    Something just didn't quite go right with your submission.<br />
    Please <a href="$returnURL">try again</a>.
HTML;
die();
} else {

$sqlQuery = "INSERT INTO " . MESS_TABLE
. "
    (name, email, subject, message, status, timestamp, uid, modified, ustamp, op, rURL)
    VALUES (\"$name\", \"$email\", \"$subj\", \"$message\", \"Pending\", \"$timestamp\", \"$rand_id\", \"\", \"$subTime\", \"\", \"$returnURL\")
";
$response = $wpdb->get_results($sqlQuery);
    
$successPage = html_entity_decode( get_option('td_tts_successPage') );  
$theMessage = html_entity_decode( get_option('td_tts_notification') );
$theMessage = str_replace("[%userMessage%]", $message, $theMessage);
$theMessage = str_replace("[%userEmail%]", $email, $theMessage);
$theMessage = str_replace("[%returnURL%]", $returnURL, $theMessage);
$theMessage = str_replace("[%message_id%]", $rand_id, $theMessage);

$subj    = "Ticket Submitted : " . html_entity_decode($subj);
$theMessage = html_entity_decode($theMessage);
$subj    = stripslashes($subj);
$theMessage = stripslashes($theMessage);

$to = $email;
$subject = $subj;
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From:" . $emailFrom;
mail($to, $subject, $theMessage ,$headers);

if ($eNote == 'checked') {
    if (empty($emailTo)) {$emailTo = get_option( admin_email );}
    $notificationMess = "
        A new ticket has been submitted. Log into your Wordpress admin to respond.<br /><br />
        Message Subject: $subject<br /><br />
        Message Details<br /><br />
        $message
    ";
    $to = $emailTo;
    $subject = 'New Ticket Submitted';
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From:" . $emailFrom;
    mail($to, $subject, $notificationMess ,$headers);   
}

echo $successPage;
die();
}

}
//---------------------------------------------------------//
function td_tts_adminMessageDelete() {
//---------------------------------------------------------//
global $wpdb;

$thisID = filter_var($_POST['ticket'], FILTER_SANITIZE_STRING);

$sqlQuery = "DELETE FROM " . MESS_TABLE
. "
    WHERE id = $thisID
";
$response = $wpdb->get_results($sqlQuery);

$sqlQuery2 = "DELETE FROM " . RESP_TABLE
. "
    WHERE tid = $thisID
";
$response = $wpdb->get_results($sqlQuery2);

td_tts_mainAdminPanel(1);
die();

}
//---------------------------------------------------------//
function td_tts_doMessageShow() {
//---------------------------------------------------------//    
global $wpdb;
	$thisMessage = intval( $_POST['thisMessage'] );
        $sqlQuery = "SELECT * FROM " . MESS_TABLE . " WHERE id = $thisMessage";
        
        $messageDetails = $wpdb->get_results($sqlQuery);
        foreach ( $messageDetails as $md ) {
                $mFrom = $md->name; $mID = $md->id; $mTime = $md->timestamp; $mSubject = $md->subject;
                $mEmail = $md->email; $mMessage = $md->message;
                $showDate = $mTime;
                $mMessage = nl2br($mMessage);
                $mResponses='';
                $sqlQuery2 = "SELECT message, name, timestamp FROM " . RESP_TABLE . " WHERE tid = $thisMessage ORDER BY ustamp ASC";
                $respDetails = $wpdb->get_results($sqlQuery2);
                foreach ( $respDetails as $rd ) {
                    $rName = $rd->name; $rMess = $rd->message; $rTime = $rd->timestamp; $rMess = nl2br($rMess);
                    $mResponses .= "
                        <div class=\"td_tts_responseBox\">
                            <div class=\"td_tts_responseHeader\">$rName responded on $rTime</div>
                            $rMess
                        </div>
                    ";                    
                }

$showThisMessage = td_tts_linkStyles();
$showThisMessage .= <<<"HTML"
         <div class="td_tts_readMessageLabel"><div class="td_tts_contentPadding"><strong>From</strong></div></div>
         <div class="td_tts_readMessageValue"><div class="td_tts_contentPadding"><a href="mailto:$mEmail">$mFrom</a></div></div>
         <div style="clear:both"></div>
         <div class="td_tts_readMessageLabel"><div class="td_tts_contentPadding"><strong>Date</strong></div></div>
         <div class="td_tts_readMessageValue"><div class="td_tts_contentPadding">$showDate</div></div>
         <div style="clear:both"></div>
         <div class="td_tts_readMessageLabel"><div class="td_tts_contentPadding"><strong>Subject</strong></div></div>
         <div class="td_tts_readMessageValue"><div class="td_tts_contentPadding">$mSubject</div></div>
         <div style="clear:both"></div>
         <div id="td_tts_messageBox">
            $mMessage
         </div>
         $mResponses
         <div style="padding:15px 0 0 0;">
            <strong>Your Response:</strong><br /><br />
            <textarea style="width:100%" rows="10" id="response"></textarea>
            <input type="hidden" id="thisTicket" value="$mID" />
            <div style="text-align:right;">
            Set Status: <select id="status"><option selected>Responded</option><option>Pending</option><option>Open</option><option>Closed</option></select>
            </div>
            <div style="text-align:center">
                <button onclick="if (confirm('This will permanently delete this entire communication. Are you sure you want to do this?')){td_tts_adminMessageDelete($mID);}else{return false;}">Delete</button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="td_tts_adminMessageResponse();">Submit Reply</button>
            </div>
         </div>
HTML;
            echo $showThisMessage;        
        }

	die(); 
}
//---------------------------------------------------------//
function td_tts_adminMessageResponse() {
//---------------------------------------------------------//
global $wpdb;
global $current_user;
get_currentuserinfo();

$setAr = td_tts_getAllSettings();

$ticket         = filter_var($_POST['ticket'], FILTER_SANITIZE_STRING);
$theMess        = filter_var($_POST['theMess'], FILTER_SANITIZE_STRING);
$status         = filter_var($_POST['tStatus'], FILTER_SANITIZE_STRING);
$timeZone       = get_option('timezone_string');
                  date_default_timezone_set($timeZone);
$timestamp      = date('D m/d/y h:i a');
$ustamp         = time();
$theName        = $current_user->display_name;
if (!$theName) {$theName = $current_user->user_login;}

if ($theMess!='') { # if the message is blank, don't enter it as a response.
    $sqlQuery = "INSERT INTO " . RESP_TABLE .
    "
        (tid, message, name, timestamp, ustamp)
        VALUES (\"$ticket\", \"$theMess\", \"$theName\", \"$timestamp\", \"$ustamp\")
    ";
    $response = $wpdb->get_results($sqlQuery);
}

$sqlQuery2 = "UPDATE " . MESS_TABLE .
"
    SET status = \"$status\", modified = \"$timestamp\"
    WHERE id = $ticket
";
$response = $wpdb->get_results($sqlQuery2);

$sqlQuery = "SELECT email, uid, subject, rURL FROM " . MESS_TABLE . " WHERE id = \"$ticket\" ";
$orgMessDetails = $wpdb->get_results($sqlQuery);
foreach ( $orgMessDetails as $omd ) {
    $userEmail = $omd->email; $messID = $omd->uid; $subject = $omd->subject; $rURL = $omd->rURL;
}

$subj = "Your ticket has received a response - re: $subject";
$theMessage = html_entity_decode( get_option('td_tts_responsenote') );
$theMessage = str_replace("[%userMessage%]", $theMess, $theMessage);
$theMessage = str_replace("[%userEmail%]", $userEmail, $theMessage);
$theMessage = str_replace("[%returnURL%]", $rURL, $theMessage);
$theMessage = str_replace("[%message_id%]", $messID, $theMessage);

$subject    = html_entity_decode($subject);
$theMessage = html_entity_decode($theMessage);
$subject    = stripslashes($subject);
$theMessage = stripslashes($theMessage);


$to = $userEmail;
$subject = $subj;
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From:" . $setAr[2];
mail($to, $subject, $theMessage ,$headers);

td_tts_mainAdminPanel(1);
die();
}
//---------------------------------------------------------//
function td_tts_hookInPage( $content ) {
//---------------------------------------------------------//
    $content = str_replace("[ticket_system]", td_tts_showMainTicketForm(), $content);
    return $content;
}
//---------------------------------------------------------//
function td_tts_showMainTicketForm() {
//---------------------------------------------------------//
global $wpdb;

$returnURL = get_page_link();
$devCredit = 0;
$setAr = td_tts_getAllSettings();
if ($setAr[3] == 'checked') {$devCredit = 1;}

$retVal = td_tts_linkStyles();

# if recaptcha is set up
if ($setAr[6] == 'checked') {
    require_once(TDTTSDIR.'includes/recaptchalib.php');
    $publickey = $setAr[7]; // you got this from the signup page
    $showCaptcha = recaptcha_get_html($publickey);
} else {
    $showCaptcha = "
        <input type='hidden' id='recaptcha_challenge_field' name='recaptcha_challenge_field' value='' />
        <input type='hidden' id='recaptcha_response_field' name='recaptcha_response_field' value='' />
    ";
}

if (isset($_GET['action'])) {$action = filter_var($_GET['action'], FILTER_SANITIZE_STRING);} else {$action='';}

$ajaxLoader = '<img src="'.TDTTSAJAXIMG.'" />';    
$retVal .= <<<HTML
<div id="td_tts_userHold">
    <div id="td_tts_resizeWindow" class="ui-widget-content">
        <div id="tdtts_loading_panel" style="display:none;">
            <div style="text-align:center;padding-top:100px;">
            <h1 id="td_tts_loadingMessage"></h1>
                $ajaxLoader
            </div>
        </div>
        <div style="text-align:right;padding:0 0 5px 0;">
            <button onclick="document.getElementById('td_tts_resizeWindow').style.display='none';
            ">Close</button>
        </div>
        <div id="tdtts_message_panel"></div>
    </div>
</div>
HTML;

if ($action == 'td_tts_ticketLogIn') {
    $retVal .= html_entity_decode( get_option('td_tts_loginForm') );
} else {
    $retVal .= html_entity_decode( get_option('td_tts_contactForm') );
}

if ($devCredit == 1) {
    $retVal .= td_tts_howdoyado();
}

if (isset($_GET['messID'])) {$messID = filter_var($_GET['messID'], FILTER_SANITIZE_STRING);} else {$messID = '';}
if (isset($_GET['email'])) {$userEmail = filter_var($_GET['email'], FILTER_SANITIZE_STRING);} else {$userEmail = '';}

$retVal = str_replace("[%returnURL%]", $returnURL, $retVal);
$retVal = str_replace("[%email%]", $userEmail, $retVal);
$retVal = str_replace("[%messID%]", $messID, $retVal);
$retVal = str_replace("[%captcha%]", $showCaptcha, $retVal);
$actions = TDPLUGINURL . 'includes/actions.js';
$retVal .= "<script language=\"javascript\" src=\"$actions\"></script>";


return $retVal;
}
//---------------------------------------------------------//
function td_tts_getDepartments( $filterdept ) {
//---------------------------------------------------------//
global $wpdb;

    $sqlQuery = "SELECT id, name FROM " . DEPT_TABLE . " ORDER BY name ASC";
    $depts = $wpdb->get_results($sqlQuery);
        foreach ( $depts as $dept ) {
                $deptName = $dept->name; $deptID = $dept->id;
                if ($deptName == $filterdept) {$retVal .= "<option selected>$deptName</option>";}
                else {$retVal .= "<option>$deptName</option>";}
        }
        
return $retVal;
}
//---------------------------------------------------------//
function td_tts_save_configs() {
//---------------------------------------------------------//
global $wpdb;
if (isset($_POST['emailNotifications'])) {$enote = filter_var($_POST['emailNotifications'], FILTER_SANITIZE_STRING);}
else {$enote='';}
if (isset($_POST['emailto'])) {$emailto = filter_var($_POST['emailto'], FILTER_SANITIZE_EMAIL);}
else {$emailto='';}
if (isset($_POST['emailfrom'])) {$emailfrom = filter_var($_POST['emailfrom'], FILTER_SANITIZE_EMAIL);}
else {$emailfrom='';}         
if (isset($_POST['devCredit'])) {$devCredit = filter_var($_POST['devCredit'], FILTER_SANITIZE_STRING);}
else {$devCredit='';}
if (isset($_POST['tpp'])) {$tpp = filter_var($_POST['tpp'], FILTER_SANITIZE_STRING);}
else {$tpp='';}
if (isset($_POST['likesPizza'])) {$likesPizza = filter_var($_POST['likesPizza'], FILTER_SANITIZE_STRING);}
else {$likesPizza='';}
if (isset($_POST['useRC'])) {$useRC = filter_var($_POST['useRC'], FILTER_SANITIZE_STRING);}
else {$useRC='';}
if (isset($_POST['rcPubKey'])) {$rcPubKey = filter_var($_POST['rcPubKey'], FILTER_SANITIZE_STRING);}
else {$rcPubKey='';}
if (isset($_POST['rcPriKey'])) {$rcPriKey = filter_var($_POST['rcPriKey'], FILTER_SANITIZE_STRING);}
else {$rcPriKey='';}
if (isset($_POST['menuPos'])) {$td_tts_menuPos = filter_var($_POST['menuPos'], FILTER_SANITIZE_STRING);}
else {$td_tts_menuPos='';}

delete_option('td_tts_menuPos');
add_option('td_tts_menuPos', $td_tts_menuPos, '', 'yes');

# Email notification setting
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"enote\" ";
$response = $wpdb->get_results($sqlQuery);
if ($enote == 1) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"enote\", \"$enote\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# Email to setting
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"emailto\" ";
$response = $wpdb->get_results($sqlQuery);
if ($emailto) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"emailto\", \"$emailto\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# Email from setting
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"emailfrom\" ";
$response = $wpdb->get_results($sqlQuery);
if ($emailfrom) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"emailfrom\", \"$emailfrom\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# Developer credit setting 
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"devCredit\" ";
$response = $wpdb->get_results($sqlQuery);
if ($devCredit == 1) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"devCredit\", \"$devCredit\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# Tickets per page setting
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"tpp\" ";
$response = $wpdb->get_results($sqlQuery);
if ($tpp > 0) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"tpp\", \"$tpp\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# Use ReCaptcha?
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"useRC\" ";
$response = $wpdb->get_results($sqlQuery);
if ($useRC > 0) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"useRC\", \"$useRC\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# Public RC Key
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"rcPubKey\" ";
$response = $wpdb->get_results($sqlQuery);
if ($rcPubKey) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"rcPubKey\", \"$rcPubKey\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# Private RC Key
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"rcPriKey\" ";
$response = $wpdb->get_results($sqlQuery);
if ($rcPriKey) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"rcPriKey\", \"$rcPriKey\") ";
    $response = $wpdb->get_results($sqlQuery);
}

# The necessary LIKES PIZZA setting
$sqlQuery = "DELETE FROM " . SET_TABLE . " WHERE setName = \"likesPizza\" ";
$response = $wpdb->get_results($sqlQuery);
if ($likesPizza > 0) {
    $sqlQuery = "INSERT INTO " . SET_TABLE . " (setName, setVal) VALUES (\"likesPizza\", \"$likesPizza\") ";
    $response = $wpdb->get_results($sqlQuery);
}

#die ($sqlQuery);
echo '<div style="color:green;padding:8px 0 8px 0;">Settings saved.</div>';
}
//---------------------------------------------------------//
function td_tts_install() {
//---------------------------------------------------------//    
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $td_tts_version = get_option('td_tts_db_version');
    if (empty($td_tts_version)) {
    $table_name1 = MESS_TABLE;
    $table_name2 = RESP_TABLE;
    $table_name3 = DEPT_TABLE;
    $table_name4 = SET_TABLE;
    
    $sql = "
      CREATE TABLE $table_name1 (
      id int(255) NOT NULL auto_increment,
      name varchar(255) NOT NULL,
      email varchar(255) NOT NULL,
      subject varchar(255) NOT NULL,
      message longtext NOT NULL,
      status varchar(255) NOT NULL,
      timestamp varchar(255) NOT NULL,
      uid varchar(255) NOT NULL,
      department varchar(255) NOT NULL,
      modified varchar(255) NOT NULL,
      ustamp int(255) NOT NULL,
      op int(255) NOT NULL,
      rURL varchar(255) NOT NULL,
      UNIQUE KEY id (id)
    )";
    dbDelta( $sql );
    
    $sql2 = "
        CREATE TABLE $table_name2 (
        id int(255) NOT NULL auto_increment,
        tid int(255) NOT NULL,
        message longtext NOT NULL,
        name varchar(255) NOT NULL,
        timestamp varchar(255) NOT NULL,
        ustamp int(255) NOT NULL,
        UNIQUE KEY id (id)
        );
    ";
    dbDelta( $sql2 );
    
    $sql3 = "
        CREATE TABLE $table_name3 (
        id INT( 255 ) NOT NULL AUTO_INCREMENT,
        name VARCHAR( 255 ) NOT NULL ,
        UNIQUE KEY id (id)
        );	
    ";
    
    dbDelta( $sql3 );
    
    $sql4 = "
        CREATE TABLE $table_name4 (
        setName varchar(50) NOT NULL,
        setVal varchar(50) NOT NULL,
        UNIQUE KEY setName (setName)
        );
    ";
    
    dbDelta( $sql4 );
    } 
    
    if ($td_tts_version != '1.0.5') {
        td_tts_populateDefaultTemplates();
    }
    add_option( "td_tts_db_version", "1.0.5" );
}
//---------------------------------------------------------//
function td_tts_howdoyado() {
//---------------------------------------------------------//    
$iconurl = TDPLUGINURL . 'images/icon.png';
$retVal = <<<"HTML"
    <div style="padding:15px 0 15px 0;">
        <img src="$iconurl" align="center" />&nbsp;
        <a href="http://www.transcendevelopment.com" target="_blank">TD Ticket System</a>
    </div>
HTML;
return $retVal;
}
//---------------------------------------------------------//
function td_tts_linkStyles() {
//---------------------------------------------------------//
$td_tts_stylesheet = html_entity_decode( get_option('td_tts_styles') );
$retVal = <<<"HTML"
    <style>
        $td_tts_stylesheet
    </style>
HTML;
return $retVal;
}
//---------------------------------------------------------//
function td_tts_populateDefaultTemplates() {
//---------------------------------------------------------//    

    $td_tts_styles = stripslashes( filter_var( file_get_contents(TDTTSDIR.'includes/default_styles.css'), FILTER_SANITIZE_SPECIAL_CHARS ) );
    delete_option('td_tts_styles');
    add_option('td_tts_styles', $td_tts_styles, '', 'yes');

    $td_tts_notification = stripslashes( filter_var( file_get_contents(TDTTSDIR.'includes/default_emailnotification.php'), FILTER_SANITIZE_SPECIAL_CHARS ) );
    delete_option('td_tts_notification');
    add_option('td_tts_notification', $td_tts_notification, '', 'yes');

    $td_tts_responsenote = stripslashes( filter_var( file_get_contents(TDTTSDIR.'includes/default_responsenotification.php'), FILTER_SANITIZE_SPECIAL_CHARS ) );
    delete_option('td_tts_responsenote');
    add_option('td_tts_responsenote', $td_tts_responsenote, '', 'yes');

    $td_tts_contactForm = stripslashes( filter_var( file_get_contents(TDTTSDIR.'includes/default_form.php'), FILTER_SANITIZE_SPECIAL_CHARS ) );
    delete_option('td_tts_contactForm');
    add_option('td_tts_contactForm', $td_tts_contactForm, '', 'yes');

    $td_tts_loginForm = stripslashes( filter_var( file_get_contents(TDTTSDIR.'includes/default_login.php'), FILTER_SANITIZE_SPECIAL_CHARS ) );
    delete_option('td_tts_loginForm');
    add_option('td_tts_loginForm', $td_tts_loginForm, '', 'yes');

    $td_tts_successPage = stripslashes( filter_var( file_get_contents(TDTTSDIR.'includes/default_success.php'), FILTER_SANITIZE_SPECIAL_CHARS ) );
    delete_option('td_tts_successPage');
    add_option('td_tts_successPage', $td_tts_successPage, '', 'yes');
    
}
//---------------------------------------------------------//
function td_tts_getAllSettings() {
//---------------------------------------------------------//
global $wpdb;
    $enote=''; $eto=''; $efrom=''; $devcred=''; $tpp=''; $likesPizza=''; $useRC=''; $rcPubKey=''; $rcPriKey='';
    $sqlQuery = "SELECT setVal, setName FROM " . SET_TABLE;
    $settings = $wpdb->get_results($sqlQuery);
    foreach ( $settings as $setting ) {
        if ($setting->setName == 'enote') {$enote = 'checked';}
        if ($setting->setName == 'devCredit') {$devcred = 'checked';}
        if ($setting->setName == 'emailto') {$eto = $setting->setVal;}
        if ($setting->setName == 'emailfrom') {$efrom = $setting->setVal;}
        if ($setting->setName == 'tpp') {$tpp = $setting->setVal;}
        if ($setting->setName == 'likesPizza') {$likesPizza = 'checked';}
        if ($setting->setName == 'useRC') {$useRC = 'checked';}
        if ($setting->setName == 'rcPubKey') {$rcPubKey = $setting->setVal;}
        if ($setting->setName == 'rcPriKey') {$rcPriKey = $setting->setVal;}
    }
    return array($enote, $eto, $efrom, $devcred, $tpp, $likesPizza, $useRC, $rcPubKey, $rcPriKey); 
}

?>