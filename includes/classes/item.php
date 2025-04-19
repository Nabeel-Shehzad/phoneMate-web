<?php
// Item class

class Item extends Primary
{
    protected static $table_name = 'items';
    protected static $col_names = ['item_category', 'item_brand', 'item_number', 'item_description', 'item_image', 'item_price', 'item_quantity', 'more_quantity', 'item_sold', 'agreement_date', 'item_profit', 'item_status', 'fk_item_tracking_id'];
    protected static $table_id = 'item_id';
    public $item_id;
    public $item_category;
    public $item_brand;
    public $item_number;
    public $item_description;
    public $item_image;
    public $item_price;
    public $item_quantity;
    public $more_quantity;
    public $item_sold;
    public $agreement_date;
    public $item_profit;
    public $item_status;
    public $fk_item_tracking_id;
}