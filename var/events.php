<?php

    $events = new \Framework\Services\Events;

    $events->register('application->run:before', function($services) {

        $settings = $services->get('settings');

        //$settings->maintenance = false;

        if($settings->maintenance) {

            if($services->has('request'))
                \Framework\Core\Response::view('maintenance', 501 /*@TO-CHECK[CODE]*/)
                        ->cache('maintenance', \Framework\Core\Response::CACHETIME_LONG, \Framework\Core\Response::CACHE_PUBLIC)
                        ->render(true);

            else
                \Framework\Core\Response::text('Maintenance on run') ->render(false);

            exit; // Kill app run time processes
        }

    });

    return $events;
