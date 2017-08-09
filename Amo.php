<?php

class Amo
{
	protected $urls;
	protected $account;
	
	public function __construct($login, $hash, $subdomain)
	{
		$this->urls = array(
			'login' => 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json',
			'current' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/accounts/current',
			'get_leads' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/leads/list',
			'set_leads' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/leads/set',
			'set_contacts' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/contacts/set',
			'get_contacts' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/contacts/list',
			'get_links' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/contacts/links',
			'get_tasks' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/tasks/list',
			'get_pipelines' => 'https://' . $subdomain . '.amocrm.ru/private/api/v2/json/pipelines/list'
		);
		
		
		
		$user = array(
			'USER_LOGIN' => $login,
			'USER_HASH' => $hash
		);
		
		$this->request($this->urls['login'], $user);
		$this->account = $this->request($this->urls['current'])->response->account;
	}
	
	protected function request($url, $data = null)
	{
		$errors = array(
			301 => 'Moved permanently',
			400 => 'Bad request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not found',
			500 => 'Internal server error',
			502 => 'Bad gateway',
			503 => 'Service unavailable'
		);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		if ($data) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}
		
		$response = curl_exec($ch);
		$res_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);

		if ($res_code != 200 && $res_code != 204) {
			exit($errors[$res_code]);
		}
		
		return json_decode($response);
	}
	
	public function searchField($fields, $name)
	{
		foreach ($fields as $field) {
			if ($field->name == $name) {
				return $field;
			}
		}
	}
	
	public function setLead($data)
	{
		foreach ($data['fields'] as $field) {
			$custom_fields[] = array(
				'id' => (int) $this->searchField($this->account->custom_fields->leads, $field['name'])->id,
				'values' => array(
					array(
						'value' => $field['value']
					)
				)
			);
		}
		
		$leads['request']['leads']['add'][] = array(
			'name' => $data['name'],
			'price' => (int) $data['price'],
			'status_id' => (int) $this->searchField($this->account->leads_statuses, $data['status'])->id,
			'tags' => $data['tags'],
			'custom_fields' => $custom_fields
		);
		
		return $this->request($this->urls['set_leads'], $leads);
	}
	
	public function setContact ($data)
	{
		$leads_list = $this->setLead($data);

		$contacts['request']['contacts']['add'][] = array(
				'name' => $data['fields'][1]['val'],
				'linked_leads_id' => array($leads_list->response->leads->add[0]->id),
				'tags' => $data['tags'],
				'custom_fields' => array(
						array(
							'id' => 327731,
							'values' => array(
								array(
									'value' => $data['fields'][2]['val'],
									'enum' => 'WORK'									
								)
							)
						),
						array(
							'id' => 327737,
							'values' => array(
								array(
									'value' => $data['fields'][3]['val'],
									'enum' => 'SKYPE'
								)
							)
						)
					)
		);
		//var_dump($contacts);
		$this->request($this->urls['set_contacts'], $contacts);
		//var_dump($this->request($this->urls['set_contacts'], $contacts));
	}
	
	public function getTask($id)
	{
		return $this->request($this->urls['get_tasks'] . '?id=' . $id)->response->tasks;
	}
	
	public function getTasks($type, $limit_rows = 500, $limit_offset = 0)
	{
		return $this->request($this->urls['get_tasks'] . '?type=' . $type . '&limit_rows=' . $limit_rows . '&limit_offset=' . $limit_offset)->response->tasks;
	}
	
	public function getContact($id)
	{
		return $this->request($this->urls['get_contacts'] . '?id=' . $id)->response->contacts;
	}

	public function getContacts($limit, $offset)
	{
		return $this->request($this->urls['get_contacts'] . '?limit_rows=' . $limit . '&' . 'limit_offset=' . $offset)->response->contacts;
	}
	
	public function getLinks($type, $id)
	{
		return $this->request($this->urls['get_links'] . '?' . $type . '=' . $id)->response->links;
	}
	
	public function getPipelines()
	{
		return $this->request($this->urls['get_pipelines'])->response->pipelines;
	}
}
