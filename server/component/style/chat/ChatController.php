<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../../../component/BaseController.php";
/**
 * The controller class of the chat component.
 */
class ChatController extends BaseController
{
    /* Constructors ***********************************************************/

    /**
     * The constructor
     *
     * @param object $model
     *  The model instance of the component.
     */
    public function __construct($model)
    {
        parent::__construct($model);
        $this->fail = false;
        if(isset($_POST['msg']))
        {
            if(!$this->model->send_chat_msg(
                htmlspecialchars(strip_tags($_POST['msg']), ENT_QUOTES, 'UTF-8')))
                $this->fail = true;
        }
    }
}
?>
