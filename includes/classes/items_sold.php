<?php
// Items Sold class

class ItemsSold extends Primary
{
    protected static $table_name = 'items_sold';
    protected static $col_names = ['sell_price', 'sell_quantity', 'sell_date', 'sell_status', 'fk_buyer_id', 'fk_item_id'];
    protected static $table_id = 'sell_id';
    public $sell_id;
    public $sell_price;
    public $sell_quantity;
    public $sell_date;
    public $sell_status;
    public $fk_buyer_id;
    public $fk_item_id;
}