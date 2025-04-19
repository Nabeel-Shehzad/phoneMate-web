<?php
// Wholesaler Payment Records class

class WsPaymentRecords extends Primary
{
    protected static $table_name = 'ws_payment_records';
    protected static $col_names = ['wsr_image', 'wsr_paid', 'date', 'fk_ws_id'];
    protected static $table_id = 'wsr_id';
    public $wsr_id;
    public $wsr_image;
    public $wsr_paid;
    public $date;
    public $fk_ws_id;
}