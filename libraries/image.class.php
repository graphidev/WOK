<?php
    
    /**
     * Image (class)
     *
     * @version 1.0
     * @author Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @licence CC BY 4.0 <http://creativecommons.org/licenses/by/4.0/>
     *
     * @require GD2 library
     * @require EXIF class
     * @require get_mime_type() function
     * @require makedir() function
     * @require ExtendedExceptions
     * @require PHP 5.5+ for WEBP format support
     *
     * @package Libraries
    **/
    
    if(!extension_loaded('gd')) // Generate a fatal error on not loaded GD library
        trigger_error('GD2 library must be loaded in order to use Image class', E_USER_ERROR);


    class Image {
        
        private $path;
        private $name;
        private $filename;
        private $extension;
        private $mime;
        private $size; 
        private $width;
        private $height;
        private $image;
                
        private $authorized = array('image/jpeg', 'image/png', 'image/x-png', 'image/gif', 'image/webp', 'image/bmp', 'image/x-ms-bmp', 'image/tiff');
        
        const RESIZE_FORCE      = 'f'; // Force defined dimensions resize
        const RESIZE_CROP       = 'c'; // Crop extra resized image
        const RESIZE_WITHIN     = 'w'; // Rezine image keeping ratio
        
        const SAVE_RELOAD       = 'l'; // Load saved imaged
        const SAVE_CONTINUE     = 'c'; // Keep working with the original
        const SAVE_CLEAR        = 'd'; // Keep working with the original
        
        const ROTATE_LANDSCAPE  = 'l'; 
        const ROTATE_PORTRAIT   = 'p';

        const FLIP_HORIZONTAL   = IMG_FLIP_HORIZONTAL;
        const FLIP_VERTICAL     = IMG_FLIP_VERTICAL;
        const FLIP_BOTH         = IMG_FLIP_BOTH;
        
        
        /**
         * Initialize image settings
         * @param string    $path
         * @return object
        **/
        public function __construct($path) {    
            
            if(!file_exists($path)) // Check file existence
                throw new ExtendedInvalidArgumentException('Invalid image path', array('path'=> $path));
            
             if(!is_readable($path)) // Check file readable
                throw new ExtendedLogicException('Image not readable', array('path'=> $path, 'readable' => false));
            
            $mime = get_mime_type($path);
            
            if(!in_array($mime, $this->authorized)) // && is_image
                throw new ExtendedInvalidArgumentException('Invalid file type', array('mime'=> $mime));
            
            // Get image settings
            $this->path = $path;
            $this->mime = $mime;
            list($this->width, $this->height) = getimagesize($this->path);
            $this->filename     = basename($this->path);
            $this->extension    = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
            $this->name         = basename($this->path, '.'.$this->extension);
            $this->size         = filesize($this->path);
            
            switch($this->extension) {
                case 'jpg': case 'jpeg': $this->image = @imagecreatefromjpeg($this->path); break;
                case 'png': $this->image = @imagecreatefrompng($this->path); break;
                case 'gif': $this->image = @imagecreatefromgif($this->path); break;
                case 'bmp': $this->image = @imagecreatefromwbmp($this->path); break;
                case 'webp': $this->image = @imagecreatefromwebp($this->path); break;
                case 'xbm': $this->image = @imagecreatefromxbm($this->path); break;
                default:
                    $this->image = @imagecreate($width, $height);
            }
                        
            if(!is_resource($this->image))
                throw new RuntimeException('Error while processing');
        }
        
        
        /**
         * Resize image
         * @param integer    $width
         * @param integer    $height
         * @param string    $option
        **/
        public function resize($width, $height, $option = self::RESIZE_FORCE) {
                
            // Default source image settings
            $top = 0; $left = 0;
            $src_width = $this->width;
            $src_height = $this->height;
            
            // Resize but keep ratio
            if($option == self::RESIZE_WITHIN):
            
                $width_ratio = $this->width / $width;
                $height_ratio = $this->height / $height;
                
                if($height_ratio > $width_ratio):
                    $width = ceil($this->width / $height_ratio);
            
                elseif($height_ratio < $width_ratio):
                    $height = ceil($this->height / $width_ratio);
                endif;
                        
            // Resize and crop extra image
            elseif($option == self::RESIZE_CROP):
            
                $width_ratio = $this->width / $width;
                $height_ratio = $this->height / $height;
                
                if($width_ratio > $height_ratio) // Width > height
                    $src_width = ceil($width * $height_ratio);
            
                elseif($height_ratio > $width_ratio) // Height > width
                    $src_height = ceil($height * $width_ratio);
                
                $left = ceil($this->width/2 - $src_width/2);
                $top = ceil($this->height/2 - $src_height/2);
                        
            endif;
                
            $resize = imagecreatetruecolor($width, $height);
                    
            if($this->extension == 'png'):
                imagealphablending($resize, false);
                imagesavealpha($resize, true);
            endif;
  
            imagecopyresampled($resize, $this->image, 0, 0, $left, $top, $width, $height, $src_width, $src_height);
            
            // Redefine image settings after resize
            $this->image = $resize; // Redefine resource      
            $this->width = $width; // Redefine resource      
            $this->height = $height; // Redefine resource      
        }
        
        /**
         * Rotate image
         * @param float     $angle
         * @param integer   $background
        **/ 
        public function rotate($angle, $background = null) {
            
            if($background === null) // Define transparency background color
                $background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
            
            imagefill($this->image, 0, 0, $background);

            // Rotate image
            $this->image = imagerotate($this->image, $angle, $background, 0);
                        
            if(!$this->image)
                throw new RuntimeException('Image rotatio failure');
            
            // Keep transparency as possible
            imagesavealpha($this->image, true);
            imagealphablending($this->image, false);

        }      
        
        /**
         * Mirror turn image
         * @param const     $direction
        **/
        function flip($direction) {            
            imageflip($this->image, $direction);
        }
        
        
        /**
         * Save image
         * @param string    $path
        **/ 
        public function save($path = null, $option = self::SAVE_CLEAR) {
            
            if(!empty($path))
                makedir(dirname($path), 0755);
            
            else // Default : save as original
                $path = $this->path;
            
            $path = $this->_replace($path);            
            $this->extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            
            switch($this->extension) {
                case 'jpg': case 'jpeg': imagejpeg($this->image, $path); break;
                case 'png' : imagepng($this->image, $path); break;
                case 'gif': imagegif($this->image, $path); break;
                case 'bmp': imagewbmp($this->image, $path); break;
                case 'webp': imagewebp($this->image, $path); break;
                case 'xbm': imagexbm($this->image, $path); break;
                default:
                    imagegd($this->image, $path);                    
            }
            
            if($option == self::SAVE_RELOAD) // Redefine source image
                $this->__construct($path);
            
            elseif($option == self::SAVE_CLEAR) // Stop working
                $this->__destruct();
                
            // Continue with the original otherwise
          
        }
        
        
        /**
         * Get image information
        **/
        public function get($information) {
            if(!isset($this->$information))
                throw new ExtendedLogicException('Undefined information', array('information'=>$information));
            
            return $this->$information;
        }
        
        
        /**
         * Destroy image resource
         * Free used memory
        **/
        public function __destruct() {
            if(is_resource($this->image))
                imagedestroy($this->image);
            
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

    
    if(!function_exists('imageflip')):

        if(!defined('IMG_FLIP_HORIZONTAL'))
            define('IMG_FLIP_HORIZONTAL', 'horizontal');

        if(!defined('IMG_FLIP_VERTICAL'))
            define('IMG_FLIP_VERTICAL', 'vertical');

        if(!defined('IMG_FLIP_BOTH'))
            define('IMG_FLIP_BOTH', 'both'); 
        /**
         * Define PHP < 5.5 compatility imageflip() function and constants.
         *
         * Warning : The native PHP 5.5 function should be used instead.
        **/ 
        function imageflip (&$image, $mode) {
            $width = imagesx ($image);
            $height = imagesy ($image);

            $x = 0; $y = 0;
            $w = $width;
            $h = $height;

            switch ($mode){

                case IMG_FLIP_VERTICAL:
                    $y = $height -1;
                    $h = -$height;
                    break;

                case IMG_FLIP_HORIZONTAL:
                    $x = $width -1;
                    $w = -$width;
                    break;

                case IMG_FLIP_BOTH:
                    $x = $width -1;
                    $y = $height -1;
                    $w = -$width;
                    $h = -$height;
                    break;

                default:
                    trigger_error('Invalid imageflip $mode parameter', E_USER_ERROR);

            }

            $tmp = imagecreatetruecolor ($width, $height);
            imagecopyresampled($tmp, $image, 0, 0, $x, $y , $width, $height, $w, $h );
            $image = $tmp;
        }  

    endif;

?>