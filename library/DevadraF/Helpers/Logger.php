<?php
/**
 * Voici les options possible :
 *  - mode : Mode de gestion du fichier (voir doc php)
 * @param $file
 * @param array $options
 *
 */
class Logger
{

    private $_handler;
    private $_file;
    private $_options = array(
        "mode" => "a"
    );

    /**
     * Voici les options possible :
     *  - mode : Mode de gestion du fichier (voir doc php)
     * @param $file
     * @param array $options
     *
     */
    public function __construct($file, $options = array())
    {

        foreach ($options as $key => $opt)
        {
            $this->_options[$key] = $opt;
        }

        try{
            $this->_file = $file;
            $this->_handler = fopen($this->_file, $this->_options["mode"]);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }

    }

    /**
     * @param string $mess
     * @param string $type
     * @param bool $return_line
     */
    public function write($mess, $type = "Info",  $return_line = true)
    {

        if($return_line) $mess.="\r\n";

        $d = new DateTime();
        $full_message = $type . " : " . Generale::getRealIpAddr() . " [" . $d->format("d/M/Y:H:i:s Z") . "] : " . $mess;


        fwrite($this->_handler, $full_message);
    }

    public function close()
    {
        fclose($this->_handler);
    }

    public function open()
    {
        try{
            $this->_handler = fopen($this->_file, "a");
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }




}