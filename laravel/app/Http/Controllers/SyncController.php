<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Woocommerce;

class SyncController extends Controller
{
    //
    public function index(){
        $result = Woocommerce::get('');
        var_dump($result);
    }
    public function getProducts(){
        $data = [
        ];
        $result = Woocommerce::get('products', $data);
        print_r($result);
    }
    public function getOrders(){
        $data = [
            'status' => 'completed',
            'filter' => [
                'created_at_min' => '2020-01-01'
            ]
        ];
        $result = Woocommerce::get('orders', $data);
        print_r($result);
    }
}
