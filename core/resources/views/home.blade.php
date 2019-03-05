@extends('front.layouts.admaster')
@section('content')
    <style>
        #chartdiv {
            width : 100%;
            height  : 300px;
        }
        #content {
            background-color: white;
        }
        .ball {
            width: 45px;
        }
        .font-bold {
            font-weight: bold;
            font-size: 14px;
        }
        .font-black {
            color: black;
            font-weight: bold;
        }
        .table>tbody>tr>td {
            padding: 8px 0 !important;
        }
        .play-button {
            width: 40%;
            cursor: pointer;
            margin: 5% 30% 10%;
        }
        #page-container{
            background-color: white;
        }
        .pace-done{
            background-color: white !important;
        }
        .pace-running{
            background-color: white !important;
        }

    </style>

    <!-- Chart code -->
    <script>
        const chartData = generateChartData();
        const chart = AmCharts.makeChart("chartdiv", {
            "type": "serial",
            "theme": "light",
            "marginRight": 80,
            "dataProvider": chartData,
            "valueAxes": [{
                "position": "left",
                "title": "Price History"
            }],
            "graphs": [{
                "id": "g1",
                "fillAlphas": 0.4,
                "valueField": "visits",
                "balloonText": "<div style='margin:5px; font-size:19px;'><b>$ [[value]]</b></div>"
            }],
            "chartScrollbar": {
                "graph": "g1",
                "scrollbarHeight": 20,
                "backgroundAlpha": 0,
                "selectedBackgroundAlpha": 0.1,
                "selectedBackgroundColor": "#888888",
                "graphFillAlpha": 0,
                "graphLineAlpha": 0.5,
                "selectedGraphFillAlpha": 0,
                "selectedGraphLineAlpha": 1,
                "autoGridCount": true,
                "color": "#333"
            },
            "chartCursor": {
                "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
                "cursorPosition": "mouse"
            },
            "categoryField": "date",
            "categoryAxis": {
                "minPeriod": "mm",
                "parseDates": true
            },
            "export": {
                "enabled": true,
                "dateFormat": "YYYY-MM-DD HH:NN:SS"
            }
        });

        chart.addListener("dataUpdated", zoomChart);
        function zoomChart() {

            chart.zoomToIndexes(chartData.length - 250, chartData.length - 100);
        }

        function generateChartData() {

            const chartData = [];
            @foreach ($allprice as $pri)
            chartData.push({
                date:  new Date('{{$pri->created_at}}'),
                visits: parseFloat({{$pri->price}})
            });

            @endforeach
                return chartData;
        }
    </script>
    <div class="row">
        <!-- begin col-3 -->
    <!-- <div class="col-md-3 col-sm-6">
      <div class="widget widget-stats bg-green">
        <div class="stats-icon"><i class="fa fa-bitcoin"></i></div>
        <div class="stats-info">
          <h4>BTC RATE</h4>
          <p>${{number_format(floatval($currentRate) , $gset->decimalPoint, '.', '')}}</p>
        </div>
      </div>
    </div> -->
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-md-12 col-sm-12" style="display: none;" id="btc_balance">
            <div class="widget widget-stats bg-purple">
                <div class="stats-icon"><img src="{{ asset('assets/images/coin/btc.png') }}" style="width: 100%;  " alt=""></div>
                <div class="stats-info">
                    <h4>BitCoin BALANCE</h4>
                    <p>{{number_format(floatval(Auth::user()->bitcoin) ,  $gset->decimalPoint, '.', '')}}</p>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12" style="display: none;" id="dmc_balance">
            <div class="widget widget-stats bg-red">
                <div class="stats-icon"> <img src="{{ asset('assets/images/coin/dmc.png') }}" style="width: 100%; "></div>
                <div class="stats-info">
                    <h4>DynamicCoin BALANCE</h4>
                    <p>{{number_format(floatval(Auth::user()->balance) ,  $gset->decimalPoint, '.', '')}}</p>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12" style="display: none;" id="oro_balance">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"> <img src="{{ asset('assets/images/coin/oro.png') }}" style="width: 100%; "></div>
                <div class="stats-info">
                    <h4>Oro BALANCE</h4>
                    <p>{{number_format(floatval(Auth::user()->balance) , $gset->decimalPoint, '.', '')}}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6" id="play_btc">
            <img src="{{ asset('assets/images/coin/playBTC.jpg') }}" class="play-button">
        </div>
        <div class="col-md-6 col-sm-6" id="play_dmc">
            <img src="{{ asset('assets/images/coin/playdmc.jpg') }}" class="play-button">
        </div>
        <div class="col-md-6 col-sm-6" id="play_free">
            <img src="{{ asset('assets/images/coin/playfree.jpg') }}" class="play-button">
        </div>
        <div class="col-md-6 col-sm-6" id="play_oro">
            <img src="{{ asset('assets/images/coin/playoro.jpg') }}" class="play-button">
        </div>
    </div>
    <div class="row" id="game_container" style="display: none;">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <img style="width: 100%;" src="{{asset('assets/front/img/breadcrumb1.jpg')}}" alt=""/>
            {{--<div class="col-md-12">
                <div class="panel panel-inverse" data-sortable-id="ui-buttons-3">
                    <div class="panel-heading">
                        <h4 class="panel-title">MAKE TRANSACTION</h4>
                    </div>
                    <div class="panel-body">
                        <button type="button" class="btn btn-inverse btn-lg" data-toggle="modal" data-target="#sendmoney"><i class="fa fa-upload" aria-hidden="true"></i> Send </button>
                        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#request" id="request-button"><i class="fa fa-download" aria-hidden="true"></i> Request </button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-inverse" data-sortable-id="ui-buttons-3">
                    <div class="panel-heading">
                        <h4 class="panel-title">YOUR ACCOUNT BALANCE</h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-responsive">
                            <tr>
                                <td>My {{$gset->curCode}} Wallet</td>
                                <td>
                                    {{number_format(floatval(Auth::user()->balance) ,  $gset->decimalPoint, '.', '')}} <br/>  $ {{number_format(floatval($nusd) ,  $gset->decimalPoint, '.', '')}}
                                </td>
                            </tr>
                            <tr>
                                <td>My BitCoin Wallet</td>
                                <td>
                                    {{number_format(floatval(Auth::user()->bitcoin) ,  $gset->decimalPoint, '.', '')}} <br/> $ {{number_format(floatval($btusd) ,  $gset->decimalPoint, '.', '')}}
                                </td>
                            </tr>
                            <tr class="text-center">
                                <td colspan="2">$ <b>{{number_format(floatval($totusd) , $gset->decimalPoint, '.', '')}}</b></td>
                            </tr>
                        </table>
                    </div>
                </div>--}}
            <div class="col-md-12">
                <div class="col-md-12 table-responsive">
                    <table class="table">
                        <tbody id="game_body">
                        <tr>
                            <td class="text-center">
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-1.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-2.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-3.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-4.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-5.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-6.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-7.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-8.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-9.png') }}"></div>
                            </td>
                            <td class="text-center">
                                <div class="stats-icon"> <img class="ball" src="{{ asset('assets/front/row-1-col-10.png') }}"></div>
                            </td>
                            <td class="text-center font-bold font-black" style="vertical-align: inherit;">
                                TOTAL
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center font-bold font-black">TOTAL</td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center font-bold font-black">OTHERS</td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center font-bold font-black">WIN %</td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center font-bold font-black">WAGER</td>
                            <td class="text-center font-black">
                                <select id="wager1">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager2">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager3">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager4">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager5">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager6">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager7">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager8">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager9">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black">
                                <select id="wager10">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2500">2500</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </td>
                            <td class="text-center font-black" style="vertical-align: inherit;">
                                0
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center font-bold font-black">WIN</td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                                0
                            </td>
                            <td class="text-center font-black">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 text-center">
                    <h1 id="game_status"></h1>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <button type="button" class="close" aria-label="Close" style="position: absolute; right: 14px; top: 58px; cursor: pointer;">
            <span class="danger" aria-hidden="true" style="font-size: 47px;color: red;">&times;</span>
        </button>
    </div>
    {{--<div class="col-md-8">
        <div class="panel panel-inverse" data-sortable-id="index-1">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                </div>
                <h4 class="panel-title">Rate Analytics</h4>
            </div>
            <div class="panel-body">
                <div class="col-md-12" id="chartdiv"></div>
            </div>
        </div>
    </div>--}}

    <!--Send Modal-->
    <div id="sendmoney" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Send</h4>
                </div>
                <div class="modal-body">
                    <form action="{{route('send.money')}}" method="POST">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label>Currency </label>
                            <select class="form-control" id="myselect" name="curn">
                                <option id="ncoin" value="1" selected>{{$gset->curCode}}</option>
                                <option id="bitcoin" value="2">BitCoin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>To</label>
                            <input type="text" name="code" class="form-control input-sz" placeholder="Enter Wallet Address" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <table>
                                <tr>
                                    <td> <div class="input-group">
                                            <input type="text" name="amount" class="form-control" id="amount" required>
                                            <span class="input-group-addon" id="btc"  style="display: none;">BitCoin</span>
                                            <span class="input-group-addon" id="ncc">
                                                {{$gset->curCode}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fa fa-arrows-h" aria-hidden="true"></i>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="" id="usd" class="form-control">
                                            <span class="input-group-addon">USD</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="desc">
                            </textarea>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-lg btn-block">
                                Send Money
                            </button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>

    <!-- Request Modal -->
    <div id="request" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Request</h4>
                </div>
                <div class="modal-body text-center">
                    <p>Copy and share this code to Request Money</p>
                    <p id="qrcode" style="color:#3366cc; font-size: 20px;"></p>
                    <div class="form-group">
                        <div class="input-group">
                            <input id="code" class="form-control input-lg">
                            <span class="btn btn-success input-group-addon" id="copybtn">Copy</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Game Modal -->
    <div id="game_result" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h1 class="modal-title">Congratulations!</h1>
                </div>
                <div class="modal-body text-center">
                    <h2 class="modal-title">Winner</h2>
                    <td class="text-center">
                        <div class="stats-icon"> <img id="result_img" class="ball" src="{{ asset('assets/front/row-1-col-1.png') }}"></div>
                    </td>
                    <td class="text-center">
                        <h3 id="earn_text"></h3>
                    </td>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!--Copy Data -->
    <script type="text/javascript">
        document.getElementById("copybtn").onclick = function()
        {
            document.getElementById('code').select();
            document.execCommand('copy');
        }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Ajax Get Data -->
    <script type="text/javascript">
        $(document).ready(function(){

            $(document).on('click','#request-button',function(){
                $.ajax({
                    type:'get',
                    url:'{{ route('money.requests') }}',
                    data:'',
                    success:function(data){
                        $('#qrcode').html(data.code);
                        $('#code').val(data.accnum);
                    }
                });
            });

            function showGameResult(game) {
                const result = JSON.parse(game.result);
                const bets = JSON.parse(game.data);
                if(result['wining_number']) {
                    const path = '{{asset('assets/front/')}}';
                    const img = path + '/row-1-col-' + result['wining_number'] +  '.png';
                    $("#result_img").attr('src', (img));
                    let totalList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    let wagerList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    let winList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    let total = 0;
                    let wager = 0;
                    for(let index = 0 ; index < bets.length ; index ++) {
                        let bet = bets[index];
                        totalList[bet.number - 1] += parseInt(bet.value);
                        total += parseInt(bet.value);
                        const id = parseInt('{{$currentUser->id}}');
                        if(id === parseInt(bet.id)) {
                            wagerList[bet.number - 1] = parseInt(bet.value);
                            wager += parseInt(bet.value);
                        }
                    }
                    if(wager === 0){
                        return;
                    }
                    for (let index = 0; index < 10; index++) {
                        winList[index] = ((wagerList[index] / totalList[index]) * total * 0.9) | 0;
                    }
                    const text = 'You got ' + winList[result['wining_number'] - 1] + ' ' + $('#game_container').attr('data-unit');
                    $('#earn_text').text(text);
                    $("#game_result").modal('show');
                }
            }

            function updateGameData(bets, status, startedAt) {
                const table = $('#game_body').find("tr");
                let totalList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                let othersList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                let winPercentList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                let wagerList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                let winList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                let total = 0;
                let others = 0;
                let wager = 0;
                let win = 0;
                for(let index = 0 ; index < bets.length ; index ++) {
                    let bet = bets[index];
                    totalList[bet.number - 1] += parseInt(bet.value);
                    total += parseInt(bet.value);
                    const id = parseInt('{{$currentUser->id}}');
                    if(id !== parseInt(bet.id)) {
                        othersList[bet.number - 1] += parseInt(bet.value);
                        others += parseInt(bet.value);
                    } else {
                        wagerList[bet.number - 1] = parseInt(bet.value);
                        wager += parseInt(bet.value);
                    }
                }
                for (let index = 0; index < 10; index++) {
                    if(total === 0) {
                        winPercentList[index] = 0;
                    } else {
                        winPercentList[index] = (totalList[index] / total * 100) === 0 ? (totalList[index] / total * 100) : (totalList[index] / total * 100).toFixed(2);
                    }
                    winList[index] = ((wagerList[index] / totalList[index]) * total * 0.9) | 0;
                }
                let differences = startedAt === null ? 0 : Date.now() - (new Date(startedAt).getTime() - (new Date(startedAt).getTimezoneOffset() * 60000));
                differences = differences / 1000;
                differences = differences | 0;
                status === 'started' ? (differences < 60 ? $('#game_status').text('DRAWING IN ' + (60 - differences) + ' SECONDS...') : $('#game_status').text('WAIT FOR NEW GAME TO BE STARTED...') ) : $('#game_status').text('WAITING FOR OTHER TO JOIN...');
                if(status === 'started' && differences < 60) {
                    if(differences > 56) {
                        $('#game_status').css("color", "red");
                    } else {
                        $('#game_status').css("color", "black");
                    }
                } else {
                    $('#game_status').css("color", "black");
                }
                let i = 0;
                table.each(function (row) {
                    let j = 0;
                    $(this).find("td").each(function (cell) {
                        if(j > 0) {
                            switch (i) {
                                case 1:
                                    if(j === 11) {
                                        $(this).text(total);
                                    } else {
                                        $(this).text(totalList[j-1]);
                                    }
                                    break;
                                case 2:
                                    if(j === 11) {
                                        $(this).text(others);
                                    } else {
                                        $(this).text(othersList[j-1]);
                                    }
                                    break;
                                case 3:
                                    if(j === 11) {
                                        $(this).text(100);
                                    } else {
                                        $(this).text(winPercentList[j-1]);
                                    }
                                    break;
                                case 4:
                                    if(j === 11) {
                                        $(this).text(wager);
                                    } else {
                                        $(this).find("select").val(wagerList[j-1]);
                                        if(status === 'started' && differences < 60) {
                                            if(differences > 56) {
                                                $(this).find("select").prop("disabled", true);
                                            } else {
                                                $(this).find("select").prop('disabled', false);
                                            }
                                        } else {
                                            $(this).find("select").prop('disabled', false);
                                        }
                                    }
                                    break;
                                case 5:
                                    if(j === 11) {
                                    } else {
                                        $(this).text(winList[j-1]);
                                    }
                                    break;
                            }
                        }
                        j++;
                    });
                    i++;
                });
            }

            var socket = new WebSocket("ws://192.168.5.15:8080/gameSocket");

            connectToSocket();

            function connectToSocket(){
                socket.onopen = function() {
                    console.log("The connection is established.");
                    initWager();
                    setInterval(function () {
                        const unit = $('#game_container').attr('data-unit');
                        if(unit) {
                            $.ajax({
                                type:'get',
                                url:'{{ route('game.currentGame') }}',
                                data:{'coin': unit},
                                success:function(data){
                                    if(data) {
                                        const bets = data.data;
                                        if(data.status !== 'ended') {
                                            const coin = $('#game_container').attr('data-unit');
                                            if(data.coin !== coin) {
                                                return;
                                            }
                                            updateGameData(bets? JSON.parse(bets): [], data.status, data.started_at);
                                        }
                                    } else {
                                        updateGameData([], 'pending', null);
                                    }
                                }
                            });
                        }
                    }, 1000);
                };
                socket.onclose = function(event) {
                    if (event.wasClean) {
                        console.log('Connection closed cleanly');
                    } else {
                        console.log('Broken connections');
                    }
                    console.log('Key: ' + event.code + ' cause: ' + event.reason);
                };
                socket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    const bets = JSON.parse(data.data);
                    const coin = $('#game_container').attr('data-unit');
                    if(data.coin !== coin) {
                        return;
                    }
                    updateGameData(bets, data.status, data.started_at);
                    if(data.status === 'ended') {
                        showGameResult(data);
                    }
                };
                socket.onerror = function(error) {
                    console.log("Error " + error.message);
                };
            }

            function initWager() {
                $('#wager1').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 1,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager2').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 2,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager3').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 3,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager4').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 4,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager5').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 5,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager6').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 6,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager7').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 7,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager8').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 8,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager9').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 9,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
                $('#wager10').change(function () {
                    const data = $(this).val();
                    const id = parseInt('{{$currentUser->id}}');
                    const name = '{{$currentUser->username}}';
                    socket.send( JSON.stringify({
                        id: id,
                        name: name,
                        number: 10,
                        value: data,
                        unit: $('#game_container').attr('data-unit')
                    }) );
                });
            }

        });

    </script>

    <!--Currrency change Valu -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#myselect").change(function(){
                const a = $( "#myselect option:selected" ).val();
                if(a === '1') {
                    $("#ncc").show();
                    $("#btc").hide();
                    const data = $("#amount").val();
                    const rate = parseFloat({{$price->price}});
                    const total = data*rate;
                    $("#usd").val(total);
                } else {
                    $("#ncc").hide();
                    $("#btc").show();
                    const data = $("#amount").val();
                    const rate = parseFloat({{$currentRate}});
                    const total = data*rate;
                    $("#usd").val(total);
                }
            });
            $("#amount").keypress(function(){
                const a = $( "#myselect option:selected" ).val();
                if(a === '1') {
                    $("#ncc").show();
                    $("#btc").hide();
                    $("#amount").keyup(function(){
                        const data = $("#amount").val();
                        const rate = parseFloat({{$price->price}});
                        const total = data * rate;
                        $("#usd").val(total);
                    });

                    $("#usd").keyup(function(){
                        const data = $("#usd").val();
                        const rate = parseFloat({{$price->price}});
                        const total = data/rate;
                        $("#amount").val(total);
                    });
                } else {
                    $("#ncc").hide();
                    $("#btc").show();
                    $("#amount").keyup(function(){
                        const data = $("#amount").val();
                        const rate = parseFloat({{$currentRate}});
                        const total = data*rate;
                        $("#usd").val(total);
                    });
                    $("#usd").keyup(function(){
                        const data = $("#usd").val();
                        const rate = parseFloat({{$currentRate}});
                        const total = data/rate;
                        $("#amount").val(total);
                    });
                }
            });
            $("#usd").keypress(function(){
                const a = $( "#myselect option:selected" ).val();
                if(a === '1') {
                    $("#ncc").show();
                    $("#btc").hide();
                    $("#amount").keyup(function(){
                        const data = $("#amount").val();
                        const rate = parseFloat({{$price->price}});
                        const total = data*rate;
                        $("#usd").val(total);
                    });

                    $("#usd").keyup(function(){
                        const data = $("#usd").val();
                        const rate = parseFloat({{$price->price}});
                        const total = data/rate;
                        $("#amount").val(total);
                    });
                } else {
                    $("#ncc").hide();
                    $("#btc").show();
                    $("#amount").keyup(function(){
                        const data = $("#amount").val();
                        const rate = parseFloat({{$currentRate}});
                        const total = data*rate;
                        $("#usd").val(total);
                    });
                    $("#usd").keyup(function(){
                        const data = $("#usd").val();
                        const rate = parseFloat({{$currentRate}});
                        const total = data/rate;
                        $("#amount").val(total);
                    });
                }
            });
            $("#play_btc").click(function () {
                hideClickButtons();
                $('#game_container').attr('data-unit', 'btc');
            });
            $("#play_dmc").click(function () {
                hideClickButtons();
                $('#game_container').attr('data-unit', 'dmc');
            });
            $("#play_oro").click(function () {
                hideClickButtons();
                $('#game_container').attr('data-unit', 'oro');
            });
            $("#play_free").click(function () {
                hideClickButtons();
                $('#game_container').attr('data-unit', 'free');
            });
            $(".close").click(function () {
                $("#play_btc").show();
                $("#play_free").show();
                $("#play_dmc").show();
                $("#play_oro").show();
                $("#game_container").hide();
            });

            function hideClickButtons() {
                $("#play_btc").hide();
                $("#play_free").hide();
                $("#play_dmc").hide();
                $("#play_oro").hide();
                $("#game_container").show();
            }
        });
    </script>
@endsection

