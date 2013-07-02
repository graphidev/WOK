<?php

    require_once(root(PATH_CORE.'/mysql.php'));
    
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $encode = 'UTF8';
    
    $db = new MySQL($host, $user, $password, $encode);
    
    $db->database('wok');

    if($db->createBase('mydatabase')):
        $db->database('mydatabase');        

        $table = $db->table('table_test');

        $table->remove();

        $table->AddIntColumn('id', 3);
        $table->AddStringColumn('column1', 40);
        $table->AddTextColumn('text_column');
        $table->AddColumn('test', 'VARCHAR(255)');
        $table->create($encode, 'default');
        
        //$table->dropColumn('test');
        $table->renameColumn('test', 'string_chain');
        
        $table->AddStringColumn('add1');
        $table->AddStringColumn('add2');
        $table->AddStringColumn('add3');
        $table->AddBinaryColumn('blop');
        $table->update('LAST', $encode, 'default');

        echo '<pre>';
        print_r($table->getInfos());
        echo '</pre>';

        echo '<pre>';
        $table = $db->table('table_test');
        print_r($table->getColumns());
        echo '<pre>';
        
        
        //$db->dropBase('mydatabase');

    else:
        exit('Error');
    endif;
    

    

    $db->close();

?>