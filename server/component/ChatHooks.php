<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../component/BaseHooks.php";

/**
 * The class to define the hooks for the plugin.
 */
class ChatHooks extends BaseHooks
{
    /* Constructors ***********************************************************/

    /**
     * The constructor creates an instance of the hooks.
     * @param object $services
     *  The service handler instance which holds all services
     * @param object $params
     *  Various params
     */
    public function __construct($services, $params = array())
    {
        parent::__construct($services, $params);
    }

    /* Private Methods *********************************************************/

    /**
     * Checks whether user has access to the chat. If not later the icon is not visualized
     *
     * @param string $key
     *  The page name of the chat; either "chatTherapist" or "chatSubject"
     * @retval bool
     *  True if the user has access to the chat.
     */
    private function has_access_to_chat($key){
        return $this->acl->has_access_select($_SESSION['id_user'], $this->db->fetch_page_id_by_keyword($key)); 
    }

    /**
     * Get the first group in which the user has chat permisions
     * @retval array
     * The group
     */
    private function get_chat_first_chat_group(){
        $sql = "SELECT ug.id_groups
                FROM users_groups ug
                INNER JOIN acl_groups acl ON (acl.id_groups = ug.id_groups)
                INNER JOIN pages p ON (acl.id_pages = p.id)
                WHERE id_users = :uid AND keyword = 'chatSubject' AND acl_select = 1 AND ug.id_groups > 2
                ORDER BY ug.id_groups ASC";
        return $this->db->query_db_first($sql, array(":uid"=>$_SESSION['id_user']));
    }

    /**
     * Return the number of new messages.
     *
     * @retval int
     *  The number of new messages.
     */
    private function get_new_message_count()
    {
        $sql = "SELECT count(cr.id_chat) AS count FROM chatRecipiants AS cr
            WHERE cr.is_new = '1' AND cr.id_users = :uid";
        $res = $this->db->query_db_first($sql,
            array(":uid" => $_SESSION['id_user']));
        if($res)
            return intval($res['count']);
        else
            return 0;
    }

    /**
     * Render the pill indicating new messages.
     */
    private function output_new_messages()
    {
        $count = $this->get_new_message_count();
        if($count)
            require __DIR__ .'/tpl_new_messages.php';
    }

    /* Public Methods *********************************************************/

    /**
     * Output an item in nav-right    
     */
    public function outputNavRight()
    {
        $key = '';
        if ($this->has_access_to_chat('chatTherapist')) {
            $key = 'chatTherapist';
        } else if ($this->has_access_to_chat('chatSubject')) {
            $key = 'chatSubject';
        } else {
            return;
        }
        $active = ($this->router->is_active($key)) ? "active" : "";
        $group =  $this->get_chat_first_chat_group();
        if (!$group) {
            // if there is no chat group do not show
            return;
        }
        $url = $this->get_link_url($key, array("gid" => intval($group['id_groups'])));
        require __DIR__ . '/tpl_chat.php';
    }
}
?>
