<?php
class ev_AiMessages extends Basic
{
    public $new_schema = true;
    public $module_dir = 'ev_AiMessages';
    public $object_name = 'ev_AiMessages';
    public $table_name = 'ev_aimessages';
    public $importable = true;
    public $id;
    public $conversation_id;
    public $text;
    public $author_id;
    public $date_entered;
    public $deleted;

    public function bean_implements($interface)
    {
        $result = false;
        if ('ACL' === $interface) {
            $result = true;
        }
        return $result;
    }
}
