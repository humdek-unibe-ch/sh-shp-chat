<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/ChatView.php";

/**
 * The view class of the chat component.
 * The chat component is not made available to the CMS in is only used
 * internally.
 */
class ChatViewSubject extends ChatView
{
    /* Private Properties******************************************************/

    /**
     * DB field 'experimenter' (empty string)
     * The text to be displayed when addressing experimenter.
     */
    private $experimenter;

    /* Constructors ***********************************************************/

    /**
     * The constructor.
     *
     * @param object $model
     *  The model instance of the user profile component.
     * @param object $controller
     *  The controller instance of the user profile component.
     */
    public function __construct($model, $controller)
    {
        parent::__construct($model, $controller);
        $this->experimenter = $this->model->get_db_field("experimenter");
    }

    /* Protected Methods ******************************************************/

    /**
     * Render the chat messages. Place and color the messages dependeing on who
     * the author is.
     *
     * @param string $user
     *  The user name of the author.
     * @param string $msg
     *  The message.
     * @param int $uid
     *  The user id of the author.
     * @param string $datetime
     *  The date and time of the message.
     */
    protected function output_msgs_spec($user, $msg, $uid, $datetime)
    {
        $css = "";
        if($uid == $_SESSION['id_user'])
            $css = "me ml-auto";
        else
            $css .= " experimenter";
        require __DIR__ . "/tpl_chat_item.php";
    }

    /* Public Methods *********************************************************/

    /**
     * Render the chat view of the subject role.
     */
    public function output_content_spec()
    {
        $title = $this->title_prefix . " "
            . $this->experimenter;
        require __DIR__ . "/tpl_chat_subject.php";
    }

    /**
     * Render all groups that has access to chat and the subject is in them as tabs.
     */
    public function output_group_tabs(){
        $groups = $this->model->get_groups();
        foreach ($groups as $key => $group) {
            $group_id = intval($group['id']);
            $tab_name = $group['name'];
            if($tab_name == 'subject'){
                $tab_name = $this->label_global;
            } else {
                $tab_name = 'Group_' . $group_id;
            }
            $tab_css = $this->model->is_group_selected($group_id) ? 'active' : '';
            $tab_url = $this->model->get_link_url('chatSubject', array("gid" => $group_id));
            require __DIR__ . "/tpl_chat_tab.php";
        }        
    }
}
?>
