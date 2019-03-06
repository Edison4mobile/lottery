<?php

namespace App\Http\Sockets;

use App\Balance;
use App\Fee;
use App\Game;
use Orchid\Socket\BaseSocketListener;
use Ratchet\ConnectionInterface;

class Timeout
{
    private $active;
    public static $isWorking = false;
    private $from, $data, $game, $socket;
    public function set($seconds, $from, $data, $game, $socket)
    {
        if(self::$isWorking) {
            return;
        }
        $this->from = $from;
        $this->data = $data;
        $this->game = $game;
        $this->socket = $socket;
        $this->active = true;
        self::$isWorking = true;
        declare(ticks = 1);
        pcntl_signal(SIGALRM, array($this, 'handle'), true);
        pcntl_alarm($seconds);
    }

    public function clear()
    {
        $this->active = false;
    }

    private function distributeCoin($game, $win) {
        $data = json_decode($game->data);
        $users = [];
        $bets = array();
        $total = 0;
        $wagerTotal = 0;
        foreach ($data as $index => $item) {
            $total += $item->value;
            if($item->number === $win) {
                $wagerTotal += $item->value;
                if($bets[$item->id]) {
                    $bets[$item->id] += $item->value;
                } else {
                    $bets[$item->id] = $item->value;
                }
                if(sizeof($users) == 0 || ! in_array($item->id, $users)) {
                    array_push($users, $item->id);
                }
            }
        }
        echo print_r($users, true);
        echo print_r($bets, true);
        $fee = Fee::orderBy('id', 'desc')->first();
        if(!$fee) {
            $fee = Fee::create([
                'btc_balance' => 0,
                'dmc_balance' => 0,
                'oro_balance' => 0,
                'free_balance' => 0,
                'created_at' => date("Y-m-d h:i:s"),
                'update_at' => date("Y-m-d h:i:s")
            ]);
        }
        foreach ($users as $index => $user) {
            $balance = Balance::where('user_id', $user)->first();
            if($balance) {
                switch ($game->coin) {
                    case 'btc':
                        $balance['btc_balance'] = $balance['btc_balance'] +  $bets[$user] / $wagerTotal * $total * 0.9 - $bets[$user];
                        $fee['btc_balance'] += $fee['btc_balance'] +  $bets[$user] / $wagerTotal * $total * 0.1;
                        break;
                    case 'dmc':
                        $balance['dmc_balance'] = $balance['dmc_balance'] +  $bets[$user] / $wagerTotal * $total * 0.9 - $bets[$user];
                        $fee['dmc_balance'] += $fee['dmc_balance'] +  $bets[$user] / $wagerTotal * $total * 0.1;
                        break;
                    case 'oro':
                        $balance['oro_balance'] = $balance['oro_balance'] +  $bets[$user] / $wagerTotal * $total * 0.9 - $bets[$user];
                        $fee['oro_balance'] += $fee['oro_balance'] +  $bets[$user] / $wagerTotal * $total * 0.1;
                        break;
                    case 'free':
                        $balance['free_balance'] = $balance['free_balance'] +  $bets[$user] / $wagerTotal * $total * 0.9 - $bets[$user];
                        $fee['free_balance'] += $fee['free_balance'] +  $bets[$user] / $wagerTotal * $total * 0.1;
                        break;
                }
                $balance->save();
            }
        }
        $fee->save();
    }

