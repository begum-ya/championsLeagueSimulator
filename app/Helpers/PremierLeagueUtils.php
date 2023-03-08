<?php

namespace App\Helpers;

use App\Models\FootballClub;
use App\Models\Standing;

class PremierLeagueUtils
{
    public static function matchResultPredictionCalc(FootballClub $homeFC, FootballClub $awayFC)
    {
        $homeFCGoalCount = 0;
        $awayFCGoalCount = 0;

        $HOME_FC_ADVANTAGE_RATE = 1.3;

        $homeFCStraightArr = [
            $homeFC->attack,
            $homeFC->midfield,
            $homeFC->defence,
        ];

        $awayFCStraightArr = [
            $awayFC->attack,
            $awayFC->midfield,
            $awayFC->defence,
        ];

        $homeFCStraight = (array_sum($homeFCStraightArr) / count($homeFCStraightArr) * $HOME_FC_ADVANTAGE_RATE);
        $awayFCStraight = (array_sum($awayFCStraightArr) / count($awayFCStraightArr));

        $probabilityLength = 6000;
        $homeFCProbability = $homeFCStraight / $probabilityLength;
        $awayFCProbability = $awayFCStraight / $probabilityLength;

        for ($i = 0; $i < 90; $i++) {
            if (mt_rand($homeFCProbability, $probabilityLength) <= $homeFCProbability * $probabilityLength) {
                $homeFCGoalCount++;
            }

            if (mt_rand($awayFCProbability, $probabilityLength) <= $awayFCProbability * $probabilityLength) {
                $awayFCGoalCount++;
            }
        }

        return [
            'homeFCGoalCount' => $homeFCGoalCount,
            'awayFCGoalCount' => $awayFCGoalCount,
        ];
    }

    public static function resultProccess($results,$week, $savePermanently = true)
    {
        foreach ($results as $result) {
            if (!empty($result->home_football_club_goal_count) || !empty($result->away_football_club_goal_count)) {
                continue;
            }
            $matchResultPrediction = self::matchResultPredictionCalc($result->homeFootballClub, $result->awayFootballClub);

            $result->home_football_club_goal_count = $matchResultPrediction['homeFCGoalCount'];
            $result->away_football_club_goal_count = $matchResultPrediction['awayFCGoalCount'];
            if ($savePermanently) {
                $result->save();
            }

         
         /*   $homeFCStanding->p += 1;
            $awayFCStanding->p += 1;

            $homeFCStanding->gd += ($matchResultPrediction['homeFCGoalCount'] - $matchResultPrediction['awayFCGoalCount']);
            $awayFCStanding->gd += ($matchResultPrediction['awayFCGoalCount'] - $matchResultPrediction['homeFCGoalCount']);

            if ($matchResultPrediction['homeFCGoalCount'] > $matchResultPrediction['awayFCGoalCount']) {
                $homeFCStanding->pts +=  3;
                $homeFCStanding->w +=  3;
                $awayFCStanding->l += 1;
            } else if ($matchResultPrediction['homeFCGoalCount'] === $matchResultPrediction['awayFCGoalCount']) {
                $homeFCStanding->pts +=  1;
                $awayFCStanding->pts += 1;
                $homeFCStanding->d +=  1;
                $awayFCStanding->d += 1;
            } else {
                $awayFCStanding->pts += 3;
                $awayFCStanding->w += 3;
                $homeFCStanding->l +=  1;
            }

            if ($savePermanently) {
                $homeFCStanding->save();
                $awayFCStanding->save();
            }*/
        }
        $calculatePoint = self::calculatePoint($results, $week);
            if($calculatePoint['success'] != false){
                foreach($calculatePoint['result'] as $val){
                    Standing::where(['football_club_id'=>$val['club_id']])->
                    update(['pts'=>$val['pts'], 'p'=>$week, 'w'=>$val['w'],
                    'd'=>$val['d'], 'l'=>$val['l'] , 'gd'=>$val['gd']]);
                }
            }
    

        return [
            'results' => $results
        ];
    }

 
     public static function championshipPredictionBasedOnTheResults($results, $week)
    {
        
         $standings = self::calculatePoint($results, $week);
     
         if($standings['success'] == 1){
            $total = 0;
            foreach($standings['result'] as $val){
                $total += $val['pts']; 
            }

            foreach($standings['result'] as $key=>$val){
                $standings['result'][$key]['avg'] = round(($val['pts']/$total)*100 ,2); 
            }
            return [
                'success'=>true,
                'results' => $standings['result']
            ];
         }
         return [
            'success'=>false
        ];
      
    }


    public static function calculatePoint($results, $week)
    {
  
        $array = array();

        foreach($results as $result){
            
            $array[$result['home_football_club_id']]['goalCountF'] = 0;
            $array[$result['away_football_club_id']]['goalCountF'] = 0;

            $array[$result['home_football_club_id']]['name'] = $result->homeFootballClub->name;
            $array[$result['away_football_club_id']]['name'] = $result->awayFootballClub->name;

            $array[$result['home_football_club_id']]['club_id'] = $result['home_football_club_id'];
            $array[$result['away_football_club_id']]['club_id'] = $result['away_football_club_id'];

            $array[$result['home_football_club_id']]['goalCountA'] = 0;
            $array[$result['away_football_club_id']]['goalCountA'] = 0;

            $array[$result['home_football_club_id']]['w'] = 0;
            $array[$result['away_football_club_id']]['w'] = 0;

            $array[$result['home_football_club_id']]['l'] = 0;
            $array[$result['away_football_club_id']]['l'] = 0;

            $array[$result['home_football_club_id']]['d'] = 0;
            $array[$result['away_football_club_id']]['d'] = 0;
         }


        foreach($results as $result){
            
           $array[$result['home_football_club_id']]['goalCountF']  += $result['home_football_club_goal_count'];
           $array[$result['away_football_club_id']]['goalCountF']  += $result['away_football_club_goal_count'];

           $array[$result['home_football_club_id']]['goalCountA']  += $result['away_football_club_goal_count'];
           $array[$result['away_football_club_id']]['goalCountA']  += $result['home_football_club_goal_count'];

           if($result['home_football_club_goal_count'] > $result['away_football_club_goal_count'] ){
            $array[$result['home_football_club_id']]['w']++;
            $array[$result['away_football_club_id']]['l']++;
           }

           if($result['home_football_club_goal_count'] < $result['away_football_club_goal_count'] ){
            $array[$result['home_football_club_id']]['l']++;
            $array[$result['away_football_club_id']]['w']++;
           }

           if($result['home_football_club_goal_count'] == $result['away_football_club_goal_count'] ){
            $array[$result['home_football_club_id']]['d']++;
            $array[$result['away_football_club_id']]['d']++;
           }
        }
        
      if(!empty($array)){
        foreach($array as $key=>$val){
            $array[$key]['pts'] = $val['w']*3 + $val['d'];
            $array[$key]['gd'] = $val['goalCountF']-$val['goalCountA'];
        }
        $newArray = array();

        foreach($array as $val){
            array_push($newArray,$val);
        }
  
        //return $newArray;
        return ['success'=>true,
          'result' => $newArray];
      }else{
        return ['success'=>false,
                ];
      }
       

    }
}
