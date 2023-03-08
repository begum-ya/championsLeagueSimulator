<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Standing;
use App\Models\Result;
use App\Models\FootballClub;
use App\Helpers\PremierLeagueUtils;

class HomeController extends Controller
{
    public function getLeague(){
        $league = Standing::join('football_clubs', 'football_clubs.id', '=', 'standings.football_club_id')
        ->orderBy('pts', 'DESC')
        ->orderBy('gd', 'DESC')
        ->get();

        $matches = $this->getResults('last');
        $champions = $this->getChampionPercentage('last');
      
        return view(
            'index',
            ['league' => $league,
            'matches'=>$matches['results'],
            'weeks'=>$matches['week_count'],
            'champions'=>$champions['success'] == 1 ? $champions['results'] : ""
         ]);
    }

    public function getStandings(Request $request){
        $payload = Standing::with('footballClub')
            ->orderBy('pts', 'DESC')
            ->orderBy('gd', 'DESC')
            ->get();

        return response()->json($payload);
    }

    public function getResults($weekSelect){

        $weekCount = Result::max('week');
        $week = $weekSelect != 'last' ? $weekSelect : $weekCount;
        $latestWeek = Result::whereNotNull('home_football_club_goal_count')->orderBy('week', 'DESC')->pluck('week')->first();
        if (empty($latestWeek)) {
            $latestWeek = 1;
        }

        $query = Result::with('homeFootballClub', 'awayFootballClub');
    
        if ($week) {
            $filteredWeek = $week;
            $query->where('week', $filteredWeek);
        }

        $payload = $query->orderBy('week')->get();

        foreach($payload as $py){
            $home_name = FootballClub::where(['id'=>$py->home_football_club_id])->first();
         
            $away_name = FootballClub::where(['id'=>$py->away_football_club_id])->first();

            $py->home_name = $home_name->name;
            $py->away_name = $away_name->name;
        }

        $championshipPrediction = FootballClub::all();

        $result = array(
            'results' => $payload,
            'latest_week' => $latestWeek,
            'week_count' => $weekCount,
            'championship_prediction' => $championshipPrediction,
        );
        return $result;
    }

    public function postPlayAll(Request $request){
        $results = Result::all();
        $weekCount = Result::max('week');
        $processedResults = PremierLeagueUtils::resultProccess($results, $weekCount);

        return response()->json($processedResults);
    }

    public function postResetLeague(Request $request){
        \Artisan::call('migrate:fresh');
        \Artisan::call('db:seed');
    }

    public function postNextWeek(Request $request){
        $week = $request->input('week');
        $results = Result::where('week','<=',$week)->get();
        
        if(empty(Result::whereNotNull('home_football_club_goal_count')->first())){
       
            $result = array(
                'success' => false
            );
         
        }else{
            $processedResults = PremierLeagueUtils::calculatePoint($results,$week);

            $result = array(
             'success' => true,
             'results' => $processedResults['result']
             );
        }
        return $result;
    }

    public function getChampionPercentage ($weekSelect){

        $weekCount = Result::max('week');
        $week = $weekSelect != 'last' ? $weekSelect : $weekCount;
        $results = Result::where('week','<=',$week)->get();

        $processedResults = PremierLeagueUtils::championshipPredictionBasedOnTheResults($results,$week);

       if($processedResults['success'] == 1){
            $result = array(
                'success' => true,
                'results' => $processedResults['results']
            );
       }else{
            $result = array(
                'success' => false
            );
       }
       return $result;
    }
}
