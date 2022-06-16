<?php
    declare(strict_types = 1);

    namespace Entities;

    class Movie implements \JsonSerializable 
    {
        private $_movieID; //int
        private $_movieCode;// string
        private $_title; //string
        private $_description; //string
        private $_genre; //string
        private $_releaseDate; //\Datetime

        public function getMovieID() : int
        {
            return $this->_movieID;
        }
        
        public function setMovieID(int $value) : void
        {
            $this->_movieID = $value;
        }
        

        public function getMovieCode() : string
        {
            return $this->_movieCode;
        }

        public function setMovieCode(string $value) : void
        {
            $this->_movieCode = $value;
        }
   
        public function getTitle() : string
        {
            return $this->_title;
        }
        
        public function setTitle(string $value) : void
        {
            $this->_title = $value;
        }
        
        
        public function getDescription() : string
        {
            return $this->_description;
        }
        
        public function setDescription(string $value) : void
        {
            $this->_description = $value;
        }
        
        
        public function getGenre() : string
        {
            return $this->_genre;
        }
        
        public function setGenre(string $value) : void
        {
            $this->_genre = $value;
        }
        
        
        public function getReleaseDate() : string
        {
            return $this->_releaseDate;
        }
        
        public function setReleaseDate(string $value) : void
        {
            $this->_releaseDate = $value;
        }
        
        
        
        

        public function jsonSerialize() : array
        {
            return [
                'id' => $this->_movieID,
                'code' => $this->_movieCode,
                'title' => $this->_title,
                'description' => $this->_description,
                'genre' => $this->_genre,
                'releaseDate' => $this->_releaseDate
            ];
        }


        public function jsonDeserialize(string $jsonString) : void
        {
            $json = json_decode($jsonString, true);

            $this->_movieID = $json['id'];
            $this->_movieCode = $json['code'];
            $this->_title = $json['title'];
            $this->_description = $json['description'];
            $this->_genre = $json['genre'];
            $this->_releaseDate = $json['releaseDate'];
       
        }
    }