<?php
	
	/**
		Mail (class)
		
		@version 1.3
		@date 2012-11-18
		@author Sébastien ALEXANDRE 
				
		@required function mail() allowed
		@required function strip_magic_quotes()
		
	*/
	
	class mail {
	 	private $template;	// mail model
	 	private $addressee = array(); // Send to
	 	private $Cc = array(); // Carbon copy
	 	private $Bcc = array(); // Blind carbon copy
	 	private $sender = array(); // Sender informations
	 	private $object = null; // Message object
	 	private $content = null; // Message content
	 	private $attachments = null; // Attachments
	 	private $signature; // Really ? Need a description ?
	 	
	 	/**
	 		@ new / construct
	 		@param (string) $object = message object
	 	*/
	 	public function __construct($object = null) {
	 		$this->object($object);
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ template
	 		@about define mail template and format
	 		@param (string) $template = template file path
	 		@parem (string) $format = mail format (html/text)
	 	*/
	 	public function template($template) {
	 		if(file_exists($template)):
	 			$this->template = @file_get_contents($template);
	 		else:
	 			$this->template = "%object%\n\n%content%\n\n%signature%";
	 		endif;
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ object
	 		@about define mail object
	 		@param (string) $value = mail object
	 	*/	 	
	 	public function object($value) {
	 		$this->object =  strip_magic_quotes($value);
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ to
	 		@about define addressee's informations.
	 		@param (string) $mail = addressee's mail
	 		@parem (string) $name = addressee's name (mail will be used if $name is not defined)
	 	*/
	 	public function to($mail, $name = null) {
	 		$this->addressee['addr'] = strip_magic_quotes($mail);
	 		$this->addressee['name'] = (empty($name) ? strip_magic_quotes($mail) : strip_magic_quotes($name));
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ Cc
	 		@about define carbon copies mails
	 		@param (mixed) $mail = mails addresses (array, or separate with a comma)
	 	*/
	 	public function Cc($mail) {
	 		if(is_array($mail)):
	 			$this->Cc = array_merge($this->Cc, strip_magic_quotes($mail));
	 		else:
	 			$this->Cc[] = strip_magic_quotes($mail);
	 		endif;
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ Bcc
	 		@about define blind carbon copies mails
	 		@param (mixed) $mail = mails addresses (array, or separate with a comma)
	 	*/
	 	public function Bcc($mail) {
	 		if(is_array($mail)):
	 			$this->Bcc = array_merge($this->Bcc, strip_magic_quotes($mail));
	 		else:
	 			$this->Bcc[] = strip_magic_quotes($mail);
	 		endif;
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ from
	 		@about define sender informations
	 		@param (string) $mail = sender's mail address
	 		@param (string) $name = senders' name
	 		@param (string $more = additional informations about the sender (added as signature)
	 	*/
	 	public function from($mail, $name, $more = null) {
			$this->sender['name'] = strip_magic_quotes($name);
			$this->sender['addr'] = strip_magic_quotes($mail);
			
			$this->signature = $this->sender['name']."\n".$this->sender['addr'];
			if(!empty($more)):
				$this->signature .= "\n".strip_magic_quotes($more);
			endif;
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ content
	 		@about define message content
	 		@param (string) $message = message content
	 		@require method template()
	 	*/
	 	public function content($message) {
		 	$this->content= $message;
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ attachment
	 		@about define attachement file
	 		@param (string) $path = attachment file path
	 	*/	 	
	 	public function attachment($path, $name) {
	 		$this->attachments[] = array(
	 			'name' => $name,
	 			'type' => get_mime_type($path),
	 			'content' => base64_encode(file_get_contents($path))
	 		);
	 	}
	 	
	 	// -----------------------------------
	 	
	 	/**
	 		@ send
	 		@about try to send mail and return result
	 		@required function construct() / object()
	 		@required function to()
	 		@required function sender()
	 		@required function content()
	 	*/
	 	public function send() {
	 	 	 		
	 		// Required informations		
	 		$rn = "\n"; // Newline
	 		$boundary = '--'.sha1(uniqid(microtime(), true)).'--'.$rn; // Boundary
	 		
	 		// Mail headers
	 		$headers = 'From: "'.utf8_decode($this->sender['name']).'" <'.$this->sender['addr'].'>'.$rn; // sender
	 		$headers .= 'Reply-To: '.$this->sender['addr'].$rn; // Reply address
	 		$headers .= 'Date: '.date('r').$rn; // Sending date
	 		$headers .= 'Return-Path: <'.$this->sender['addr'].'>'.$rn;
	 		$headers .= 'MIME-Version: 1.0'.$rn; // MIME version
	 		
	 		if(count($this->Cc) > 0): $headers .= 'Cc: '.implode(', ', $this->Cc).$rn; endif; // Carbon copy
	 		if(count($this->Bcc) > 0): $headers .= 'Bcc: '.implode(', ', $this->Bcc).$rn; endif; // Blind carbon copy
	 		
	 		//$headers .= 'X-Mailer: PHP/'.PHP_VERSION.$rn; // Sending software (not adviced: consired as SPAM)
	 		$headers .= "Content-Type: multipart/mixed; boundary=$boundary"; // Content-Type
	 		
	 		// Default text message
	 		$message = "--$boundary $rn";
	 		$message .= "Content-type: text/plain; charset=\"utf-8\"$rn";
	 		$message .= "Content-Transfer-Encoding: 8bit$rn$rn";
	 		$message = $this->content.$rn.$rn.$this->signature.$rn.$rn;
	 		
	 		// HTML message
	 		$message .= "--$boundary $rn";
	 		$message .= "Content-type: text/html; charset=\"utf-8\"$rn";
	 		$message .= "Content-Transfer-Encoding: 8bit$rn$rn";
	 		
	 		// Replace template tags by values
	 		$search = array('%object%', '%content%', '%signature%'); // Tags
	 		$replace = array($this->object, nl2br($this->content), nl2br($this->signature)); // Values
	 		$content = str_replace($search, $replace, $this->template); // Replacement
			$message .= $content.$rn.$rn;

	 		// Adding message attachment
	 		if(!empty($this->attachments)):
	 			foreach($this->attachments as $key => $attachment) {
		 			$message .= "--$boundary $rn"; 
		 			$message .= "Content-type:".$attachment['type'].";name=".$attachment['name'].$rn; // Type & name
		 			$message .= "Content-Transfer-Encoding: base64$rn"; // Encoding method
		 			$message .= "Content-Disposition:attachment; filename=\"".$attachment['name']."\"$rn$rn";
		 			$message .= chunk_split($attachment['content']) . $rn . $rn; // File content
		 		}
	 		endif;
	 		
	 		$message .= "--$boundary $rn"; // End message
	 		
	  		// Send mail
	 		return mail($this->addressee['addr'], $this->object, $message, $headers);
	 	}
	 			
	 }
 
 ?>