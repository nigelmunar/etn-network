<?php

    declare(strict_types = 1);

    use Entities\Movie;

    require_once ROOT_PATH . 'application/tools/filterFunctions.php';
    require_once ROOT_PATH . 'application/tools/dateFunctions.php';
    require_once ROOT_PATH . 'application/utilities/RedisCacher.php';
    require_once ROOT_PATH . 'application/entities/Movie.php';
    require_once ROOT_PATH . 'application/logging/FileLogger.php';

    class MovieDatabase
    {
        private $pdo;
        private $movies = [];
        private $movieCount = [];
        private $movieResults = [];

        public function __construct(\PDO $pdo)
        {
            $this->pdo = $pdo;
        }


        public function getMovie(string $movieCode, int $movieID = -1) : Entities\Movie
        {
            $stmt = $this->pdo->prepare('
                SELECT `movie_id`, `movie_code`, `title`, `genre`, `description`, `release_date`
                FROM `movies`
                WHERE `live` = 1 '
                . (strlen($movieCode) > 0 ? ' AND `movie_code` = :movie_code' : ' AND `movie_id` = :movie_id'));
                

            if(strlen($movieCode) > 0)
            {   
                $stmt->bindValue(':movie_code', $movieCode, \PDO::PARAM_STR);
            }
            else
            {
                $stmt->bindValue(':movie_id', $movieID, \PDO::PARAM_INT);
            }

            $stmt->execute();

            while($row = $stmt->fetch())
            {
                $movie = new Movie();

                $movie->setMovieID((int)$row['movie_id']);
                $movie->setMovieCode((string)$row['movie_code']);
                $movie->setTitle((string)$row['title']);
                $movie->setGenre((string)$row['genre']);
                $movie->setDescription((string)$row['description']);
                $movie->setReleaseDate((string)$row['release_date']);
            }

            return $movie;
        }


        public function getMovieList(int $start, int $length, array $sortOrder = [], string $titleSearch = '', string $genreSearch = '', string $descriptionSearch = '', string $releaseDateSearch = '') : array
        {
            // $key = 'MovieList_' . $start . '_' . $length . '_' . json_encode($sortOrder) . '_' . $titleSearch . '_' . $genreSearch . '_' . $descriptionSearch . '_' . $releaseDateSearch;

            // if(!array_key_exists($key, $this->movieResults))
            // {
            //     $cachedValue = \RedisCacher::getCache($key);

            //     if(is_null($cachedValue))
            //     {                   
                    $unfilteredCount = $this->getMovieCount();
                    $filteredCount   = $this->getMovieCount($titleSearch, $genreSearch, $descriptionSearch, $releaseDateSearch);

                    $orderString = '';


                    for($i = 0; $i < count($sortOrder); $i++)
                    {
                        $orderPart = $this->getOrderStringPart($sortOrder[$i]);
                        
                        if(strlen($orderString) > 0 && strlen($orderPart) > 0)
                        {
                            $orderString .= ', ';
                        }

                        $orderString .= $orderPart;
                    }

                    if(strlen($orderString) === 0)
                    {
                        $orderString = '`title` ASC';
                    }


                    $stmt = $this->pdo->prepare('

                        SELECT `movie_id`, `movie_code`, `title`, `genre`, `description`, `release_date`
                        FROM `movies`
                        WHERE `live` = 1 ' .
                        (strlen($titleSearch) > 0 ? ' AND `title` LIKE CONCAT(\'%\', :title, \'%\') ' : '') .
                        (strlen($genreSearch) > 0 ? ' AND `genre` LIKE CONCAT(\'%\', :genre, \'%\') ' : '') . 
                        (strlen($releaseDateSearch) > 0 ? ' AND `genre` LIKE CONCAT(\'%\', :release_date, \'%\') ' : '') . 
                        (strlen($descriptionSearch) > 0 ? ' AND `description` LIKE CONCAT(\'%\', :description, \'%\') ' : '') . '
                        ORDER BY ' . $orderString . '
                        LIMIT :offset, :length
                    ');

                    $stmt->bindValue(':offset', $start,  \PDO::PARAM_INT);
                    $stmt->bindValue(':length', $length, \PDO::PARAM_INT);

                    if(strlen($titleSearch) > 0)
                    {
                        $stmt->bindValue(':title', $titleSearch, \PDO::PARAM_STR);
                    }

                    if(strlen($genreSearch) > 0)
                    {
                        $stmt->bindValue(':genre', $genreSearch, \PDO::PARAM_STR);
                    }

                    if(strlen($releaseDateSearch) > 0)
                    {
                        $stmt->bindValue(':release_date', $releaseDateSearch, \PDO::PARAM_STR);
                    }

                    if(strlen($descriptionSearch) > 0)
                    {
                        $stmt->bindValue(':description', $descriptionSearch, \PDO::PARAM_STR);
                    }


                    $stmt->execute();

                    $results = [ 
                        'recordsTotal' => $unfilteredCount,
                        'recordsFiltered' => $filteredCount,
                        'data' => [] 
                    ];
                            
                    while($row = $stmt->fetch())
                    {
                        $results['data'][] =
                        [

                            'id'             => (int)$row['movie_id'],
                            'code'           => (string)$row['movie_code'],
                            'title'          => (string)$row['title'],
                            'genre'          => (string)$row['genre'],
                            'description'    => (string)$row['description'],
                            'release_date'   => ((string)$row['release_date']),
                        ];
                    }

                    return $results;

            //         $this->movieResults[$key] = $results;

            //         \RedisCacher::setCache($key, json_encode($this->movieResults[$key]), MOVIES_CACHE_SECONDS, 'Movies');
            //     }
            //     else
            //     {
            //         $json = json_decode($cachedValue, true);

            //         $this->movieResults[$key] = $json;
            //     }
            // }

            // return $this->movieResults[$key];
        }



        public function addMovie(\Entities\Movie $movie) : \Entities\Movie
        {
            $stmt = $this->pdo->prepare('
                INSERT INTO `movies`(`title`, `genre`, `description`, `release_date`, `live`)
                VALUES (:title, :genre, :description, :release_date, 1)');

            $stmt->bindValue(':title', $movie->getTitle(), PDO::PARAM_STR);
            $stmt->bindValue(':genre', $movie->getGenre(), PDO::PARAM_STR);
            $stmt->bindValue(':description', $movie->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(':release_date', $movie->getReleaseDate(), PDO::PARAM_STR);

            $stmt->execute();

            $movieID = (int)$this->pdo->lastInsertId();


            $stmt = $this->pdo->prepare('
                SELECT `movie_code`
                FROM `movies`
                WHERE `movie_id` = :movie_id');
            
            $stmt->bindValue(':movie_id', $movieID, \PDO::PARAM_INT);

            $stmt->execute();

            if($row = $stmt->fetch())
            {
                $movieCode = (string)$row['movie_code'];

                $movie->setMovieCode($movieCode);
            }

            return $movie;
        }

        public function saveMovie(\Entities\Movie $movie) : void
        {
            $stmt = $this->pdo->prepare('
                UPDATE `movies` 
                SET `title` = :title,
                `genre` = :genre,
                `description` = :description,
                `release_date` = :release_date
                WHERE `movie_id` = :movie_id');

            $stmt->bindValue(':title', $movie->getTitle(), PDO::PARAM_STR);
            $stmt->bindValue(':genre', $movie->getGenre(), PDO::PARAM_STR);
            $stmt->bindValue(':description', $movie->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(':release_date', $movie->getReleaseDate(), PDO::PARAM_STR);
            $stmt->bindValue(':movie_id', $movie->getMovieID(), PDO::PARAM_INT);

            $stmt->execute();

        }

        public function deleteMovie(string $movieCode) : void
        {
            $stmt = $this->pdo->prepare('
                UPDATE `movies` SET `live` = 0 WHERE `movie_code` = :movie_code');

            $stmt->bindValue(':movie_code', $movieCode, PDO::PARAM_STR);

            $stmt->execute();
        }  


        public function getMovieCount(string $titleSearch = '', string $genreSearch = '', string $descriptionSearch = '', string $releaseDateSearch = '') : int
        {
            // $key = 'MoviesCount_' . $titleSearch . '_' . $genreSearch . '_' . $descriptionSearch . '_' . $releaseDateSearch;

            // if(!array_key_exists($key, $this->movieCount))
            // {
            //     $cachedValue = \RedisCacher::getCache($key);

            //     if(is_null($cachedValue))
            //     {
            //         $this->movieCount[$key] = 0;

                    $stmt = $this->pdo->prepare('
                        SELECT COUNT(1) AS `movie_count`
                        FROM `movies`
                        WHERE `live` = 1 ' .
                        (strlen($titleSearch) > 0 ? ' AND `title` LIKE CONCAT(\'%\', :title, \'%\') ' : '') .
                        (strlen($genreSearch)    > 0 ? ' AND `genre` LIKE CONCAT(\'%\', :genre, \'%\') ' : '') . 
                        (strlen($releaseDateSearch)    > 0 ? ' AND `genre` LIKE CONCAT(\'%\', :release_date, \'%\') ' : '') . 
                        (strlen($descriptionSearch)    > 0 ? ' AND `description` LIKE CONCAT(\'%\', :description, \'%\') ' : '') . '
                    ');
                    

                    if(strlen($titleSearch) > 0)
                    {
                        $stmt->bindValue(':title', $titleSearch, \PDO::PARAM_STR);
                    }

                    if(strlen($genreSearch) > 0)
                    {
                        $stmt->bindValue(':genre', $genreSearch, \PDO::PARAM_STR);
                    }
                    
                    if(strlen($releaseDateSearch) > 0)
                    {
                        $stmt->bindValue(':release_date', $releaseDateSearch, \PDO::PARAM_STR);
                    }

                    if(strlen($descriptionSearch) > 0)
                    {
                        $stmt->bindValue(':description', $descriptionSearch, \PDO::PARAM_STR);
                    }

                    $stmt->execute();

                    if($row  = $stmt->fetch())
                    {
                        return (int)$row['movie_count'];
                    }

            //         \RedisCacher::setCache($key, (string)$this->movieCount[$key], MOVIES_CACHE_SECONDS, 'Movies');
            //     }
            //     else
            //     {
            //         $this->movieCount[$key] = (int)$cachedValue;
            //     }
            // }

            // return $this->movieCount[$key];
        }

        private function getOrderStringPart(array $orderPart) : string
        {
            $orderString = '';

            if(count($orderPart) === 2)
            {
                switch($orderPart[0])
                {
                    case 'title':

                        $orderString .= '`title`';

                        break;

                    case 'genre':

                        $orderString .= '`genre`';

                        break;

                    case 'release_date':

                        $orderString .= '`release_date`';

                        break;
                    
                }

                if(strlen($orderString) > 0)
                {
                    if($orderPart[1] === 'asc')
                    {
                        $orderString .= ' ASC';
                    }
                    else
                    {
                        $orderString .= ' DESC';
                    }
                }
            }


            return $orderString;
        }
        
    }