<?php

namespace App\Helper;

class DoubleApo
{
    public function Process($user_name)
    {
        $split = explode("'", $user_name);
        if( count($split) != 1)
        {
            $user_name = '';
            $i = 1;
            foreach($split as $s)
            {
                $user_name .= $s;
                if($i != count($split))
                {
                    $user_name .= '\'\'';
                }
                $i = $i + 1;
            }
        }
        return $user_name;
    }
}