<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Woocommerce;
use App\Sync;

class SyncController extends Controller
{
    //
    const IVA = 1.19;
    private $accessToken;
    private $endPoint;
    public function __construct(
    )
    {
      $this->endPoint = env('LAUDUS_ENDPOINT') != '' ? env('LAUDUS_ENDPOINT') : 'https://erp.laudus.cl/api/';
      $this->user = env('LAUDUS_USER') != '' ? env('LAUDUS_USER') : '';
      $this->password = env('LAUDUS_PASSWORD') != '' ? env('LAUDUS_PASSWORD') : '';
      $this->companyVatId = env('LAUDUS_COMPANY_VAT_ID') != '' ? env('LAUDUS_COMPANY_VAT_ID') : '';
      $this->wharehouseId = env('LAUDUS_WAREHOUSE_ID') != '' ? env('LAUDUS_WAREHOUSE_ID') : '';
      $this->laudusToken = null;
    }
    public function index(){
        $result = Woocommerce::get('');
        var_dump($result);
    }
    private function laudusConnection ($function=null, $method='GET', $data=array())
    {
        $response = null;
        if ($function!=null) {
            $url = $this->endPoint.$function;
            $session = curl_init($url);
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json'
            );
            if ($this->laudusToken != null){
                $headers[] = 'token: ' . $this->laudusToken;
            }
            if ($method=='GET' && count($data)>0){
                $url.='?'.http_build_query($data);
            }

            $config = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
            );
            if (($method=='POST' || $method=='PUT') && count($data)>0){
                $config[CURLOPT_POSTFIELDS] = json_encode($data);
            }
            curl_setopt_array($session, $config);
            $response = curl_exec($session);
            $err = curl_error($session);
            $code = curl_getinfo($session, CURLINFO_HTTP_CODE);
            curl_close($session);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $response = json_decode($response);
            }
        }
        return $response;
    }
    private function laudusLogin() 
    {
        $method = 'users/login';
        $credentials = [
            'user' => $this->user,
            'password' => $this->password,
            'companyVatId' => $this->companyVatId
        ];
        $response = $this->laudusConnection($method, 'POST', $credentials);
        // var_dump($response);
        $this->laudusToken = $response->token;
    }
    private function laudusStockByProductId ($productId) {
        $this->laudusLogin();
        $method = 'products/get/stock/'.$productId;
        $data = [
            'warehouseId' => $this->wharehouseId
        ];
        print_r($data);
        $response = $this->laudusConnection($method, 'GET', $data);
        return $response;
    }
    private function getWooCProductBySKU($sku){
        $params = [
            'sku' => $sku
        ];
        return Woocommerce::get('products', $params);
    }
    public function syncLaudusProducts() {
        $this->laudusLogin();
        $method = 'products/get/list/complete';
        $response = $this->laudusConnection($method, 'GET');
        $session = date('YmdHis');
        if ($response != null) {
            foreach ($response as $product) {
                $sync = Sync::BySku($product->code)->first();
                if (!$sync) {
                    $sync = new Sync();
                    $sync->status = 1;
                    $sync->sku = $product->code;
                }
                $isUpdate = false;
                if ($product->unitPrice != $sync->netPrice) {
                    $isUpdate = true;
                    $sync->netPrice = $product->unitPrice;
                }
                if ($isUpdate) {
                    $sync->status = 1;
                }
                $sync->session = $session;
                $sync->save();
            }
        }
    }
    public function syncLaudusStock() {
        $this->laudusLogin();
        $method = 'products/get/list/stock';
        $data = [
            'warehouseId' => $this->wharehouseId
        ];
        $response = $this->laudusConnection($method, 'GET', $data);
        if ($reponse != null) {
            foreach ($response as $product) {
                $sync = Sync::BySku($product->code)->first();
                if ($sync && $sync->count() > 0) {
                    if ($product->stock != $sync->availableStock) {
                        $isUpdate = true;
                        $sync->stockAvailable = $product->stock;
                        $sync->status = 1;
                        $sync->save();
                    }
                }
            }
        }
    }

    public function syncWCProducts() {
        $take = 10;
        $syncs = Sync::Pending()->paginate($take);
        if ($syncs->count() > 0) {
            foreach ($syncs as $sync) {
                $WCProduct = $this->getWooCProductBySKU($sync->sku);
                if (count($WCProduct) > 0) {
                    foreach ($WCProduct as $item) {
                        $fields = [
                            'price' => (string)(round($sync->unitPrice * 1.19)),
                            'regular_price' => (string)(round($sync->unitPrice * 1.19)),
                            'stock_quantity' => $sync->stockAvailable > 0 ? (string)($sync->stockAvailable) : '0'
                        ];
                        $this->updateWooCProduct($item['id'], $fields);
                        $sync->status = 2;
                        $sync->save();
                    }
                }
            }
        }
    }
    private function updateWooCProduct($productId, $fields) {
        echo 'products/'.$productId.'<br>';
        $response = Woocommerce::put('products/'.$productId, $fields);
        print_r($response);
    }
    public function syncStock() {
        $products = $this->laudusStock();
        dd($products);
        /* foreach ($products as $product) {
            
            if ($product->code == 'AIR3473') {
                echo 'Capturando codigo='.$product->code.' precio='.$product->unitPrice.'<br>';
                $WCProduct = $this->getWooCProductBySKU($product->code);
                dd($WCProduct);
            }
        }*/
    }
    public function getProducts(){
        $this->laudusProducts();
        echo 'laudusToken'.$this->laudusToken.'<br>';
        die();
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
