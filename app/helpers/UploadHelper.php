<?php

/**
 * Class UploadHelper
 */
class UploadHelper extends BaseHelper
{
    /**
     * Init the Folder
     */
    public function __construct($filepath=null, $organize = false)
    {
        parent::__construct();
        $this->url($filepath, $organize);
    }


    /**
     * Upload a file
     * @return mixed
     * @throws Exception
     */
    public function upload($profilPicture = false)
    {
        $this->profilPicture = $profilPicture;
        //Upload the file in the repository
        if (!$files = $this->web->receive(function ($file) 
        {
            $this->checkSize($file['type'], $file['size'], $this->profilPicture);    
        }, true, function ($file) {
            $tab = explode('.', $file);
            $extension = $tab[count($tab) - 1];
            $this->file = uniqid() . '.' . $extension;
            return $this->file;
        })
        ) 
        {
            throw new Exception('Error during upload');
        }

        // Resize if needed, and return cleaned array
        $new_array = [];
        foreach ($files as $key => $file)
        {
            if($file)
            {
                $new_array[] = $key;
                if($this->profilPicture)
                    $this->resize($key);
            }
        }

        return $new_array;

    }


    /**
     * Resize and crop image
     * @param $filepath
     * @return bool
     */
    private function resize($filepath)
    {
        $height = $this->f3->get('AVATAR_SIZE_HEIGHT');
        $width = $this->f3->get('AVATAR_SIZE_WIDTH');
        $img = new \Image($filepath, true);
        $img->resize($width, $height, true);
        $img->save();

        if (file_put_contents($filepath, $img->dump())) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param $fileType
     * @param $size
     * @return bool
     */
    private function checkSize($fileType, $size, $profilPicture)
    {
        $type = stristr($fileType, '/', true);
        if($size <= ($this->f3->get('MAX_FILE_SIZE') * 1024 * 1024))
        {
            if($type === 'image')
                return true;
            else if(!$profilPicture && $fileType ==='application/pdf')
                return true;
            return false;
        }
        return false;
    }

    /**
     * Change the upload directory
     * Set the global variable UPLOADS
     * @param string $filepath
     */
    private function url($filepath, $organize)
    {
        if(empty($filepath))
            $filepath = 'webroot/uploads/';

        if($organize && $this->f3->get('ORGANIZE_UPLOAD') === true)
        {
            $years = date("Y");
            $month = date("m");
            $day = date("d");
            $this->f3->set('UPLOADS', $filepath . $years . '/' . $month . '/' . $day . '/');
        }
        else 
            $this->f3->set('UPLOADS', $filepath);
    }
}
