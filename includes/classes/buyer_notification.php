<?php
// Buyer Notification class

class BuyerNotification extends Primary
{
    protected static $table_name = 'buyer_notification';
    protected static $col_names = ['message', 'date', 'is_read', 'fk_buyer_id'];
    protected static $table_id = 'id';
    public $id;
    public $message;
    public $date;
    public $is_read;
    public $fk_buyer_id;
}