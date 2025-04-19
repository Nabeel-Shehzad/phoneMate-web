<?php
// Wholesaler class

class Wholesaler extends Primary
{
    protected static $table_name = 'wholesaler';
    protected static $col_names = ['ws_name', 'ws_company_name', 'ws_home_address', 'ws_office_address', 'ws_personal_contact', 'ws_office_contact', 'ws_cnic', 'ws_image', 'ws_email', 'ws_status', 'ws_password'];
    protected static $table_id = 'ws_id';
    public $ws_id;
    public $ws_name;
    public $ws_company_name;
    public $ws_home_address;
    public $ws_office_address;
    public $ws_personal_contact;
    public $ws_office_contact;
    public $ws_cnic;
    public $ws_image;
    public $ws_email;
    public $ws_status;
    public $ws_password;
}