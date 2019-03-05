<?php
require_once "Bitcoin.php";

define("BITCOIND_HOST", "");
define("BITCOIND_PORT", "");
define("BITCOIND_USERNAME", "");
define("BITCOIND_PASSWORD", "");

define("ORO_HOST", "163.172.174.26");
define("ORO_PORT", "41002");
define("ORO_USERNAME", "asuwuvdnejqwvmq");
define("ORO_PASSWORD", "nqpbtqnkbnhnbgxupethrlvrfudurja");

define("DMC_HOST", "");
define("DMC_PORT", "");
define("DMC_USERNAME", "");
define("DMC_PASSWORD", "");

echo date('Y-m-d H:i:s').' Beginning Crypto Deposits processing...'.PHP_EOL;

$db_host		= 'localhost';
$db_user		= 'skyrus';
$db_pass		= 'x112233x';
$db_database	= 'lottery_db';
$db = new PDO('mysql:host='.$db_host.';dbname='.$db_database, $db_user, $db_pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$transactions_dir = '../core/transactions/btc/';
$transactions = scandir($transactions_dir);
if ($transactions) {
    deposit($db, $transactions, 'btc', $transactions_dir);
}

$transactions_dir = '../core/transactions/oro/';
$transactions = scandir($transactions_dir);
if ($transactions) {
    deposit($db, $transactions, 'oro', $transactions_dir);
}

$transactions_dir = '../core/transactions/dmc/';
$transactions = scandir($transactions_dir);
if ($transactions) {
    deposit($db, $transactions, 'dmc', $transactions_dir);
}

function deposit($db, $transactions, $symbol, $transactions_dir){

    switch ($symbol){
        case 'btc':
            $coin = new Bitcoin(BTC_USERNAME,BTC_PASSWORD,BTC_HOST,BTC_PORT);
            $gateway_id = 10;
            break;
        case 'oro':
            $coin = new Bitcoin(ORO_USERNAME,ORO_PASSWORD,ORO_HOST,ORO_PORT);
            $gateway_id = 20;
            break;
        case 'dmc':
            $coin = new Bitcoin(DMC_USERNAME,DMC_PASSWORD,DMC_HOST,DMC_PORT);
            $gateway_id = 30;
            break;
        default:
            break;
    }

    foreach ($transactions as $t_id) {
        if (!$t_id || $t_id == '.' || $t_id == '..' || $t_id == '.gitignore')
            continue;

        $transaction = $coin->gettransaction($t_id);
        /*if (!empty($transaction_log[$t_id])) {
            unlink($transactions_dir.$t_id);
            continue;
        }*/
        if (empty($transaction['details']))
            continue;

        foreach ($transaction['details'] as $detail) {
            if ($detail['category'] == 'receive') {
                $user_id = $detail['account']==""?0:$detail['account'];

                // if not confirmed enough
                if ($transaction['confirmations'] < 3) {

                }
                else {
                    // if confirmation sufficient
                    $sql = "INSERT INTO deposits(gateway_id, user_id, trxid, amount, charge, 
                                inusd, details, status, created_at, updated_at)
                                VALUES (:gateway_id, :user_id, :trxid, :amount, :charge, 
                                :inusd, :details, :status, :created_at, :updated_at)";
                    $tmp_arr = array(
                        ":gateway_id" => $gateway_id,  //btc:10 oro:20 dmc:30
                        ":user_id" => $user_id,
                        ":trxid" => $t_id,
                        ":amount" => $detail['amount'],
                        ":charge" => 0,
                        ":inusd" => 0,
                        ":details" => $symbol." deposit success.",
                        ":status" => 1,
                        ":created_at" => date('Y-m-d H:i:s'),
                        ":updated_at" => date('Y-m-d H:i:s')
                    );

                    $result = $db->prepare($sql);
                    $updated = $result->execute($tmp_arr);
                    //$updated=1;

                    if ($updated > 0) {
                        $unlink = unlink($transactions_dir.$t_id);

                        if (!$unlink && file_exists($unlink)) {
                            $unlink = unlink($transactions_dir.$t_id);
                        }

                        $sql = "SELECT * FROM balances WHERE user_id=".$user_id;
                        $result = $db->prepare($sql);
                        $result->execute();
                        $row = $result->fetch();

                        if($result->rowCount()>0){
                            $balance = $row[$symbol.'_balance'];
                            $new_balance = $detail['amount'] + $balance;
                            $sql="UPDATE balances SET ".$symbol."_balance=".$new_balance." WHERE user_id=".$user_id;
                            $result = $db->prepare($sql);
                            $result->execute();
                        }

                        if (!$unlink)
                            echo 'Error: Could not delete transaction file.'.PHP_EOL;
                        else
                            echo 'transaction credited successfully.'.PHP_EOL;
                    }
                }
            }
            elseif ($detail['category'] == 'send') {

            }
        }
    }
}



