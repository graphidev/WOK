<?php
/*
    $array = array(
        'title' => 'My new post',
        'body' => 'This is my first post !'
    );
    
    echo json_encode($array);
*/
?>
{
  people: [
    {firstName: "Yehuda", lastName: "Katz"},
    {firstName: "Carl", lastName: "Lerche"},
    {firstName: "Alan", lastName: "Johnson"}
  ],

  session: {

    <?php if($_GET['key'] == 'valid'): ?>
        firstname: 'Sébastien', lastname: 'ALEXANDRE'
    <?php else: ?>
        error : 'true'
    <?php endif; ?>

   }
    
}