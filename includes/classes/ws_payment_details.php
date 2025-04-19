<?php
// Wholesaler Payment Details class

class WsPaymentDetails extends Primary
{
    protected static $table_name = 'ws_payment_details';
    protected static $col_names = ['wsp_amount', 'wsp_paid', 'date', 'fk_ws_id', 'fk_delivery_id', 'fk_wsr_id'];
    protected static $table_id = 'wsp_id';
    public $wsp_id;
    public $wsp_amount;
    public $wsp_paid;
    public $date;
    public $fk_ws_id;
    public $fk_delivery_id;
    public $fk_wsr_id;
}