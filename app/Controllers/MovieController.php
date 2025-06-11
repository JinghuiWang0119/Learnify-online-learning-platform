<?php

namespace App\Controllers;

use App\Libraries\MongoDB;
use CodeIgniter\Controller;
use App\Models\MovieModel;
use App\Config\Services;

class MovieController extends Controller
{
    protected $mv;
   
    public function index()
    {
        // Initialize the MongoDB library
        
        $this->mv = new MovieModel();
                
        $mvs = $this->mv->getAllMovies();
        //$mvs = $this->mv->getMovieByYear(2008);
        
        //$mvs = $this->mv->getMovieByCountry('UK');
        //$mvs = $this->mv-/>getMovieByRatingAndCountry("8.5","USA");

        // Pass the movies data to the view
        $data = ['mvs' => $mvs,
                'pager' => $this->mv->pager];
            
        return view('movie_view', $data);
    }
}