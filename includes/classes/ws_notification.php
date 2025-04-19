<?php
// Wholesaler Notification class

class WsNotification extends Primary
{
    protected static $table_name = 'ws_notification';
    protected static $col_names = ['message', 'date', 'is_read', 'fk_ws_id'];
    protected static $table_id = 'id';
    public $id;
    public $message;
    public $date;
    public $is_read;
    public $fk_ws_id;
}