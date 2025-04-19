<?php
// Wholesaler Pending Payments class

class WsPendingPayments extends Primary
{
    protected static $table_name = 'ws_pending_payments';
    protected static $col_names = ['wspp_amount', 'fk_ws_id'];
    protected static $table_id = 'wspp_id';
    public $wspp_id;
    public $wspp_amount;
    public $fk_ws_id;
}