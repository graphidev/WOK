It works !

<?php 
    if(!empty($_POST['token'])):
        if(Token::authorized('test', $_POST['token'], 2))
            exit('Token authorized');
        else
            echo 'Not authorized token';
    endif;
?>

<form action="#" method="post">
    <input type="hidden" name="token" value="<?php echo Token::generate('test'); ?>">
    <input type="submit">
</form>

<?php exit; ?>
<?php Session::logout(); ?>
<?php Session::login(null, true); ?>

<?php echo Session::id(); ?>