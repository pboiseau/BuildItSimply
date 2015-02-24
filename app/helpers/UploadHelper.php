<?php

/**
 * Class UploadHelper
 */
class UploadHelper extends BaseHelper
{
    /**
     * Init the Folder
     */
    public function __construct($filepath=null)
    {
        parent::__construct();
        $this->url($filepath);
    }


    /**
     * Upload a file
     * @return mixed
     * @throws Exception
     */
    public function upload()
    {
        //Upload the file in the repository
        if (!$files = $this->web->receive(function ($file) {
            $this->checkSize($file['type'], $file['size']);
        }, true, function ($file) {
            $tab = explode('.', $file);
            $extension = $tab[count($tab) - 1];
            $this->file = uniqid() . '.' . $extension;
            return $this->file;
        })
        ) {
            throw new Exception('Error during upload');
        }

        $this->resize($this->f3->get('UPLOADS') . $this->file);
        return $this->file;

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
    private function checkSize($fileType, $size)
    {
        $type = stristr($fileType, '/', true);
        if ($type === 'image' && $size <= ($this->f3->get('MAX_FILE_SIZE') * 1024 * 1024)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Change the upload directory
     */
    private function url($filepath)
    {
        if(!empty($filepath))
            $this->f3->set('UPLOADS', $filepath);
        else if ($this->f3->get('ORGANIZE_UPLOAD') === true) {
            $years = date("Y");
            $month = date("m");
            $day = date("d");
            $this->f3->set('UPLOADS', 'webroot/uploads/' . $years . '/' . $month . '/' . $day . '/');
        } else {
            $this->f3->set('UPLOADS', 'webroot/uploads/');
        }
    }
}
