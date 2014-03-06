<?php
	
	/**
     * Mail (class)
     *
	 *	@version 2.0
     *	@author Sébastien ALEXANDRE 
     *
     *	@require function mail() allowed
	 *	@require function get_mime_type()
    **/
	
	class Mail {
        
        const BREAKLINE          = "\n"; // New line break
        const MAX_LINES_LENGTH   = 76; // Max content lines length
        const FORMAT_TEXT        = 'text/plain';
        const FORMAT_HTML        = 'text/html';
        
        private $format          = 'text/plain';
	 	private $To              = array(); // Send to
	 	private $Cc              = array(); // Carbon copy
	 	private $Bcc             = array(); // Blind carbon copy
	 	private $sender          = array(); // Sender informations
        private $reply           = null; // Reply address
	 	private $object          = null; // Message object
	 	private $content         = null; // Message content
	 	private $attachments     = null; // Attachments
        
        /**
         * Check email
         * @param string    $email
        **/
        private function __checkEmail($email) {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                throw new InvalidArgumentException("Mail : Invalid e-mail '$email'");   
        }
        
	 	/**
         * Generate a new mail
         * @param string   $object
	 	**/
	 	public function __construct($object = null) {
	 		$this->object($object);
	 	}
	 	
	 	/**
	 	 * Define mail object
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
            $this->__checkEmail($email);
            $this->To[] = (!empty($name) ? "\"$name\" <$email>" : $email);
	 	}
	 	

	 	/**
         * Define carbon copies contacts
         * @param string    $mail
         * @param string    $name
         * @param bool      $bind
	 	**/
	 	public function Cc($email, $name = null, $bind = false) {
            $this->__checkEmail($email);

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
            $this->sender = (!empty($name) ? "\"$name\" <$email>" : $email);
            $this->reply = $email;
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
	 	public function attachment($name, $path) {
            
            if(!file_exists($path))
                throw new InvalidArgumentException('Mail : Attachment not found');
            
	 		$this->attachments[] = array(
	 			'name' => $name,
	 			'type' => \Compatibility\get_mime_type($path),
	 			'content' => base64_encode(file_get_contents($path))
	 		);
	 	}
	 		 	
	 	/**
	 	 * Try to send mail
	 	 * @require function construct() / object()
	 	 * @require  $To, $sender, $reply
	 	**/
	 	public function send() {
            
            if(empty($this->To) || empty($this->sender) || empty($this->reply))
                throw new Exception("Mail : Addressee or sender not defined");
            	 	 	 		
	 		// Required informations	
	 		$boundary = sha1(uniqid(microtime(), true)) . self::BREAKLINE; // Boundary
	 		
	 		// Mail headers
	 		$headers = 'From: '.$this->sender . self::BREAKLINE; // sender
	 		$headers .= 'Reply-To: '.$this->reply . self::BREAKLINE; // Reply address
            $headers .= 'Return-Path: '.$this->reply . self::BREAKLINE;
	 		if(!empty($this->Cc)): $headers .= 'Cc: '.implode(', ', $this->Cc) . self::BREAKLINE; endif; // Carbon copies
	 		if(!empty($this->Bcc)): $headers .= 'Bcc: '.implode(', ', $this->Bcc) . self::BREAKLINE; endif; // Blind carbon copies
            
            $headers .= 'Date: '.date('r') . self::BREAKLINE; // Sending date
	 		$headers .= 'MIME-Version: 1.0' . self::BREAKLINE; // MIME version
	 		$headers .= 'X-Mailer: PHP/'.PHP_VERSION . self::BREAKLINE; // Sending software (not adviced: consired as SPAM)
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
	 		if(!mail(implode(', ', $this->To), $this->object, $message, $headers))
                throw new Exception('Mail : Failure while sending message');
	 	}

	 			
	 }
 
 ?>