<?php
class ControllerExtensionPaymentNiftePay extends Controller {
public function index() {
$this->language->load('extension/payment/niftepay');
$data['button_confirm'] = $this->language->get('button_confirm');
$data['action'] = '';
		$sandbox= trim($this->config->get('payment_niftepay_sandbox'));
		if($sandbox) {
		$data['action'] = 'https://uat-merchants.niftepay.pk/CustomerPortal/transactionmanagement/merchantform';
		} else {			
		$data['action'] = 'https://merchants.niftepay.pk/CustomerPortal/transactionmanagement/merchantform';
		}
$this->load->model('checkout/order');

	$data['pp_merchantId'] = $this->config->get('payment_niftepay_merchantId');
	$data['"pp_SubMerchantId'] = $this->config->get('payment_niftepay_"pp_SubMerchantId');
	$data['pp_version'] = $this->config->get('payment_niftepay_version');
	$data['pp_language'] = $this->config->get('payment_niftepay_language');
	$data['pp_password'] = $this->config->get('payment_niftepay_password');
	$data['pp_currency'] = $this->config->get('payment_niftepay_currency');

	$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
if ($order_info) {
    $price = $order_info['total'];
    $orderAmount = floatval($price);
    if (strpos($orderAmount, '.') !== false) {
        $amount = $orderAmount;
    } else {
        $amount = number_format($orderAmount, 1, ".", "") + 0;

    }
    $custEmail = $order_info['email'];
    $custCell = html_entity_decode($order_info['telephone'], ENT_QUOTES, 'UTF-8');
    $billReference=$order_info['order_id'];

    $items= $this->cart->getProducts();
    $product_name  = array();
    foreach ($items as $item) {
        array_push($product_name, $item['name']);
    }
    $description   = implode(", ", $product_name);
    $description = substr($description, 0, 150);
}
        $data['orderid'] = date('His') . $this->session->data['order_id'];
		$data['orderdate'] = date('YmdHis');
		$data['billemail'] =$description;
		$data['orderamount'] = $order_info['total'];
		$data['callbackurl'] = $this->url->link('extension/payment/niftepay/callback');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['continue'] = $this->url->link('checkout/success');

	//_secureHash
$returnURL= $this->url->link('extension/payment/niftepay/callback');	
$pp_merchantId = $this->config->get('niftepay_merchantId');
$pp_returnUrl = $returnURL;
$pp_password = $this->config->get('payment_niftepay_password');
$pp_language =  $this->config->get('payment_niftepay_language');
$pp_version = $this->config->get('payment_niftepay_version');
$pp_billRef =date('His').$this->session->data['order_id'];
$pp_integritySalt=$this->config->get('payment_niftepay_integritySalt');
$pp_currency = $this->config->get('payment_niftepay_currency');
$pp_txnExpiryHours =  $this->config->get('payment_niftepay_txnExpiryHours');
$billemail=$description;
$pp_amountTmp = $amount; 
$pp_amtSplitArray = explode('.', $pp_amountTmp); 
$pp_amount= $pp_amtSplitArray[0];
$pp_description = trim($billemail,",");
$pp_txnDateTime = date('Ymdhms');
$pp_txnExpDateTime = date('Ymdhms', strtotime('+'.$pp_txnExpiryHours.' hours')); 
$pp_txnRefNumber = "T".$pp_txnDateTime; 
$SortedArrayOld =$pp_integritySalt.'&'.$pp_amount.'&'.$pp_billRef.'&'.$pp_description.'&'.$pp_language.'&'.$pp_merchantId.'&'.$pp_password.'&'.$pp_returnUrl.'&'.$pp_currency.'&'.$pp_txnDateTime.'&'.$pp_txnExpDateTime.'&'.$pp_txnRefNumber.'&'.$pp_version.'&'.'1'.'&'.'2'.'&'.'3'.'&'.'4'.'&'.'5';
$pp_secureHash = hash_hmac('sha256', $SortedArrayOld, $pp_integritySalt);
$data['pp_secureHash']=$pp_secureHash;
$data['pp_Amount']=$pp_amount;
$data['pp_returnUrl']=$returnURL;
$data['pp_txnExpDateTime']=$pp_txnExpDateTime;
$data['pp_billRef']=$pp_billRef;
$data['pp_description']=$pp_description;
$data['"pp_TxnRefNo']=$pp_txnRefNumber;
$data['pp_txnDateTime']=$pp_txnDateTime;
//print_r($data);
//exit;
return $this->load->view('extension/payment/niftepay', $data);
	}
        
 public function callBack() {
    $this->load->model('checkout/order');
       	
			$sortedResponseArray = array();
			if (!empty($_POST)) {
				foreach ($_POST as $key => $val) {
					$sortedResponseArray[$key] = $val;
				}
			}	
			
	$CalSecureHash=$sortedResponseArray['pp_SecureHash'];
	$responseCode=$sortedResponseArray['pp_ResponseCode'];
	$responseMessage=$sortedResponseArray['pp_ResponseMessage'];
	$amount=$sortedResponseArray['pp_Amount'];
	$billReference=$sortedResponseArray['pp_BillReference'];
    
   // print_r($sortedResponseArray);
    //exit;
       
    if($responseCode== '124'|| $responseCode== '000' ) {
     $this->response->redirect($this->url->link('checkout/success', '', true));
    }
    else
    {
     $this->response->redirect($this->url->link('checkout/failure', '', true));
    }
  }
 
}