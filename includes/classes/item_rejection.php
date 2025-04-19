<?php
// Item Rejection class

class ItemRejection extends Primary
{
    protected static $table_name = 'item_rejection';
    protected static $col_names = ['rejection_reason', 'fk_item_id'];
    protected static $table_id = 'rejection_id';
    public $rejection_id;
    public $rejection_reason;
    public $fk_item_id;
}