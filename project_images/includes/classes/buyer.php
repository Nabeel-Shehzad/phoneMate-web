<?php
// Buyer class

class Buyer extends Primary
{
    protected static $table_name = 'buyer';
    protected static $col_names = ['buyer_name', 'buyer_address', 'buyer_contact', 'buyer_cnic', 'buyer_image', 'buyer_email', 'buyer_password', 'buyer_status', 'fk_bd_id', 'fk_rider_id'];
    protected static $table_id = 'buyer_id';
    public $buyer_id;
    public $buyer_name;
    public $buyer_address;
    public $buyer_contact;
    public $buyer_cnic;
    public $buyer_image;
    public $buyer_email;
    public $buyer_password;
    public $buyer_status;
    public $fk_bd_id;
    public $fk_rider_id;
}