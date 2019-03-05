@extends('front.layouts.admaster')
@section('content')

    <div class="panel panel-inverse" data-sortable-id="ui-buttons-3">
        <div class="panel-heading">
            <h4 class="panel-title">YOUR ACCOUNT BALANCE</h4>
        </div>
        <div class="panel-body">
            <div class="row text-center">
                <div class="col-md-6">

                    <table class="table table-responsive">
                        <tr>
                            <td>BTC</td>
                            <td>
                                {{$balances['btc_balance']}}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-block" id="btn-btc">Deposit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>ORO</td>
                            <td>
                                {{$balances['oro_balance']}}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-block" id="btn-oro">Deposit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>DMC</td>
                            <td>
                                {{$balances['dmc_balance']}}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-block" id="btn-dmc">Deposit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Free Balance</td>
                            <td>
                                {{$balances['free_balance']}}
                            </td>
                            <td>

                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <div id="depositbox-btc">
                        <h3>Bitcoin Deposit</h3>
                        <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{$balances['btc_address']}}&choe=UTF-8">
                        <br>
                        <div style="width:300px; margin:auto;">
                            <input type="text" value="{{$balances['btc_address']}}" id = "address_btc"
                                   style="width:80%;padding: 4px 10px;font-size: 12px;line-height: 1.5; vertical-align: bottom">
                            <button class="btn btn-sm btn-primary btn-block" id = "copy_btc" style="width:15%; display: inline-block">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div id="depositbox-oro">
                        <h3>ORO Deposit</h3>
                        <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{$balances['oro_address']}}&choe=UTF-8">
                        <br>
                        <div style="min-width:400px; margin:auto;">
                            <input type="text" value="{{$balances['oro_address']}}" id = "address_oro"
                                   style="width:80%;padding: 4px 10px;font-size: 12px;line-height: 1.5; vertical-align: bottom">
                            <button class="btn btn-sm btn-primary btn-block" id = "copy_oro" style="width:15%; display: inline-block">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div id="depositbox-dmc">
                        <h3>DMC Deposit</h3>
                        <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{$balances['dmc_address']}}&choe=UTF-8">
                        <br>
                        <div style="width:300px; margin:auto;">
                            <input type="text" value="{{$balances['dmc_address']}}" id = "address_dmc"
                                   style="width:80%;padding: 4px 10px;font-size: 12px;line-height: 1.5; vertical-align: bottom">
                            <button class="btn btn-sm btn-primary btn-block" id = "copy_dmc" style="width:15%; display: inline-block">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        $(document).ready(function(){
            $("#depositbox-btc").show();
            $("#depositbox-oro").hide();
            $("#depositbox-dmc").hide();

            $("#btn-btc").on("click",function(){
                $("#depositbox-btc").show();
                $("#depositbox-oro").hide();
                $("#depositbox-dmc").hide();
            });

            $("#btn-oro").on("click",function(){
                $("#depositbox-btc").hide();
                $("#depositbox-oro").show();
                $("#depositbox-dmc").hide();
            });
            $("#btn-dmc").on("click",function(){
                $("#depositbox-btc").hide();
                $("#depositbox-oro").hide();
                $("#depositbox-dmc").show();
            });

            $("#copy_btc").on("click", function(){
                document.getElementById('address_btc').select();
                document.execCommand('copy');
            });
            $("#copy_oro").on("click", function(){
                document.getElementById('address_oro').select();
                document.execCommand('copy');
            });
            $("#copy_dmc").on("click", function(){
                document.getElementById('address_dmc').select();
                document.execCommand('copy');
            });
        });

    </script>
@endsection



