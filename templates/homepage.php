<?php
    
    $data = array('a', 'b', 'c');
    $data = array(date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));

    $loop = new loop($data);
    
    if($loop->have_entries()) echo $loop->date('d/m/Y').'<br>';
    
    while($loop->have_entries()):

        echo '['.$loop->index(true).'/'.$loop->total().'] '.$loop->entry().'<br />';

        $loop->next_entry();

    endwhile; 

?>



<?php 
    exit;
    Session::logout();
    Session::login(null, true);

    echo Session::id();

?>

<?php
    exit;
    $result = Cookie::set('blabla', 'lalala', null, true);
    echo $result ? 'Cookie sent' : 'Error'; 
    echo '<br />';
    if(Cookie::exists('blabla'))
        echo Cookie::get('blabla', true); 
    else
        echo 'Cookie doesn\'t exists';
?>


<?php exit; ?>

[<?php echo path(); ?>]

It works !

<?php 
    if(!empty($_POST['token'])):
        if(Token::authorized('test', $_POST['token'], 5)):
            Token::destroy('test');
            exit('Token authorized');
        else:
            echo 'Not authorized token';
        endif;
    endif;
?>


<form action="#" method="post">
    <input type="hidden" name="token" value="<?php echo Token::generate('test'); ?>">
    <input type="submit" value="check token">
</form>


<?php exit; ?>
<?php Session::logout(); ?>
<?php Session::login(null, true); ?>

<?php echo Session::id(); ?>