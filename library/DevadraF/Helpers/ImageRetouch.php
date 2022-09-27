<?php


class ImageRetouch
{

    protected $_src = null;
    protected $_img = null;
    protected $_compression = "tinyfy";
    private $_tinify_key = null;
    private $_shortpixel_key = null;

    /**
     * ImageRetouch constructor.
     * @param $src string
     * @param $options array
     */
    public function __construct($src, $options = array())
    {
        $this->_src = $src;

        try{
            $config = parse_ini_file("./config.ini", true);
            $this->_tinify_key = $config["imgcompression"]["tinify"];
            $this->_shortpixel_key = $config["imgcompression"]["shortpixel"];

            $this->_img = imagecreatefromstring(file_get_contents($this->_src));
        }
        catch (Exception $e)
        {
            echo "ImageRetouch Error : " . $e->getMessage();
            return;
        }

        if(!empty($options))
        {

            foreach ($options as $key => $option)
            {
                $this->{$key} = $option;
            }

        }

    }

    public function setImg($img)
    {
        $this->_img = $img;
    }

    public function setSrc($src)
    {
        $this->_src = $src;
        try{
            $this->_img = imagecreatefromstring(file_get_contents($this->_src));
        }
        catch (Exception $e)
        {
            echo "ImageRetouch Error : " . $e->getMessage();
            return;
        }
    }

    /**
     *
     * Fonction permettant de redimensionner une image
     *
     * @param $adjust string
     * @param $options array
     * @return bool|false|resource|null
     */
    public function resize($adjust, $options)
    {

        $res = null;
        $size = getimagesize($this->_src);

        switch ($adjust)
        {

            case "width":
                $res = imagescale($this->_img, $options["width"]);
                break;
            case "height":
                $rat = $options["height"] / $size[1];
                $res = imagescale($this->_img, intval($size[0] * $rat));
                break;
            case "crop":
                $res = imagecrop($this->_img, array("x" => 0, "y" => 0, "width" => $options["width"], "height" => $options["height"]));
                break;
            default:
                $res = $this->_img;
                break;

        }

        return $res;

    }

    public function compress()
    {

        $res = null;



        switch ($this->_compression)
        {
            case "shortpixel":
                $res = $this->_shortpixel();
                break;
            case "tinyfy":
            default:
                $res = $this->_tinify();
                break;
        }


        return $res;

    }

    protected function _tinify()
    {

        $res = null;

        $headers = array(
            "Authorization: Basic " . base64_encode("api:" . $this->_tinify_key),
            "Content-Type: text/plain"
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.tinify.com/shrink");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($this->_src));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $compress = curl_exec($ch);

        curl_close ($ch);

        $file = "./log/tinify.log";
        $handle = fopen($file, "w");
        fwrite($handle, $compress);
        fclose($handle);


        $compress = json_decode($compress);

        $img_url = $compress->output->url;

        $ch = curl_init();

        $headers = array(
            "Authorization: Basic " . base64_encode("api:" . $this->_tinify_key)
        );

        curl_setopt($ch, CURLOPT_URL, $img_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $res = curl_exec($ch);

        curl_close ($ch);

        /*$file = "tmp.png";
        $handle = fopen($file, "w");
        fwrite($handle, $res);
        fclose($handle);*/

        $res = imagecreatefromstring($res);

        return $res;

    }


    protected function _shortpixel()
    {

        $res = null;

        $url = "https://api.shortpixel.com/v2/post-reducer.php";
        $curl = curl_version();
        $userAgent = "ShortPixel/1.0 " . " curl/" . $curl["version"];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => $userAgent,
        ));
        $files = array(
            "img" => $this->_src,
        );
        $options = array(
            "key" => $this->_shortpixel_key,
            "lossy" => 1,
            "file_paths" => json_encode($files),
            "wait" => 5
        );

        $this->_curl_custom_postfields($ch, $options, $files);
        $res = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $res = substr($res, $header_size);

        $json = json_decode($res);
        $json = $json[0];

        /*ob_start();                    // start buffer capture
        var_dump($json);           // dump the values
        $contents = ob_get_contents(); // put the buffer into a variable
        ob_end_clean();

        $file = "./log/shortpixel.log";
        $handle = fopen($file, "w");
        fwrite($handle, $contents);
        fclose($handle);*/

        if($json->Status->Code == 1)
        {
            $res = curl_exec($ch);
            $res = substr($res, $header_size);
            $json = json_decode($res);
            $json = $json[0];
        }

        if($json->Status->Code == 1)
        {
            $res = curl_exec($ch);
            $res = substr($res, $header_size);
            $json = json_decode($res);
            $json = $json[0];
        }

        $file = "./log/shortpixel.log";
        $handle = fopen($file, "w");
        fwrite($handle, json_encode($json));
        fclose($handle);

        curl_close($ch);

        $res = imagecreatefromstring(file_get_contents("$json->LossyURL"));

        return $res;


    }

    private function _curl_custom_postfields($ch, array $assoc = array() , array $files = array() , $header = array()) {
        // invalid characters for "name" and "filename"
        static $disallow = array(
            "\0",
            "\"",
            "\r",
            "\n"
        );
        // build normal parameters
        foreach ($assoc as $k => $v) {
            $k = str_replace($disallow, "_", $k);
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; name=\"{$k}\"",
                "",
                filter_var($v) ,
            ));
        }
        // build file parameters
        foreach ($files as $k => $v) {
            switch (true) {
                case false === $v = realpath(filter_var($v)):
                case !is_file($v):
                case !is_readable($v):
                    continue; // or return false, throw new InvalidArgumentException

            }
            $data = file_get_contents($v);
            $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
            $k = str_replace($disallow, "_", $k);
            $v = str_replace($disallow, "_", $v);
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
                "Content-Type: application/octet-stream",
                "",
                $data,
            ));
        }
        // generate safe boundary
        do {
            $boundary = "---------------------" . md5(mt_rand() . microtime());
        } while (preg_grep("/{$boundary}/", $body));
        // add boundary for each parameters
        array_walk($body, function (&$part) use ($boundary) {
            $part = "--{$boundary}\r\n{$part}";
        });
        // add final boundary
        $body[] = "--{$boundary}--";
        $body[] = "";
        // set options
        return @curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\r\n", $body) ,
            CURLOPT_HTTPHEADER => array_merge(array(
                "Expect: 100-continue",
                "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type

            ) , $header) ,
        ));
    }


}