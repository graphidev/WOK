<?php

    /**
     * File (class)
     *
     * @version 2.3
     * @author Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @licence CC BY 4.0 <http://creativecommons.org/licenses/by/4.0/>
     *
     * @require cURL class (v2.1)
     * @require get_mime_type() function
     * @require makedir() function
     * @required ExtendedExceptions
     * @require UploadErrorException
     * @require ZipOpenException
     *
     * @package Libraries
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
                    if(is_bool($local) && $local):
                        $this->_local($file);
                
                    else:
                        if(!filter_var($file, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
                            throw new ExtendedInvalidArgumentException("Invalid file URL ($file)", array(
                                'argument' => 'url',
                                'url' => $file
                            ));

                        $this->_download($file, $local);
                    endif;
                
                else: // Invalid file
                    throw new ExtendedLogicException('Invalid argument');

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
        private function _download($url, $directory) {
            try {
        
                if(!makedir($directory))
                    throw new ExtendedException("Unable to create temporary directory ({$directory} : check permissions)");
                
                if(!is_writable($directory))
                    throw new ExtendedException("Temporary directory isn't writable ({$directory} : check permissions)");
                    
                $file = new cURL();
                $file->exec($url);
                
                $this->origin    = 'download';
                $this->path      = "$directory/".uniqid();
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
                
                throw $e;
                
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
                throw new ExtendedException("Not readable file ({$file})", array('file' => $file));
                
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
         * Warning : If the destination file already exists, it will be overwritten
         *
         * @param string    $destination
        **/
        public function move($destination, $copy = false) {
            $destination = $this->_replace($destination); // Replace with file values
            if(!makedir(dirname($destination))) // Generate folders
                throw new ExtendedException("Unable to create directory {$destination} (check permissions)");
            
            if(!is_writable(dirname($destination)))
                throw new ExtendedException("Destination directory isn't writable (".dirname($destination)." : check permissions)");
            
            if($this->origin == 'upload')
                $success = @move_uploaded_file($this->path, $destination);
                
            elseif($copy)
                $success = @copy($this->path, $destination);
            
            else
                $success = @rename($this->path, $destination);
             
            if(!$success)
                throw new ExtendedException("Unable to move/copy file {$this->path} to {$destination}");
            
            if($copy):
                try {
                    return new File($destination, true);
                } catch(Exception $e) {
                    throw $e;
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
               throw new ExtendedException("Unable to remove file ({$this->path})", 504); 
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
                    
            $destination = $this->_replace($destination); // Replace with file values            
            $filename = $this->_replace($filename);
            
            if(!makedir(dirname($destination))) // Generate folders
                throw new ExtendedException("Unable to create directory {$destination} (check permissions)");
            
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
                throw new ExtendedException("Unable to add {$filename} to {$destination}");
                
			$archive->close();
            
            @chmod($destination, 0644);
            return new File($destination, true);
        }
        
        
        /**
         * Get file info
         *
         * @param string    $information
        **/
        public function __get($information) {
            if(!isset($this->$information))
                throw new ExtendedLogicException("Invalid request information name ({$information})");
            
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
	 * @package Libraries
    **/
    class UploadErrorException extends ExtendedException {
                
        public function __construct($file, $previous = null) {
            
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
            
            parent::__construct($message, $file, $code, $previous);   
        }
        
    }

    /**
     * Zip error exception
	 * @package Libraries
    **/
    class ZipOpenException extends ExtendedException {        
        
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
            
            parent::__construct($message, array(), $code, $previous);   
        }
        
    }


?>