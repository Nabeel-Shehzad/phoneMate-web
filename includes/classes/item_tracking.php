<?php
// Item Tracking class

class ItemTracking extends Primary
{
    protected static $table_name = 'item_tracking';
    protected static $col_names = ['fk_wh_id', 'fk_ws_id'];
    protected static $table_id = 'item_tracking_id';
    public $item_tracking_id;
    public $fk_wh_id;
    public $fk_ws_id;
}
