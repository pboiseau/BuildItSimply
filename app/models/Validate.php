<?php

class Validate extends Audit
{

    /**
     * @param $data
     * @param null $length
     * @return bool
     */
    public function isString($data, $length = null)
    {
        $check = preg_match("#^[a-zA-Z éèàùç]{1,}$#", $data);
        $length = (!empty($length)) ? (strlen($data) <= $length) : true;
        return (($check == 1) && $length);
    }

    /**
     * @param $data
     * @param null $length
     * @return bool
     */
    public function isNumber($data, $length = null)
    {
        $check = preg_match("#^[0-9]{1,}$#", $data);
        $length = (!empty($length)) ? (strlen($data) <= $length) : true;
        return (($check == 1) && $length);
    }

    /**
     * @param $data
     * @param null $length
     * @return bool
     */
    public function isPhone($data, $length = null)
    {
        $check = preg_match("#^[+0-9][0-9]{0,16}$#", $data);
        $length = (!empty($length)) ? (strlen($data) <= $length) : true;
        return (($check == 1) && $length);
    }

    /**
     * @param $data
     * @param null $entropy
     * @param null $length
     * @return bool
     */
    public function isPassword($data, $entropy = null, $length = null)
    {
        $strong = (!empty($entropy) && ($this->entropy($data) >= $entropy));
        $check = preg_match("#^[^ ;'\"]{8,25}#", $data);
        $length = (!empty($length)) ? (strlen($data) <= $length) : true;
        return (($check == 1) && $length && $strong);
    }

    /**
     * @param $data
     * @param null $length
     * @return bool
     */
    public function isKeyword($data, $length = null)
    {
        $check = preg_match("#^[^.;\"]{1,}#", $data);
        $length = (!empty($length)) ? (strlen($data) <= $length) : true;
        return (($check == 1) && $length);
    }





}

?>