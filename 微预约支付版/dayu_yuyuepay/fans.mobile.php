<?php
	load()->model('mc');
	$setting = $this->module['config'];
	if($setting['oauth']==1){
		$weid = $this->_weid;
		$openid = $this->_openid;
		$authurl = $_W['siteurl'].'&authkey=1';
		$url = $_W['siteurl'];
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $openid = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $openid = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
					$this->showMessage('授权失败.');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->get_Code($url);
                }
            }
        }
	}else{
		$weid = $_W['uniacid'];
		$openid = $_W['openid'];
	}
//		$this->checkAuth($openid,$nickname,$headimgurl);
//		print_r($_W['fans']);
		$fans = mc_fansinfo($openid,$_W['acid'], $weid);
//		print_r($openid);
		$cid = $_GPC['id'];
		
		$uid = !empty($_W['member']['uid']) ? $_W['member']['uid']:$fans['uid'];
		$member = mc_fetch($uid);
		$member['avatar'] = !empty($member['avatar']) ? $member['avatar'] : $headimgurl;
		$mcgroups = mc_groups();
		$member['group'] = $mcgroups[$member['groupid']];
		$isstaff = $this->get_staff($openid);
		$sid = $setting['store']==1 ? intval($_GPC['sid']) : '';
		if($setting['store']==1 && pdo_tableexists('dayu_yuyuepay_plugin_store_store') && !empty($sid)){
			$store = $this->get_store($sid);
			$store['score_num'] = $store['score_num'] == 0 ? 5 : round(($store['total_score']/$store['score_num']),0);
		}
		$index_url = $setting['store']==1 && pdo_tableexists('dayu_yuyuepay_plugin_store_store') ? murl('entry', array('do' => 'store', 'm' => 'dayu_yuyuepay_plugin_store'), true, true) : $this->createMobileUrl('list');
		$manage_url = $setting['store']==1 && pdo_tableexists('dayu_yuyuepay_plugin_store_store') ? murl('entry', array('do' => 'boss', 'm' => 'dayu_yuyuepay_plugin_store'), true, true) : $this->createMobileUrl('manager');
?>