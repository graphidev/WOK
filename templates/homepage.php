<html>

    <head>
    
        <title>tralala</title>
        
    </head>
    
    <body>
    
        Quamquam ab velit. Nescius tamen noster do dolore, an de varias tempor do ex iis 
        lorem dolor eram, fugiat quo de quid senserit. Offendit magna singulis quo duis 
        commodo quamquam, ubi varias aliqua duis mandaremus, nam hic fidelissimae in 
        singulis quis qui proident praesentibus est sed nam illum enim tamen, est quid 
        tempor e eu aliqua hic velit. Amet praesentibus possumus anim vidisse id se sed 
        dolore quis legam, in aliquip reprehenderit nam est anim quorum eram doctrina, 
        minim comprehenderit senserit veniam officia id est fugiat veniam magna 
        litteris, ea magna velit eu proident quo nam nulla consequat comprehenderit. 
        Multos voluptatibus admodum dolor quibusdam. O quo graviterque e doctrina est 
        duis eiusmod sed doctrina aliqua ita occaecat praetermissum, ita cupidatat quo 
        laboris. Do tamen consectetur. Voluptate quem nostrud singulis, se esse a nisi 
        hic anim nam fabulas, ut anim nostrud comprehenderit, nisi te singulis si fore 
        excepteur coniunctione, te hic familiaritatem, amet laboris relinqueret.
        
        Nescius voluptatibus ex excepteur, singulis comprehenderit id excepteur. Ne 
        legam litteris fidelissimae, quibusdam graviterque id quibusdam quo si nisi 
        appellat tractavissent, ipsum mandaremus hic arbitror aut quibusdam culpa e 
        proident firmissimum, nulla pariatur o irure labore, eiusmod noster singulis 
        laboris quo incididunt ubi ipsum tempor. Fore officia ubi arbitrantur. Quid se 
        ingeniis nam ita quorum irure aliqua admodum. Ingeniis enim fugiat proident 
        dolor, aliqua fabulas instituendarum. An quis duis ut proident id quorum ex 
        expetendis, veniam consequat relinqueret qui tempor de tempor, doctrina te fore 
        mandaremus te ubi fabulas do cernantur te probant magna multos appellat enim, 
        admodum e quae eiusmod. Aut nisi despicationes, cernantur do nescius aut do 
        singulis imitarentur ab ubi culpa consectetur et ad esse malis ne ingeniis, si 
        aliqua do velit eu aliquip lorem varias commodo magna do pariatur quae labore si 
        amet.
        
        Ne nisi fabulas nostrud sed se appellat iis fabulas, eram et cernantur iis ab 
        offendit eu proident, amet firmissimum occaecat quem commodo, qui tempor 
        relinqueret et legam consequat pariatur, e elit quem et ullamco. Irure eiusmod 
        arbitrantur, proident aliqua incididunt. Est cupidatat a senserit, quae ita ad 
        quis nescius, labore an litteris nam aliqua eu doctrina do nisi occaecat. Ubi 
        quae an anim, varias fabulas non distinguantur ita laborum tamen in incididunt 
        transferrem te an minim labore sed deserunt, appellat in summis, excepteur se 
        commodo id sed amet tempor quamquam, voluptate magna quae deserunt cillum. In 
        senserit o iudicem ex qui elit nisi aliqua voluptate ea litteris ut amet 
        nostrud, occaecat o expetendis qui do minim minim quorum nostrud, lorem tempor 
        admodum, officia legam litteris, ut ubi firmissimum. Nulla cernantur voluptate 
        ex qui quorum concursionibus. Ab probant id possumus, voluptate varias aute de 
        amet, nescius nisi deserunt probant eu offendit sed amet.
        
        Ex a minim malis aliqua. Id fugiat minim ex mentitum. Velit an laboris aut 
        labore, et esse sunt quis deserunt ut quis aliquip se lorem aute sed malis 
        expetendis ullamco, id multos litteris arbitrantur ad elit do doctrina, in 
        quorum officia imitarentur, mentitum sunt ad incurreret transferrem. Ut veniam 
        veniam ab occaecat, a cillum fugiat duis laboris quo non est nisi fore magna qui 
        culpa iis in quae eiusmod, et veniam ullamco tempor est malis exercitation 
        incididunt amet nescius est iis sed quid cernantur, quae expetendis consectetur. 
        Sed sint aliqua labore quamquam. Quis fabulas iis labore quid ut legam cernantur 
        transferrem.
    
    </body>
    
</html>

<?php /*
<?php
    
    Session::set('session.testme','tralala');
    echo Session::get('session.testme', 'not found');
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


<?php
    exit;
    Session::language('fr_FR');
    $data = array('data'=>date('Y-m-d H:i:s'));
    echo _e('tests:test.datetime.default', $data).'<br />';
    echo _e('tests:test.datetime.formated', $data).'<br />';
    echo _e('tests:test.datetime.original', $data);
    
    echo nl2br(_e('tests:test.return', $data));

?>


<?php
    
    exit;

    $data = array('a', 'b', 'c');
    $data = array(date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
    
    $data = array(
        $data,
        $data,
        $data
    );

    $options = array(
        'recursive' => true ,
        'parser' => function($data) {
            return $data;   
        }
    );

    $loop = new loop($data, $options);
    
    //if($loop->have_entries()) echo $loop->date('d/m/Y').'<br>';
    
    while($loop->have_entries()):
        
        $data = $loop->entry(); 
        while($data->have_entries()):
            echo '['.$data->index(true).'/'.$data->total().'] '.$data->entry().'<br />';
            $data->next_entry();

        endwhile;
        echo '<br><br>';

        

        $loop->next_entry();

    endwhile;

?>



<?php 
    exit;
    Session::logout();
    Session::login(null, true);

    echo Session::id();

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
*/ ?>