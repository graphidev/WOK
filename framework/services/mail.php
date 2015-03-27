<?php

	/**
	* Web Operational Kit
	* The neither huger no micro extensible framework
	*
	* @copyright   All right reserved 2015, Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @author      Sébastien ALEXANDRE <sebastien@graphidev.fr>
	* @license     BSD <license.txt>
	**/

	namespace Framework\Services;

	/**
     * The Mail class is destinated to smooth
	 * the native PHP mail usage
     *
     * @require native mail() function using POSTFIX
     * @require get_mime_type() function
     *
     * @package Libraries
    **/
	class Mail {

        const BREAKLINE          = "\n"; // New line break
        const MAX_LINES_LENGTH   = 76; // Max content lines length
        const FORMAT_TEXT        = 'text/plain';
        const FORMAT_HTML        = 'text/html';

        private $format          = null; // Default format
	 	private $To              = array(); // Send to
	 	private $Cc              = array(); // Carbon copy
	 	private $Bcc             = array(); // Blind carbon copy
	 	private $From            = null; // Sender informations
	 	private $FromMail        = null; // Sender informations
        private $reply           = null; // Reply address
        private $replyMail       = null; // Reply address
	 	private $subject         = null; // Message object
	 	private $content         = null; // Message content
	 	private $attachments     = null; // Attachments
        private $headers         = array();


	 	/**
         * Generate a new mail
         * @param array   $headers		See Mail::addHeaders()
	 	**/
	 	public function __construct(array $headers = array()) {
            $this->addHeaders(array_merge(array(
				'X-Mailer' => 'PHP/'.PHP_VERSION
			), $headers));

			// Set default format
			$this->format = self::FORMAT_TEXT;
	 	}


        /**
         * (Re)Define custom email headers
         * @param array $headers 		Custom email headers
        **/
        public function addHeaders(array $headers) {
            $this->headers = array_merge($this->headers, $headers);
        }


	 	/**
         * Define addressee's informations
         * @param string    $email		Contact email addresse
         * @parem string    $name		Contact name
	 	**/
	 	public function addTo($email, $name = null) {
            if(is_array($email)):
                foreach($email as $key => $value) {

                    if(is_string($key)):

						$this->_checkEmail($key);
                        $this->To[] = '"=?UTF-8?B?'.base64_encode($value).'?=" <'.$key.'>';

                    else:

                        $this->_checkEmail($value);
						$this->_preventLineBreaks($name);
                        $this->To[] = (!empty($name) ? '"=?UTF-8?B?'.base64_encode($name).'?=" <'.$email.'>' : $email);

                    endif;
                }
            else:
			 	$this->_checkEmail($email);
                $this->To[] = (!empty($name) ? '"=?UTF-8?B?'.base64_encode($name).'?=" <'.$email.'>' : $email);;
            endif;

	 	}


	 	/**
         * Define carbon copies contacts
         * @param string    $email		Contact email address
         * @param string    $name		Contact name
         * @param bool      $bind		Set contact as bind carbon copy
	 	**/
	 	public function addCc($email, $name = null, $bind = false) {
           	$this->_checkEmail($email);
			$this->_preventLineBreaks($name);

            if($bind)
                $this->Bcc[] = (!empty($name) ? '"=?UTF-8?B?'.base64_encode($name).'?=" <'.$email.'>' : $email);
            else
                $this->Cc[] = (!empty($name) ? '"=?UTF-8?B?'.base64_encode($name).'?=" <'.$email.'>' : $email);
	 	}


	 	/**
	 	 * Define sender informations
	 	 * @param string     $email		From email address
	 	 * @param string     $name		From name
	 	**/
	 	public function setFrom($email, $name = null) {
            $this->_checkEmail($email);
			$this->_preventLineBreaks($name);

            $this->From = (!empty($name) ? '"=?UTF-8?B?'.base64_encode($name).'?=" <'.$email.'>' : $email);
			$this->FromMail = $email;

			if(empty($this->reply))
				$this->setReplyTo($email, $name);
	 	}


		/**
	 	 * Define Reply-To/Return-Path information
	 	 * @param string     $email		Reply-To/Return-Path email address
	 	 * @param string     $name		Reply-To/Return-Path name
	 	**/
	 	public function setReplyTo($email, $name = null) {
            $this->_checkEmail($email);
			$this->_preventLineBreaks($name);

            $this->reply = (!empty($name) ? '"=?UTF-8?B?'.base64_encode($name).'?=" <'.$email.'>' : $email);
			$this->replyMail = $email;
	 	}


		/**
         * Define email subject
         * @param string    $subject		Email subject
	 	**/
	 	public function setSubject($subject) {
		 	$this->subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
	 	}


		/**
         * Alias of setSubject
         * @see Mail::setSubject
	 	**/
	 	public function setObject($subject) {
		 	$this->setSubject($subject);
	 	}


	 	/**
         * Define message content
         * @param string    $message	Email content
         * @param string    $format		Content format
	 	**/
	 	public function setBody($message, $format = self::FORMAT_TEXT) {
		 	$this->content = $message;
            $this->format = $format;
	 	}


		/**
         * Alias of setBody
         * @see Mail::setBody
	 	**/
	 	public function setContent($message, $format = self::FORMAT_TEXT) {
		 	$this->setBody($message, $format);
	 	}


	 	/**
	 	 * Define attachement file
	 	 * @param string     $file		File's path to add
	 	 * @param string     $name		Redefine file name
	 	**/
	 	public function addAttachment($file, $name = null) {
            if(!is_readable($file))
				throw new \InvalidArgumentException($file, 122);

	 		$this->attachments[] = array(
	 			'type' => get_mime_type($file),
				'name' => (!empty($name) ? $name : basename($file)),
	 			'content' => base64_encode(file_get_contents($file))
	 		);
	 	}


	 	/**
	 	 * Try to send mail
	 	 * @require  $To, $From, $reply
	 	**/
	 	public function send() {
            if(empty($this->To))
                throw new \LogicException("developer:define.addressee", 210);

            if(empty($this->From))
                $this->setFrom($_SERVER['SERVER_ADMIN']);

	 		// Required informations
	 		$boundary = sha1(uniqid(microtime(), true)) . self::BREAKLINE; // Boundary

	 		// Mail headers
	 		$headers = 'From: '.$this->From . self::BREAKLINE; // sender
	 		$headers .= 'Reply-To: ' . $this->reply . self::BREAKLINE; // Reply address
            $headers .= 'Return-Path: ' . $this->reply . self::BREAKLINE;
	 		if(!empty($this->Cc)): $headers .= 'Cc: '.implode(', ', $this->Cc) . self::BREAKLINE; endif; // Carbon copies
	 		if(!empty($this->Bcc)): $headers .= 'Bcc: '.implode(', ', $this->Bcc) . self::BREAKLINE; endif; // Blind carbon copies

            $headers .= 'Date: '.date('r') . self::BREAKLINE; // Sending date
	 		$headers .= 'MIME-Version: 1.0' . self::BREAKLINE; // MIME version

            // Custom headers
            foreach($this->headers as $name => $value)
	 		    $headers .= "$name: $value" . self::BREAKLINE;


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
	 		if(!@mail(implode(', ', $this->To), $this->subject, $message, $headers, '-f '.$this->FromMail.' -r '.$this->replyMail))
                throw new \RuntimeException('system:send.mail', 133);

	 	}


		/**
		 * Validate email
		 * @throws InvalideArgumentException
		 * @param string $email		Email address to validate
		**/
		private function _checkEmail($email) {
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                throw new \InvalidArgumentException($email, 311);
		}

		/**
		 * Prevent whitespaces and so email injection
		 * @throws InvalidArgumentException
		 * @param	string	$input		String to check
		**/
		private function _preventLineBreaks($input) {
			if(preg_match('#(<CR>|<LF>|0x0A|%0A|0x0D|%0D|\\n|\\r)+#i', $input))
				throw new \DomainException($input, 311);
		}


	 }

 ?>
