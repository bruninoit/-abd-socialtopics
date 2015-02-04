<?php
/** 
* 
* @package StaffIt - Toic List 
* @copyright (c) 2014 brunino
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2 
* 
*/ 
namespace bruninoit\socialtopics\event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */	
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\user */
	protected $user;
	protected $root_path;
	
	protected $phpEx;
/** 
 	* Constructor 
 	* 
 	* @param \phpbb\config\config   		$config             	 Config object 
 	* @param \phpbb\db\driver\driver_interface      $db        	 	 DB object 
 	* @param \phpbb\template\template    		$template  	 	 Template object 
 	* @param \phpbb\auth\auth      			$auth           	 Auth object 
 	* @param \phpbb\use		     		$user           	 User object 
 	* @param	                		$root_path          	 Root Path object 
 	* @param                  	     		$phpEx          	 phpEx object 
 	* @return \staffit\toptentopics\event\listener 
 	* @access public 
 	*/ 
public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\auth\auth $auth, \phpbb\user $user, $root_path, $phpEx, \phpbb\notification\manager $notification_manager) 
{
   $this->config = $config;
   $this->db = $db;
   $this->template = $template; 
   $this->auth = $auth;
   $this->user = $user;
   $this->root_path = $root_path;
   $this->phpEx   = $phpEx ;
   $this->notification_manager = $notification_manager;
}
/** 
 	* Assign functions defined in this class to event listeners in the core 
 	* 
 	* @return array 
 	* @static 
 	* @access public 
 	*/ 
static public function getSubscribedEvents()	
{
return array(			
'core.user_setup'						=> 'setup',
'core.viewtopic_modify_post_row' => 'viewtopic_add',
'core.submit_post_end' => 'notification_usertag'
);	
}

public function notification_usertag($event)	{
	$post_id=$event['data']['post_id'];
	$forum_id=$event['data']['forum_id'];
	$poster_id=$event['data']['poster_id'];
	$topic_id=$event['data']['topic_id'];
	$post_subject=$event['subject'];
	$message=$event['data']['message'];
	$message=str_replace("@","[ut]",$message);
	preg_match_all("(\[ut\](.*?) )", $message, $users);
for($n=0;$n<count($users[1]);$n++)
{
$ut_testo=$users[1][$n];
$query=$this->db->sql_query("SELECT user_id FROM " . USERS_TABLE . " WHERE username=\"$ut_testo\"");
$user_date=$this->db->sql_fetchrow($query);
$user_id=$user_date['user_id'];
if($user_id)
{
               $my_notification_data = array(
                  'user_id'   => (int) $user_id,
                  'post_id'   => $post_id,
                  'poster_id'   => $poster_id,
                  'topic_id'   => (int) $topic_id,
                  'forum_id'   => (int) $forum_id,
                  'time'   => time(),
                  'username'   => $ut_testo,
                  'post_subject'   => $post_subject,
               );

               $this->notification_manager->add_notifications(array(
                  'my_cool_notification',
               ), $my_notification_data);
}
}
	

}

public function setup($event)	{	
//language start
$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'staffit/topiclist',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
}
public function viewtopic_add($event)	
{
//costanti di lingua momentanee
//$l_topic_list=$this->user->lang['TOPIC_TITLE']; //da cambiare
//define("TOPIC_LIST", "$l_topic_list");



//$array_topic_data=$event['post_row'];
$rowmessage=$event['post_row'];
$message=$rowmessage['MESSAGE'];
$post_id=$rowmessage['POST_ID'];
$message=str_replace("#","[ht]",$message);
$message=str_replace("@","[ut]",$message);

//hastag
preg_match_all("(\[ht\](.*?) )", $message, $matches);
for($n=0;$n<count($matches[1]);$n++)
{
$ht_testo=$matches[1][$n];
$message=str_replace("[ht]$ht_testo","<a href=\"{$this->root_path}search.{$this->phpEx}?keywords=%23$ht_testo&sc=1&sf=msgonly&sr=posts&sk=t&sd=d&st=0&ch=300&t=0&submit=Cerca\">#$ht_testo</a>",$message);


}

//persone tag
preg_match_all("(\[ut\](.*?) )", $message, $users);
for($n=0;$n<count($users[1]);$n++)
{
$ut_testo=$users[1][$n];
$query=$this->db->sql_query("SELECT user_id FROM " . USERS_TABLE . " WHERE username=\"$ut_testo\"");
$user_date=$this->db->sql_fetchrow($query);
$user_id=$user_date['user_id'];
if($user_id)
{
$message=str_replace("[ut]$ut_testo","<a href=\"{$this->root_path}memberlist.{$this->phpEx}?mode=viewprofile&u=$user_id\">@$ut_testo</a>",$message);
}
}

$message=str_replace("[ut]","@",$message);
$message=str_replace("[ht]","#",$message);
$rowmessage['MESSAGE']=$message;
$event['post_row'] = $rowmessage;
}
}
