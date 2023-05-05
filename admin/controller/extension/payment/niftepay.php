<?php
class ControllerExtensionPaymentNiftePay extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/niftepay');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_niftepay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}
	
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/niftepay', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/niftepay', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
		
		if(isset($this->request->post['payment_niftepay_sandbox'])){
			$data['payment_niftepay_sandbox'] = $this->request->post['payment_niftepay_sandbox'];
		}else{
			$data['payment_niftepay_sandbox'] = $this->config->get('payment_niftepay_sandbox');
		}
		if(isset($this->request->post['payment_niftepay_merchantId'])){
			$data['payment_niftepay_merchantId'] = $this->request->post['payment_niftepay_merchantId'];
		}else{
			$data['payment_niftepay_merchantId'] = $this->config->get('payment_niftepay_merchantId');
		}
		
		if(isset($this->request->post['payment_niftepay_version'])){
			$data['payment_niftepay_version'] = $this->request->post['payment_niftepay_version'];
		}else{
			$data['payment_niftepay_version'] = $this->config->get('payment_niftepay_version');
		}
		
		if(isset($this->request->post['payment_niftepay_language'])){
			$data['payment_niftepay_language'] = $this->request->post['niftepay_language'];
		}else{
			$data['payment_niftepay_language'] = $this->config->get('payment_niftepay_language');
		}
		if(isset($this->request->post['payment_niftepay_password'])){
			$data['payment_niftepay_password'] = $this->request->post['payment_niftepay_password'];
		}else{
			$data['payment_niftepay_password'] = $this->config->get('payment_niftepay_password');
		}
		
		if(isset($this->request->post['payment_niftepay_currency'])){
			$data['payment_niftepay_currency'] = $this->request->post['payment_niftepay_currency'];
		}else{
			$data['payment_niftepay_currency'] = $this->config->get('payment_niftepay_currency');
		}
		
		if(isset($this->request->post['payment_niftepay_txnExpiryHours'])){
			$data['payment_niftepay_txnExpiryHours'] = $this->request->post['payment_niftepay_txnExpiryHours'];
		}else{
			$data['payment_niftepay_txnExpiryHours'] = $this->config->get('payment_niftepay_txnExpiryHours');
		}
		
		if(isset($this->request->post['payment_niftepay_integritySalt'])){
			$data['payment_niftepay_integritySalt'] = $this->request->post['payment_niftepay_integritySalt'];
		}else{
			$data['payment_niftepay_integritySalt'] = $this->config->get('payment_niftepay_integritySalt');
		}
		
		if (isset($this->request->post['payment_niftepay_total'])) {
			$data['payment_niftepay_total'] = $this->request->post['payment_niftepay_total'];
		} else {
			$data['payment_niftepay_total'] = $this->config->get('payment_niftepay_total');
		}

		if (isset($this->request->post['payment_niftepay_order_status_id'])) {
			$data['payment_niftepay_order_status_id'] = $this->request->post['payment_niftepay_order_status_id'];
		} else {
			$data['payment_niftepay_order_status_id'] = $this->config->get('payment_niftepay_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_niftepay_geo_zone_id'])) {
			$data['payment_niftepay_geo_zone_id'] = $this->request->post['payment_niftepay_geo_zone_id'];
		} else {
			$data['payment_niftepay_geo_zone_id'] = $this->config->get('payment_niftepay_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_niftepay_status'])) {
			$data['payment_niftepay_status'] = $this->request->post['payment_niftepay_status'];
		} else {
			$data['payment_niftepay_status'] = $this->config->get('payment_niftepay_status');
		}

		if (isset($this->request->post['payment_niftepay_sort_order'])) {
			$data['payment_niftepay_sort_order'] = $this->request->post['payment_niftepay_sort_order'];
		} else {
			$data['payment_niftepay_sort_order'] = $this->config->get('payment_niftepay_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/niftepay', $data));
	}
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/niftepay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}