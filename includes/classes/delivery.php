<?php
// Delivery class

class Delivery extends Primary
{
    protected static $table_name = 'delivery';
    protected static $col_names = ['delivery_quantity', 'total_cash', 'delivery_status', 'delivery_date', 'fk_item_adj_id', 'fk_buyer_id'];
    protected static $table_id = 'delivery_id';
    public $delivery_id;
    public $delivery_quantity;
    public $total_cash;
    public $delivery_status;
    public $delivery_date;
    public $fk_item_adj_id;
    public $fk_buyer_id;
}