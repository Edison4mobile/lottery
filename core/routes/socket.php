<?php
/**
 * Created by PhpStorm.
 * User: ediso
 * Date: 3/2/2019
 * Time: 12:10 PM
 */
$socket->route('/gameSocket', new \App\Http\Sockets\GameSocket(), ['*']);