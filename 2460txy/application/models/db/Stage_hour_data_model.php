<?php

class Stage_hour_data_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->set_table('stage_hour_data');
    }
}
