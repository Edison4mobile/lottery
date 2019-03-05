<?php

namespace App\Http\Sockets;

use App\Game;
use Orchid\Socket\BaseSocketListener;
use Ratchet\ConnectionInterface;

class TimeoutException extends \RuntimeException {}
class Timeout
{
    private $active;
    private $from, $data, $game, $socket;
    public function set($seconds, $from, $data, $game, $socket)
    {
        $this->from = $from;
        $this->data = $data;
        $this->game = $game;
        $this->socket = $socket;
        $this->active = true;
        declare(ticks = 1);
        pcntl_signal(SIGALRM, array($this, 'handle'), true);
        pcntl_signal(SIGALRM, array($this, 'handle1'), true);
        pcntl_alarm($seconds);
    }

    public function clear()
    {
        $this->active = false;
    }

    public function handle1($signal) {
        echo 'handle1';
    }

    public function handle($signal)
    {
        echo "received signal\n";
        if ($this->active) {
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
                if ($this->from != $client) {
                }
                $client->send($this->game);
            }
            $data = array();
            $result = array();
            Game::create(
                ['data' => json_encode($data), 'status' => "pending", 'coin' => $this->game['coin'], 'result' => json_encode($result), 'started_at' => date("Y-m-d H:i:s"), 'ended_at' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d h:i:s"), 'update_at' => date("Y-m-d h:i:s")]
            );
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
        $timeout = new Timeout();
        try {
            $timeout->set(60, $from, $data, $game, $this);
        } catch(TimeoutException $e) {
            echo "caught a TimeoutException\n";
        }
    }

}