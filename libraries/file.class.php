<?php

    /**
     * File (class)
     *
     * @version 2.0
     * @author Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @licence CC BY 4.0 <http://creativecommons.org/licenses/by/4.0/>
     *
     * @require cURL class (v2.1)
     * @require get_mime_type() function
     * @require checkdir() function
     * @require root() function
     * @require UploadErrorException
     * @require ZipOpenException
    **/

    class File {
        
        private $origin;
        private $path;
        private $filename;
        private $extension;
        private $name;
        private $mime;
		private $size;
        
        
        /**
         * Get file
         *
         * @param mixed     $file
         * @param bool      $define
        **/
        public function __construct($file, $local = false) {
            try {
                if(is_array($file)): // Upload file
                    $this->_upload($file);

                elseif(is_string($file)): // Download file
                    if($local):
                        $this->_local($file);
                
                    else:
                        if(!filter_var($file, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
                            throw new InvalidArgumentException("Invalid file URL ($file)");

                        $this->_download($file);
                    endif;
                
                else: // Invalid file
                    throw new InvalidArgumentException('Invalid argument $file');

                endif;
            } catch(Exception $e) {
                throw $e;  
            }
            
        }
        
        /**
         * Download file
         *
         * @param string    $url
        **/
        private function _download($url) {
            try {
                
                $file = new cURL();
                $file->exec($url);
                
                checkdir(root(PATH_TMP_FILES));
                
                $this->origin    = 'download';
                $this->path      = root(PATH_TMP_FILES.'/'.uniqid().'.pdf');
                $this->filename  = basename($url);
                $this->extension = pathinfo($url, PATHINFO_EXTENSION);
                $this->name      = basename($url, '.'.$this->extension);
                $this->mime      = $file->getinfo(CURLINFO_CONTENT_TYPE);
                $this->size      = $file->getinfo(CURLINFO_SIZE_DOWNLOAD);                
                                
                // Copy file to tmp
                $resource = fopen($this->path, 'wb');
                fwrite($resource, $file->content());
                fclose($resource);

                // Check file operations
                @chmod($this->path, 0600); // Protect tmp file from any access
                $this->mime = get_mime_type($this->path);
                $this->size = filesize($this->path);
                
            } catch(Exception $e) {
                
                throw new $e;
                
            }
        }
        
        
        /**
         * Upload file
         *
         * @param array  $file
        **/
        private function _upload($file) {            
            if(!@is_uploaded_file($file['tmp_name']))
                throw new UploadErrorException($file);
            
            $this->origin    = 'upload';
            $this->path      = $file['tmp_name'];
            $this->filename  = $file['name'];
            $this->extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $this->name      = basename($file['name'], '.'.$this->extension);
            
            $this->mime      = $file['type'];
            $this->size      = $file['size'];
            
            if(is_readable($file['tmp_name'])): // Better safe than sorry
                $this->mime      = get_mime_type($file['tmp_name']);
                $this->size      = filesize($file['tmp_name']);
            endif;
        }        
        
        /**
         * Define an existing file
         *
         * @param string    $file
        **/
        private function _local($file) {
            if(!is_readable($file))
                throw new Exception("Not readable file ({$file})");
                
            $this->origin       = 'local';
            $this->path         = $file;
            $this->filename     = basename($file);
            $this->extension    = pathinfo($file, PATHINFO_EXTENSION);
            $this->name         = basename($file, '.'.$this->extension);
            $this->mime         = get_mime_type($file);
            $this->size         = filesize($file);
        }
        
        
        /**
         * Get content
        **/
        public function content() {            
            return @file_get_contents($this->path);
        }
        
        /**
         * Move or copy file
         *
         * @param string    $destination
        **/
        public function move($destination, $copy = false) {
            checkdir(dirname($destination), true); // Generate folders
            $destination = $this->_replace($destination); // Replace with file values
            
            if($this->origin == 'upload')
                $success = @move_uploaded_file($this->path, $destination);
                
            elseif($copy)
                $success = @copy($this->path, $destination);
            
            else
                $success = @rename($this->path, $destination);
             
            if(!$success)
                throw new Exception("Unable to move/copy file {$this->path} to {$destination}");
            
            if($copy):
                try {
                    return new File($destination, true);
                } catch(Exception $e) {
                    throw new $e;
                }
            endif;
            
            $this->path = $destination;
            return;
        }
        
        /**
         * Copy file
         *
         * @param string    $destination
        **/
        public function copy($destination) {
             return $this->move($destination, true);  
        }
        
        /**
         * Remove file
        **/
        public function remove() {
            if(!@unlink($this->path))
               throw new Exception("Unable to remove file ({$this->path})", 504); 
        }
        
        
        /**
         * Archive file
         *
         * @param string    $destination
         * @param string    $filename
         * @param mixed     $flags
        **/
        public function zip($destination = null, $filename = null, $flags = ZIPARCHIVE::CREATE) {
            
            if(empty($destination))
                $destination = dirname($this->path) . "/$this->filename.zip";
            
            if(empty($filename))
                $filename = $this->filename;
            
            checkdir(dirname($destination), true); // Generate folders
            $destination = $this->_replace($destination); // Replace with file values            
            $filename = $this->_replace($filename);
            
            if(is_bool($flags)):
                if($flags)
                    $flags = ZIPARCHIVE::OVERWRITE;
                else
                    $flags = ZIPARCHIVE::CREATE;  
            endif;
            
            $archive = new ZipArchive();
            
            $code = $archive->open($destination, $flags);
            
            if($code !== true)
                throw new ZipOpenException($code);
            
			if(!$archive->addFile($this->path, "/$filename"))
                throw new Exception("Unable to add {$filename} to {$destination}");
                
			$archive->close();
            
            @chmod($destination, 0644);
            return $destination;
        }
        
        
        /**
         * Get file info
         *
         * @param string    $information
        **/
        public function __get($information) {
            if(!isset($this->$information))
                throw new Exception("Invalid request information name ({$information})");
            
            return $this->$information;
        }
        
        /**
         * Replace file values in path
         * 
         * @param string    $path
        **/
        private function _replace($path) {
            $path = str_replace(':filename', $this->filename, $path);
            $path = str_replace(':extension', $this->extension, $path);
            $path = str_replace(':name', $this->name, $path);
            
            return $path;
        }
        
    }

    
    /**
     * Upload error exception
    **/
    class UploadErrorException extends Exception {
        
        protected $data;
        
        public function __construct($file, $previous = null) {
            $this->data = $file;
            
            switch($file['error']) {
                
                case UPLOAD_ERR_OK: 
                    $message = 'No error detected on upload';
                    break;
                
                case UPLOAD_ERR_INI_SIZE: 
                    $message = 'Uploaded file size exceeds upload size directive';
                    break;
                
                case UPLOAD_ERR_FORM_SIZE: 
                    $message = 'Uploaded file size exceeds form size directive';
                    break;
                
                case UPLOAD_ERR_PARTIAL: 
                    $message = 'Incomplete file upload';
                    break;
                
                case UPLOAD_ERR_NO_FILE: 
                    $message = 'No file have been uploaded';
                    break;
                
                case UPLOAD_ERR_NO_TMP_DIR: 
                    $message = 'Upload temporary directory unavailable';
                    break;
                
                case UPLOAD_ERR_CANT_WRITE: 
                    $message = 'Uploaded file writing failed';
                    break;
                
                case UPLOAD_ERR_EXTENSION: 
                    $message = 'Upload stopped by upload.write';
                    break;
                
                default:
                    $message = 'Unknow upload error';
            }    
            
            parent::__construct($message, $code, $previous);   
        }
        
        public function getInfo($param) {
            if(!isset($data[$param]))
                trigger_error(__METHOD__." Parameter $param does not exists", E_USER_ERROR);
                
            return $data[$param];
        }
        
    }

    /**
     * Zip error exception
    **/
    class ZipOpenException extends Exception {        
        
        public function __construct($code, $previous = null) {
            switch($code) {
                
                case ZipArchive::ER_EXISTS: 
                    $message = 'Zip file already exists';
                    break;
                
                case ZipArchive::ER_INCONS: 
                    $message = 'Zip archive inconsistent';
                    break;
                
                case ZipArchive::ER_INVAL: 
                    $message = 'Invalid zip argument';
                    break;
                
                case ZipArchive::ER_MEMORY: 
                    $message = 'Malloc failure for zip';
                    break;
                
                case ZipArchive::ER_NOENT: 
                    $message = 'No such zip file';
                    break;
                
                case ZipArchive::ER_NOZIP: 
                    $message = 'Not a zip archive';
                    break;
                
                case ZipArchive::ER_OPEN: 
                    $message = 'Can\'t open file';
                    break;
                
                case ZipArchive::ER_READ: 
                    $message = 'Read error';
                    break;
                
                case ZipArchive::ER_SEEK: 
                    $message = 'Seek error';
                    break;
                
                default:
                    $message = 'Unknow zip error';
            }            
            
            parent::__construct($message, $code, $previous);   
        }
        
    }


?>