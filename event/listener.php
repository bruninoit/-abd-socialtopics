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
public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\auth\auth $auth, \phpbb\user $user, $root_path, $phpEx) 
{
   $this->config = $config;
   $this->db = $db;
   $this->template = $template; 
   $this->auth = $auth;
   $this->user = $user;
   $this->root_path = $root_path;
   $this->phpEx   = $phpEx ;
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
);	
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
$l_topic_list=$this->user->lang['TOPIC_TITLE']; //da cambiare
$l_topic_no=$this->user->lang['TOPIC_TITLE']; //da cambiare
$l_topic_title=$this->user->lang['TOPIC_TITLE'];
$l_topic_author=$this->user->lang['TOPIC_AUTHOR'];
$l_topic_date=$this->user->lang['TOPIC_DATE'];
define("TOPIC_LIST", "$l_topic_list");
define("TOPIC_TITLE", "$l_topic_title");
define("TOPIC_AUTHOR", "$l_topic_author");
define("TOPIC_DATE", "$l_topic_date");
define("NO_TOPIC", "$l_topic_no");


//$array_topic_data=$event['post_row'];
$rowmessage=$event['post_row'];
$message=$rowmessage['MESSAGE'];
$post_id=$rowmessage['POST_ID'];
$message=str_replace("#","[ht]",$message);
preg_match_all("(\[ht\](.*?) )", $message, $matches);
for($n=0;$n<count($matches[1]);$n++)
{
$ht_testo=$matches[1][$n];
$message=str_replace("[ht]$ht_testo","<a href=\"app.php/ht/$ht_testo\">#$ht_testo</a>",$message);
}
$rowmessage['MESSAGE']=$message;
$event['post_row'] = $rowmessage;
}
}
