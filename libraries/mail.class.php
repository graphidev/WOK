<?php
	
	/**
     * Mail (class)
     *
     * @version 2.5
     * @author Sébastien ALEXANDRE <sebastien@graphidev.fr>
     * @licence CC BY 4.0 <http://creativecommons.org/licenses/by/4.0/>
     *
     * @require native mail() function
     * @require get_mime_type() function
     * @require ExtendedExceptions
    **/

	class Mail {
        
        const BREAKLINE          = "\n"; // New line break
        const MAX_LINES_LENGTH   = 76; // Max content lines length
        const FORMAT_TEXT        = 'text/plain';
        const FORMAT_HTML        = 'text/html';
        
        private $format          = 'text/plain'; // Default format
	 	private $To              = array(); // Send to
	 	private $Cc              = array(); // Carbon copy
	 	private $Bcc             = array(); // Blind carbon copy
	 	private $From            = null; // Sender informations
        private $reply           = null; // Reply address
	 	private $object          = null; // Message object
	 	private $content         = null; // Message content
	 	private $attachments     = null; // Attachments
        private $headers         = array();
        
        
        /**
         * Check email
         * @param string    $email
        **/
        private function _checkEmail($email, $field) {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)):            
                $e = new ExtendedInvalidArgumentException("Invalid e-mail", array(
                    'argument'  => $field,
                    'value'     => $email
                ));
                $e->setCallFromTrace(2);
                throw $e;
            endif;
        }
        
        
	 	/**
         * Generate a new mail
         * @param string   $object
	 	**/
	 	public function __construct($headers = array()) {
            $this->headers(array_merge(array(
                'X-Mailer' =>  'PHP/'.PHP_VERSION
            ), $headers));
	 	}
        
        
        /**
         * (Re)Define custom email headers
         * @param array $headers
        **/
        public function headers($headers) {
            $this->headers = array_merge($this->headers, $headers);
        }
        
	 	
	 	/**
	 	 * Define email object
         * @param string   $value
	 	**/	 	
	 	public function object($value) {
	 		$this->object =  $value;
	 	}
	 	
	 	
	 	/**
         * Define addressee's informations
         * @param string    $mail
         * @parem string    $name
	 	**/
	 	public function to($email, $name = null) {
            $this->_checkEmail($email, 'To');
            $this->To[] = (!empty($name) ? "\"$name\" <$email>" : $email);
	 	}
	 	

	 	/**
         * Define carbon copies contacts
         * @param string    $mail
         * @param string    $name
         * @param bool      $bind
	 	**/
	 	public function Cc($email, $name = null, $bind = false) {
            $this->_checkEmail($email, 'Cc');

            if($bind)
                $this->Bcc[] = (!empty($name) ? "\"$name\" <$email>" : $email);
            else
                $this->Cc[] = (!empty($name) ? "\"$name\" <$email>" : $email);
	 	}
	 	
	 	
	 	/**
	 	 * Define sender informations
	 	 *	@param string     $email
	 	 *	@param string     $name
	 	**/
	 	public function from($email, $name = null) {
            $this->_checkEmail($email, 'From');
            $this->From = (!empty($name) ? "\"$name\" <$email>" : $email);
            $this->reply = $email;
	 	}
	 	
        
        /**
         * Define message
         * @param string $object
         * @param string $content
         * @param string $format
        **/
	 	public function message($object = null, $content = null, $format = self::FORMAT_TEXT) {
            $this->object = $object;
            $this->content($content, $format);
        }
        
        
	 	/**
         * Define message content
         * @param string    $message
         * @param string    $format
	 	**/
	 	public function content($message, $format = self::FORMAT_TEXT) {
		 	$this->content = $message;
            $this->format = $format;
	 	}
        
	 		 	
	 	/**
	 	 * Define attachement file
	 	 * @param string     $path
	 	 * @param string     $name
	 	**/	 	
	 	public function attachment($name, $file) {
            if(!is_readable($file)):
                $e = new ExtendedInvalidArgumentException('Unreadable file', array(
                    'argument'  => 'file', 
                    'value'     => $file
                ));
                $e->setCallFromTrace();
                throw $e;
            endif;
            
	 		$this->attachments[] = array(
	 			'name' => $name,
	 			'type' => get_mime_type($file),
	 			'content' => base64_encode(file_get_contents($file))
	 		);
	 	}
        
	 		 	
	 	/**
	 	 * Try to send mail
	 	 * @require function construct() / object()
	 	 * @require  $To, $From, $reply
	 	**/
	 	public function send() {            
            if(empty($this->To)):
                $e = new ExtendedLogicException("Addressee undefined : define it before sending message");
                $e->setCallFromTrace();
                throw $e;
            endif;
            
            if(empty($this->From))
                $this->from($_SERVER['SERVER_ADMIN']);
                        	 	 	 		
	 		// Required informations	
	 		$boundary = sha1(uniqid(microtime(), true)) . self::BREAKLINE; // Boundary
	 		
	 		// Mail headers
	 		$headers = 'From: '.$this->From . self::BREAKLINE; // sender
	 		$headers .= 'Reply-To: '.$this->reply . self::BREAKLINE; // Reply address
            $headers .= 'Return-Path: '.$this->reply . self::BREAKLINE;
	 		if(!empty($this->Cc)): $headers .= 'Cc: '.implode(', ', $this->Cc) . self::BREAKLINE; endif; // Carbon copies
	 		if(!empty($this->Bcc)): $headers .= 'Bcc: '.implode(', ', $this->Bcc) . self::BREAKLINE; endif; // Blind carbon copies
            
            $headers .= 'Date: '.date('r') . self::BREAKLINE; // Sending date
	 		$headers .= 'MIME-Version: 1.0' . self::BREAKLINE; // MIME version
            
            // Custom headers
            foreach($this->headers as $name => $value)
	 		    $headers .= "$name: $value" . self::BREAKLINE; // Sending software (not adviced: consired as SPAM)
            
            
	 		$headers .= "Content-Type: multipart/mixed; boundary=$boundary"; // Content-Type
            
               
            // Content headers
            $message = "--$boundary";
            $message .= "Content-type: ".$this->format."; charset=\"utf-8\"" . self::BREAKLINE;
            $message .= "Content-Transfer-Encoding: 8bit" . self::BREAKLINE . self::BREAKLINE;
            
            // Text content
            $message .= wordwrap($this->content, self::MAX_LINES_LENGTH, self::BREAKLINE, true) . self::BREAKLINE . self::BREAKLINE;            
            
	 		// Adding message attachment
	 		if(!empty($this->attachments)):
	 			foreach($this->attachments as $key => $attachment) {
                    
                    // Attachement headers
		 			$message .= "--$boundary";
		 			$message .= "Content-type:".$attachment['type'].";name=\"".$attachment['name'].'"' . self::BREAKLINE; // Type & name
		 			$message .= "Content-Transfer-Encoding: base64" . self::BREAKLINE; // Encoding method
		 			$message .= "Content-Disposition:attachment" . self::BREAKLINE . self::BREAKLINE;
                    
                    // Attachment file
		 			$message .= chunk_split($attachment['content']) . self::BREAKLINE . self::BREAKLINE; // File content
		 		}
	 		endif;
	 		
	 		$message .= "--$boundary" . self::BREAKLINE; // End message
	 		
	  		// Try to send mail and return result
	 		if(!@mail(implode(', ', $this->To), $this->object, $message, $headers)):
                $e = new ExtendedException('Unable to send e-mail');
                $e->setCallFromTrace();
                throw $e;
            endif;
	 	}

	 			
	 }
 
 ?>