    public function handle($signal)
    {
        echo $signal;
        if ($this->active) {
            $this->game = Game::where('status', 'started')->orderBy('created_at', 'asc')->first();
            $this->data = json_decode($this->game->data);
            $users = array();
            $totalList = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $total = 0;
            $result = array();
            foreach ($this->data as $index => $item) {
                if((sizeof($users) == 0 || ! in_array($item->id, $users)) && $item->value > 0) {
                    array_push($users, $item->id);
                }
                $totalList[$item->number - 1] += $item->value;
                $total += $item->value;
            }
            if($total > 0) {
                $wining_number = 0;
                while ($wining_number === 0) {
                    $lower_limit = 0;
                    $upper_limit = 0;
                    $rng = rand(1, 10000000 - 1) / 10000000;
                    echo $rng . "\n";
                    for ($i = 0 ; $i < 10; $i ++) {
                        $upper_limit += $totalList[$i] / $total;
                        if($rng > $lower_limit && $rng < $upper_limit) {
                            $wining_number = $i + 1;
                        }
                        $lower_limit += $upper_limit;
                    }
                }
                $result['wining_number'] = $wining_number;
            }
            $this->game = Game::where('id', $this->game['id'])->first();
            $this->game['result'] = json_encode($result);
            $this->game['status'] = 'ended';
            $this->game['ended_at'] = date("Y-m-d H:i:s");
            $this->game->save();

            foreach ($this->socket->clients as $client) {
                $client->send($this->game);
            }
            $endedGame = $this->game;
            $win = $result['wining_number'];
            $data = array();
            $result = array();
            Game::create(
                ['data' => json_encode($data), 'status' => "pending", 'coin' => $this->game['coin'], 'result' => json_encode($result), 'started_at' => date("Y-m-d H:i:s"), 'ended_at' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d h:i:s"), 'update_at' => date("Y-m-d h:i:s")]
            );
            $this->distributeCoin($endedGame, $win);
            $game = $this->game = Game::where('status', 'started')->orderBy('created_at', 'asc')->first();
            if(!$game) {
                self::$isWorking = false;
                return;
            } else {
                $diff = strtotime(date("Y-m-d H:i:s")) - strtotime($game['started_at']);
                echo date("Y-m-d H:i:s");
                echo "\n";
                echo $game['started_at'];
                echo "\n";
                echo $diff;
                if($diff < 60) {
                    declare(ticks = 1);
                    pcntl_signal(SIGALRM, array($this, 'handle'), true);
                    pcntl_alarm(60 - $diff);
                } else {
                    declare(ticks = 1);
                    pcntl_signal(SIGALRM, array($this, 'handle'), true);
                    pcntl_alarm(3);
                }
            }
        }
    }
}

class GameSocket extends BaseSocketListener
{
    /**
     * Current clients.
     *
     * @var \SplObjectStorage
     */
    public $clients;

    /**
     * GameSocket constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $msg = json_decode($msg);
        $game = Game::where('coin', $msg->unit)->orderBy('created_at', 'desc')->first();
        $users = array();
        if( !$game ) {
            $data = array();
            $result = array();
            Game::create(
                ['data' => json_encode($data), 'status' => "pending", 'coin' => $msg->unit, 'result' => json_encode($result), 'started_at' => date("Y-m-d H:i:s"), 'ended_at' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d h:i:s"), 'update_at' => date("Y-m-d h:i:s")]
            );
            $game = Game::where('coin', $msg->unit)->orderBy('created_at', 'desc')->first();
        } else if ($game['status'] === 'ended') {
            $data = array();
            $result = array();
            Game::create(
                ['data' => json_encode($data), 'status' => "pending", 'coin' => $msg->unit, 'result' => json_encode($result), 'started_at' => date("Y-m-d H:i:s"), 'ended_at' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d h:i:s"), 'update_at' => date("Y-m-d h:i:s")]
            );
            $game = Game::where('coin', $msg->unit)->orderBy('created_at', 'desc')->first();
        }
        $data = json_decode($game->data);
        $exist = false;
        foreach ($data as $index => $item) {
            if(sizeof($users) == 0 || ! in_array($item->id, $users)) {
                if(sizeof($users) == 10) {
                    return;
                }
                array_push($users, $item->id);
            }
            if ($item->id === $msg->id && $item->number === $msg->number) {
                array_push($users, $item->id);
                $item->value = $msg->value;
                $data[$index] = $item;
                $exist = true;
            }
        }
        if(!$exist) {
            $data[] = $msg;
        }
        $game['data'] = json_encode($data);
        if(sizeof($users) > 0 && !in_array($msg->id, $users) && $game['status'] === "pending") {
            $game['status'] = 'started';
            $game['started_at'] = date("Y-m-d H:i:s");
            $game->save();
            foreach ($this->clients as $client) {
                if ($from != $client) {
                }
                $client->send($game);
            }
            $this->waitGame($from, $data, $game);
        } else {
            $game->save();
        }
        foreach ($this->clients as $client) {
            if ($from != $client) {
            }
            $client->send($game);
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception          $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    private function waitGame($from, $data, $game) {
        if(!Timeout::$isWorking) {
            $timeout = new Timeout();
            $timeout->set(60, $from, $data, $game, $this);
        }
    }

}