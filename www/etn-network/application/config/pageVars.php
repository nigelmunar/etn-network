<?php
    declare(strict_types = 1);

    $breadcrumb      = [];
    $pageTitle       = '';
    $navName         = 'HOME';
    $subNavName      = '';
    $pageTitle       = 'ETN-NETWORK';
    $metaDescription = 'ETN-NETWORK.';


    switch($scriptName)
    {
        case 'index.php':
            $navName    = 'HOME';


            $scripts[] = [ 'https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js' ];
            $scripts[] = [ $siteURL . 'build/js/movie-datatable.js' ];

            break;

    }