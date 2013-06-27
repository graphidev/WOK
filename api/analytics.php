<?php
    
    /**
     * Initialize WOK
    **/
	require_once "../core/init.php";

    

    /**
     * Create and return a 1x1 transparent image.
    **/
    header ("Content-type: image/png");
    
    // Create the 1x1 image
    $image = imagecreatetruecolor(1,1);
    
    // Make it transparent
    $black = imagecolorallocate($image, 0, 0, 0);
    imagecolortransparent($image, $black);
    
    
    imagepng($image); // Send it to browser
    imagedestroy($image); // Destroy resource
    
?>