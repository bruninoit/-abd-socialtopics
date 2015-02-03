<?php
class my_cool_notification extends \phpbb\notification\type\base
{
public function get_type()
   {
      return 'my_cool_notification';
   }
   
   protected $language_key = 'MY_NOTIFICATION_TEXT';
   
      public function is_available()
   {
      return true;
   }
   
      /**
   * Get the id of the item
   *
   * @param array $my_notification_data The data from the post
   */
   public static function get_item_id($my_notification_data)
   {
      return (int) $my_notification_data['post_id'];
   }


   /**
   * Get the id of the parent
   *
   * @param array $my_notification_data The data from the topic
   */
   public static function get_item_parent_id($my_notification_data)
   {
      return (int) $my_notification_data['topic_id'];
   }


   /**
   * Find the users who want to receive notifications
   *
   * @param array $my_notification_data The data from the post
   * @param array $options Options for finding users for notification
   *
   * @return array
   */
   public function find_users_for_notification($my_notification_data, $options = array())
   {
      $options = array_merge(array(
         'ignore_users'      => array(),
      ), $options);

      $users = array((int) $my_notification_data['poster_id']);

      return $this->check_user_notification_options($users, $options);
   }
   
      /**
   * Get the user's avatar
   */
   public function get_avatar()
   {
      return $this->user_loader->get_avatar($this->get_data('user_id'));
   }
   
      /**
   * Get the HTML formatted title of this notification
   *
   * @return string
   */
   public function get_title()
   {
      $username = $this->user_loader->get_username($this->get_data('user_id'), 'no_profile');

      return $this->user->lang($this->language_key, $username);
   }
   
      /**
   * Get the url to this item
   *
   * @return string URL
   */
   public function get_url()
   {
      return append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, "p={$this->item_id}#p{$this->item_id}");
   }
   
   
      public function get_redirect_url()
   {
      return $this->get_url();
   }
   
      /**
   * Get email template
   *
   * @return string|bool
   */
   public function get_email_template()
   {
      return '@bruninoit_socialtopics/my_notification_email';
   }
   
   
   
      /**
   * Get the HTML formatted reference of the notification
   *
   * @return string
   */
   public function get_reference()
   {
      return $this->user->lang(
         'NOTIFICATION_REFERENCE',
         censor_text($this->get_data('post_subject'))
      );
   }
   
   
   
      /**
   * Get email template variables
   *
   * @return array
   */
   public function get_email_template_variables()
   {
      $user_data = $this->user_loader->get_user($this->get_data('poster_id'));

      return array(
            'NOTIFICATION_SUBJECT'   => htmlspecialchars_decode($this->get-title()),
            'USERNAME'      => htmlspecialchars_decode($this->user->data['username']),
            'U_LINK'   => generate_board_url() . '/viewtopic.' . $this->php_ext . "?p={$this->item_id}#p{$this->item_id}",
      );
   }
   
   
      /**
   * Function for preparing the data for insertion in an SQL query
   * (The service handles insertion)
   *
   * @param array $my_notification_data Data from insert_thanks
   * @param array $pre_create_data Data from pre_create_insert_array()
   *
   * @return array Array of data ready to be inserted into the database
   */
   public function create_insert_array($my_notification_data, $pre_create_data = array())
   {
      $this->set_data('user_id', $my_notification_data['user_id']);
      $this->set_data('post_id', $my_notification_data['post_id']);
      $this->set_data('post_subject', $my_notification_data['post_subject']);

      return parent::create_insert_array($my_notification_data, $pre_create_data);
   }

}
