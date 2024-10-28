<?php

class ev_AiConversations extends Basic
{
    public $new_schema = true;
    public $module_dir = 'ev_AiConversations';
    public $object_name = 'ev_AiConversations';
    public $table_name = 'ev_aiconversations';
    public $importable = true;
    public $id;
    public $name;
    public $user_id;
    public $deleted;
    public $date_read;
    public $date_active;

    public function bean_implements($interface)
    {
        $result = false;
        if ('ACL' === $interface) {
            $result = true;
        }
        return $result;
    }
    
}
