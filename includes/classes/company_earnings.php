<?php
// Company Earnings class

class CompanyEarnings extends Primary
{
    protected static $table_name = 'company_earnings';
    protected static $col_names = ['earning_amount', 'date', 'fk_delivery_id'];
    protected static $table_id = 'earning_id';
    public $earning_id;
    public $earning_amount;
    public $date;
    public $fk_delivery_id;
}