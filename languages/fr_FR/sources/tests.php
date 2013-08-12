<?php

    $tests = array(
        'default' => 'dqsdsq :input qsdqsd',
        'avar' => 'This is a var',
        'formats' => array(
            'breaklines' => 'holé - (:data|breaklines) - holé',
            'money' => '{:money|%i}',
            'datetime' => '[:date|Y-m-d H:i:s]',
            'variable' => 'Workin on &[~:avar] for some times',
            'resume' => 'Long text : (:input|5)',
            'reverse' => '(:input|reverse)',
            'upper' => '(:input|uppercase)',
            'lower' => '(:input|lowercase)',
        )
    );

?>