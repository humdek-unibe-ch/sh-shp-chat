<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/. */
?>
<?php
require_once __DIR__ . "/../../../../../../component/BaseComponent";
require_once __DIR__ . "/../../../../../../component/BaseModel";
require_once __DIR__ . "/../../../../../../component/BaseView";

/**
 * The group select component.
 */
class ChatComponent extends BaseComponent
{
    /* Constructors ***********************************************************/

    /**
     * The constructor.
     *
     * @param object $services
     *  An associative array holding the differnt available services. See the
     *  class definition BasePage for a list of all services.
     * @param int $id
     *  The id of the section id of the chat component.
     * @param array $params
     *  The GET parameters of the chatTherapist or chatSubject page
     *   'uid': The id of the selected user to communicate with
     *   'gid': The id of the selected group to communicate with
     *   'chrid': The id of the selected chat group to communicate with
     */
    public function __construct($services, $params)
    {
        $model = new BaseModel($services);
        $view = new BaseView($model);
        parent::__construct($model, $view);
    }
}
?>
