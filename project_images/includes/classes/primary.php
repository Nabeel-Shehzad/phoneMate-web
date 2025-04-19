<?php
// the primary class

class Primary {
    protected static $table_name;
    protected static $col_names;
    protected static $table_id;

    // insert data into table
    public function insert() {
        $arr = $this->clean_properties();

        $query = "INSERT INTO " . static::$table_name . "(" . implode(",", array_keys($arr)) .") ";
        $query .= "VALUES('" . implode("','", array_values($arr)) . "')";

        if ($this->use_query($query)) {
            return true;
        }else{
            return false;
        }
    }

    // update data of table
    public function update ($id = 0) {
        $get_arr = $this->clean_properties();
        $arr = [];
        foreach ($get_arr as $key => $value) {
            $arr[] = "". $key . "='" . $value . "'";
        }
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        // print_r($arr);
        // echo implode(",", $arr);
        $id_name = static::$table_id;
        $query = "UPDATE " . static::$table_name . " SET " . implode(",", $arr) . " ";
        $query .= "WHERE " . static::$table_id . "='" . $this->$id_name . "'";

        if ($this->use_query($query)){
            return true;
        }else{
            return false;
        }
    }

    // delete data from table
    public function delete () {
        $id_name = static::$table_id;
        $query = "DELETE FROM ". static::$table_name . " WHERE ";
        $query .= "" . static::$table_id . "=" . $this->$id_name ."";

        if ($this->use_query($query)) {
            return true;
        }else{
            return false;
        }
    }

    // getting the properties and cleaning them
    protected function clean_properties () {
        global $db;
        $arr = array();
        $cols = static::$col_names;
        $vars = get_object_vars($this);
        foreach ($cols as $col) {
            if (array_key_exists($col, $vars) && !empty($vars[$col])) {
                $arr[$col] = $db->escape($this->$col);
            }
        }
        return $arr;
    }

    // function to use the DB class query function
    private function use_query ($query) {
        global $db;
        return $db->query($query);
    }
}