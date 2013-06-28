<?php


	class file {
		private $mime;
		private $path;
		private $result = false;
		private $filename;
		private $extension;
		private $size;
		private $width;
		private $height;
		private $type;
		private $error; 
		
		// Formats autorisés
		private static $formats = array(
			'image' => array('png', 'x-png', 'jpg', 'jpeg', 'pjpeg', 'gif'),
			'text' => array('txt','plain', 'htm', 'html', 'css', 'sql', 'phps', 'js', 'json', 'cpp'),
			'document' => array('rtf', 'odt','msword', 'doc', 'docx', 'pdf', 'xml', 'csv', 'ppt', 'pps',
			// New Microsoft Office documents
			'vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 
			'vnd.openxmlformats-officedocument.presentationml.template',
			'vnd.openxmlformats-officedocument.presentationml.slideshow', 
			'vnd.openxmlformats-officedocument.presentationml.presentation',
			'vnd.openxmlformats-officedocument.presentationml.slide',
			'vnd.openxmlformats-officedocument.wordprocessingml.document',
			'vnd.openxmlformats-officedocument.wordprocessingml.template',
			'vnd.ms-excel.addin.macroEnabled.12',
			'vnd.ms-excel.sheet.binary.macroEnabled.12'
			),
			'video' => array('video', 'mp4', 'ogv', 'webm', 'flv', 'mpeg', 'mpg', 'mov', 'quicktime'),
			'audio' => array('audio', 'mp3', 'ogg', 'flac', 'aac', 'mpeg', 'mpg'),
			'archive' => array('rar', 'zip', 'gzip', 'gz', 'tar', 'tgz', '7z', 'dmg', 'iso', 'pkg', 'x-zip-compressed', 'x-rar-compressed', 'x-gzip', 'x-gunzip', 'x-tar-gz', 'x-tar', 'octet-stream'),
			'other' => array('pkg', 'psd', 'svg', 'tiff', 'tga', 'bmp', 'wmv'),
		);
		
		public function __construct($file = null, $method = null, $protected = false) {
            if(!empty($file) && !empty($method))
                $this->$method($file, $protected);   
		}
		
		private function is_authorized($path, $is_external = false) {
			$this->extension = strtolower(substr(strrchr($this->filename, "."), 1));
			$mime = strtolower(preg_replace('#^(.+)/(.+)$#isU', '$2', $this->mime));
									
			if(in_array($this->extension, self::$formats['image']) && in_array($mime, self::$formats['image'])):
				$this->type = 'image';
				list($this->width, $this->height) = getimagesize(($is_external ? $path : root($path)));
				
			elseif(in_array($this->extension, self::$formats['text']) && in_array($mime, self::$formats['text'])):
				$this->type = 'text';
				
			elseif(in_array($this->extension, self::$formats['document']) && in_array($mime, self::$formats['document'])):
				$this->type = 'document';
				
			elseif(in_array($this->extension, self::$formats['video']) && in_array($mime, self::$formats['video'])):
				$this->type = 'video';
				
			elseif(in_array($this->extension, self::$formats['audio']) && in_array($mime, self::$formats['audio'])):
				$this->type = 'audio';
				
			elseif(in_array($this->extension, self::$formats['archive']) && in_array($mime, self::$formats['archive'])):
				$this->type = 'archive';
				
			elseif(in_array($this->extension, self::$formats['other']) && in_array($mime, self::$formats['other'])):
				$this->type = 'unknow';
				
			else:
				$archive = new ZipArchive();
				$archive->open($this->path.'.zip' , ZIPARCHIVE::CREATE);				
				$archive->addFile($this->path, $this->filename);
				$archive->close();
				unlink($this->path);
				$this->filename = str_replace($this->extension, 'zip', $this->filename);
				$this->extension = 'zip';
				$this->mime = get_mime_type($this->path.'.zip');
				$this->type = 'archive';
				$this->size = filesize($this->path.'.zip');
				$path .= '.zip';
			endif;
			@chmod($path, 0600);
			return $path;
		}
		
		public function upload($file, $protected = false) {
			$this->error = $file['error'];		
			if($file['size'] != 0 && is_uploaded_file($file['tmp_name']) && $file['error'] == 0 && $file):
				$this->mime = $file['type'];
				$this->size = $file['size'];
				$this->filename = $file['name'];
				$tmpfile = $file['tmp_name'];
				
				$this->path = PATH_TMP_FILES.'/'.uniqid();
				$this->result = move_uploaded_file($file['tmp_name'], $this->path);				
				$this->path = $this->is_authorized($this->path);
				if($protected): @chmod($this->path, 0600); endif;
			else:
				$this->result = false;
			endif;
            
            return $this->result;
		}
		
		public function download($link, $protected = false) {
			$headers = @get_headers($link, 1);
			if(!strstr($headers[0], '200 OK')):
				$this->result = false;
				
			else:
				$this->mime = $headers['Content-Type'];
				$this->size = $headers['Content-Length'];	
				$this->filename = str_replace(dirname($link).'/', '', $link);
					
				$tmpfile = $this->is_authorized($link, true);		
				$this->path = PATH_TMP_FILES.'/'.uniqid();
				if($protected): @chmod($this->path, 0600); endif;
                
				if($tmpfile == $link):
					$this->result = @copy($link, root($this->path));
				else:
					$this->result = @rename($tmpfile, root($this->path));
				endif;
            
			endif;
            
            return $this->result;
		}
		
		public function select($path, $protected = false) {
			if(preg_match('#^'.root().'(.+)$#', $path)):
				$this->path = $path;
			else:
				$this->path = root($path);
			endif;
            
            if(file_exists($this->path)):
			
                if(empty($this->filename) && empty($this->extension) && empty($this->mime) && empty($this->size)):
                    $this->filename = str_replace(dirname($this->path).'/', '', $this->path);
                    $this->extension = strtolower(substr(strrchr($this->filename, "."), 1));
                    $this->mime = get_mime_type($this->path);
                    $this->size = filesize($this->path);
                endif;
            
                $n_path = PATH_TMP_FILES.'/'.uniqid();
                copy($this->path, root($n_path));
                $this->path = $this->is_authorized($n_path);
                if($protected): @chmod($this->path, 0600); endif;
            
            else:
            
                $this->result = false;
            endif;
            
		}
				
		public function resize($width, $height, $destination, $margin = null) {
            if($this->type == 'image'):
            
                $path = root($this->path);
                list($this->width, $this->height) = getimagesize($path);
                
                $destination = str_replace('%ext%', $this->extension, $destination);
            
                if(!is_dir(dirname($destination))):
                    mkdir(dirname($destination), 0755, true);
                endif;
                
                if(!empty($margin) && $this->width > $width && $this->height > $height):
                    if(is_array($margin)):
                        list($left, $top) = $margin;
                        $src_width = $width + $left;
                        $src_height = $height + $top;
                    else:
                        $left = ceil($this->width/2 - $width/2);
                        $top = ceil($this->height/2 - $height/2);
                        $src_width = $this->width-2*$left;
                        $src_height = $this->height-2*$top;
                    endif;
                else:
                    $top = 0; $left = 0;
                    $src_width = $this->width;
                    $src_height = $this->height;
                endif;
            
                switch($this->extension) {
                    case 'png': $image = imagecreatefrompng($path); break;
                    case 'jpg': $image = imagecreatefromjpeg($path); break;
                    case 'jpeg': $image = imagecreatefromjpeg($path); break;
                    case 'gif': $image = imagecreatefromgif($path); break;
                    default:
                        $image = imagecreate($width, $height);
                }
                $this->result = $image;
                
                $resize = imagecreatetruecolor($width, $height);
                
                if($this->extension == 'png' || $this->extension == 'x-png'):
                    imagealphablending($resize,FALSE);
                    imagesavealpha($resize,TRUE);
                endif;
                
                $this->result = $resize;
  
               $this->result = imagecopyresampled($resize, $image, 0, 0, $left, $top, $width, $height, $src_width, $src_height);
            
                switch($this->extension) {
                    case 'png' : imagepng($resize, $destination); break;
                    case 'jpg': imagejpeg($resize, $destination); break;
                    case 'jpeg': imagejpeg($resize, $destination); break;
                    case 'gif': imagegif( $resize, $destination); break;
                }
            
            else:
                $this->result = false;
            endif;
			
        }
        
        public function archive($destination, $filename = null, $protected = false) {
            
            if(empty($filename))
                $filename = $this->filename;

            if(!is_dir(dirname(root($destination)))):
				mkdir(dirname(root($destination)), 0755, true);
			endif;
            
            $archive = new ZipArchive();
            $archive->open(root($destination) , ZIPARCHIVE::CREATE);
			$archive->addFile(root($this->path), "/$filename");
			$archive->close();
            
            if($protected): @chmod(root($destination), 0600); endif;
                        
        }
		
		public function move($destination, $protected = false) {
			$destination = str_replace('%ext%', $this->extension, $destination);
            
            if(!is_dir(dirname(root($destination)))):
				mkdir(dirname(root($destination)), 0755, true);
			endif;
			
			$this->result =  @copy(root($this->path), root($destination));
			if($protected): @chmod($destination, 0600); endif;
            
            return $this->result;
		}
        
        public function info($parameter) {
			if(isset($this->$parameter)):
				return $this->$parameter;
			else:
				return false;
			endif;
		}
        
        public function reset() {
            unlink(root($this->path));
            $this->mime = null;
            $this->path = null;
            $this->result = false;
            $this->filename = null;
            $this->extension = null;
            $this->size = null;
            $this->width = null;
            $this->height = null;
            $this->type = null;
            $this->error = null;
        }
		
		
			
		/**
		* @method success;
		* @return bool $result;
		**/
		public function success() {
			return $this->result;
		}
		
		
		public function __get($name) {
			return $this->$name;
		}
	}

?>