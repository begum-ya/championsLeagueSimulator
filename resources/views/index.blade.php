<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{env('APP_NAME')}}</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
      
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
            .loader {
                border: 16px solid #f3f3f3;
                border-radius: 50%;
                border-top: 16px solid #3498db;
                width: 120px;
                height: 120px;
                -webkit-animation: spin 2s linear infinite; /* Safari */
                animation: spin 2s linear infinite;
                position: absolute;
                left: 50%;
                top: 50%;
            }
            #loader-wrapper-form {
                background: #0a0a0a8a;
                height: 100%;
                width: 100%;
                position: fixed;
                left: 0;
                top: 0;
                text-align: center;
                z-index: 1050;
                display: none;
                align-items: center;
                justify-content: space-around;
            }
            /* Safari */
            @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div id="loader-wrapper-form">
    <div class="loader"></div></div>
    </div>
        <div>
        <div class="container-fluid">
            <div class="container mt-5 mb-4">
                <div class="text-center" style="font-size: 64px;">
                    Premier Champions League
                </div>

            </div>
            <div class="container">
                <div class="row">
                    <div class="col-8">
                        <div class="d-flex">
                            <div class="card w-100">
                                <table class="table table-sm table-striped m-0" id="mytable">
                                    <thead>
                                        <tr>
                                            <th colSpan="7" scope="colgroup">
                                                League Table
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Club</th>
                                            <th scope="col">PTS</th>
                                            <th scope="col">P</th>
                                            <th scope="col">W</th>
                                            <th scope="col">D</th>
                                            <th scope="col">L</th>
                                            <th scope="col">GD</th>
                                        </tr>
                                    </thead>
                                    <tbody id="leauge-table-body">
                    @if (!empty($league))
                        @foreach ($league as $lg)

                            <tr class="{{$lg->id}}">
                                <td id="row0"> {{$lg->name}}</td>
                                <td id="row1">@if(isset($lg->pts)) {{$lg->pts}} @else 0 @endif</td>
                                <td id="row2">@if(isset($lg->p)) {{$lg->p}} @else 0 @endif</td>
                                <td id="row3">@if(isset($lg->w)) {{$lg->w}} @else 0 @endif</td>
                                <td id="row4">@if(isset($lg->d)) {{$lg->d}} @else 0 @endif</td>
                                <td id="row5">@if(isset($lg->l)) {{$lg->l}} @else 0 @endif</td>
                                <td id="row6">@if(isset($lg->gd)) {{$lg->gd}} @else 0 @endif</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                                </table>
                            </div>

                            <div class="card w-100">
                                <table class="table table-sm table-striped m-0" id="match-table">
                                    <thead>
                                        <tr>
                                            <th colSpan="3" scope="colgroup">
                                                Match Results
                                            </th>
                                        </tr>
                                     
                                        <tr>
                                                <th colSpan="3" scope="colgroup">
                                                
                                                    <select id="match-select" onChange="getResults()">
                                                    @if (!empty($weeks))
                        @for ($i=0; $i< $weeks; $i++ )
                                 <option value="{{$i+1}}">
                                                    {{$i+1}}
                                            </option>            
                                                                @endfor
                    @endif
                                                    </select>
                                                    Week Match Results
                                                </th>
                                      
                                            </tr>
                                           
                                    </thead>
                                    <tbody id="weekly-matches">
                   

                    @if (!empty($matches))
                        @foreach ($matches as $results)
                            <tr>
                                <td>{{$results['home_name']}}</td>
                                <td>
                                 {{$results['home_football_club_goal_count']}}
                                    -
                                 {{$results['away_football_club_goal_count']}} 
                                </td>
                                <td>{{$results['away_name']}}</td>
                            </tr>

                        @endforeach
                    @endif
                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card flex-row w-100 px-3 py-2" style= "justify-content: space-between">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onClick="playAllHandle()"
                            >
                                Play All
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                onClick="resetLeagueHandle()"
                            >
                                Reset League
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onClick="nextWeek()"
                            >
                                Next Week
                            </button>
                        </div>
                    </div>
                

                    <div class="col-4">
                        <table class="table table-sm table-striped m-0" id="champions-table">
                            <thead>
                                <tr>
                                    <th colSpan="2" scope="colgroup">
                                        Week Predictions of Championship
                                    </th>
                                </tr>
                                <tr>
                                            <th scope="col">Club</th>
                                            <th scope="col">Avg</th>
                                        </tr>
                            </thead>
                            <tbody>
                           
                            @if (!empty($champions))
                        @foreach ($champions as $results)
                            <tr>
                                <td>{{$results['name']}}</td>
                                <td>
                                 {{$results['avg']}}
                                </td>
                            </tr>

                        @endforeach
                    @endif
                                   
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
      <script>
        $(document).ready(function(){
            document.getElementById('match-select').value = document.getElementById('match-select').length;
        });
        </script>
    </body>
</html>
