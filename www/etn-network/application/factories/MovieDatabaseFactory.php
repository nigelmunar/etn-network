<?php
    declare(strict_types = 1);

    require_once ROOT_PATH . 'application/factories/PDOFactory.php';
    require_once ROOT_PATH . 'application/database/MovieDatabase.php';

    
    class MovieDatabaseFactory
    {
        public static $movieDatabase = null;

        public static function create() : \MovieDatabase
        {
            if(is_null(MovieDatabaseFactory::$movieDatabase))
            {
                MovieDatabaseFactory::$movieDatabase = new \MovieDatabase(\PDOFactory::getConnection());
            }

            return MovieDatabaseFactory::$movieDatabase;
        }

    }
