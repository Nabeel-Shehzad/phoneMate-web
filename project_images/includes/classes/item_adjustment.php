<?php
// Item Rejection class

class ItemAdjustment extends Primary
{
    protected static $table_name = 'item_adjustment';
    protected static $col_names = ['item_adj_price', 'pieces_pu', 'item_tag', 'fk_item_id'];
    protected static $table_id = 'item_adj_id';
    public $item_adj_id;
    public $item_adj_price;
    public $pieces_pu;
    public $item_tag;
    public $fk_item_id;
}