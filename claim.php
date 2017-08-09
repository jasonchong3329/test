<?php
    include_once 'connect.php';
    include_once 'system.php';
    include_once 'include_function.php';
    include_once 'class/Claim.php'; 
    include_once 'class/SavehandlerApi.php';
    include_once 'class/GeneralFunction.php';
    include_once 'language.php';
    $o = new Claim();sdfsdfsdfsf
    $s = new SavehandlerApi();
    $gf = new GeneralFunction();
    $o->save = $s;
    $o->document_type = 'CLS';
    $o->document_name = 'Claims Management';
    $o->document_code = 'Claim';
    $o->document_url = 'claim.php';
    $o->menu_id = 24;

    $action = escape($_REQUEST['action']);

    $o->claim_id = escape($_REQUEST['claim_id']);
    
    $o->claim_so = escape($_POST['claim_so']);
    $o->claim_engineers = escape($_POST['claim_engineers']);
    $o->claim_datefrom = escape($_POST['claim_datefrom']);
    $o->claim_remark = escape($_POST['claim_remark']);
    $o->claim_sorder = escape($_REQUEST['claim_sorder']);
    $o->claim_nonso = escape($_POST['claim_nonso']);
    if($o->claim_datefrom == ""){
        $o->claim_datefrom = system_date;
    }
    $o->claim_dateto = escape($_POST['claim_dateto']);
    if($o->claim_dateto == ""){
        $o->claim_dateto = system_date;
    }
    
    $o->claim_engineer_id = escape($_REQUEST['claim_engineer_id']);
    
    $o->clmd_seqno = escape($_POST['clmd_seqno']);
    $o->clmd_expenses_id = escape($_POST['clmd_expenses_id']);
    $o->clmd_expenses_desc = escape($_POST['clmd_expenses_desc']);
    $o->clmd_date = escape($_POST['clmd_date']);
    $o->clmd_currency_id = escape($_POST['clmd_currency_id']);
    $o->clmd_amt = str_replace(",", "",$_POST['clmd_amt']);
    $o->clmd_rate = str_replace(",", "",$_POST['clmd_rate']);
    $o->clmd_samt = str_replace(",", "",$_POST['clmd_samt']);
    $o->clmd_eamt = str_replace(",", "",$_POST['clmd_eamt']);
    $o->clmd_isreceipt = escape($_POST['clmd_isreceipt']);
    $o->clmd_isamex = escape($_POST['clmd_isamex']);
    $o->clmd_isprep = escape($_POST['clmd_isprep']);
    $o->clmd_ispriv = escape($_POST['clmd_ispriv']);
    $o->clmd_id = escape($_POST['clmd_id']);
    
    $o->clms_percent = escape($_POST['clms_percent']);
    $o->clms_sorder_id = escape($_POST['clms_sorder_id']);
    $o->clms_seqno = escape($_POST['clms_seqno']);
    $o->clms_id = escape($_POST['clms_id']);
    $o->smt = escape($_POST['smt']);
    
    if($o->clmd_seqno == ""){
        $o->clmd_seqno = 10;
    }
    if(!is_numeric($o->clmd_amt)){
        $o->clmd_amt = 0;
    }
    if(!is_numeric($o->clmd_rate)){
        $o->clmd_rate = 1;
    }
    if(!is_numeric($o->clmd_samt)){
        $o->clmd_samt = 0;
    }
    if(!is_numeric($o->clmd_eamt)){
        $o->clmd_eamt = 0;
    }
    switch ($action) { 
        case "createForm":
            if($o->checkIsGenerate()){
                header("Refresh: 0;url=$o->document_url?action=edit_claim&claim_id=$o->claim_id");
            }else{
                $o->getInputForm();
            }
            exit();
            break;
        case "create_claim":
            if($o->createClaim()){
                $_SESSION['status_alert'] = 'alert-success';
                $_SESSION['status_msg'] = "Create success.";
                rediectUrl("$o->document_url?action=edit_claim&claim_id=$o->claim_id",getSystemMsg(1,'Create data successfully'));
            }else{
                $_SESSION['status_alert'] = 'alert-error';
                $_SESSION['status_msg'] = "Create fail.";
                rediectUrl("$o->document_url?action=createForm",getSystemMsg(0,'Create data fail'));
            }
            exit();
            break;
        case "update_claim":
            if($o->updateClaim()){
                $_SESSION['status_alert'] = 'alert-success';
                $_SESSION['status_msg'] = "Update success.";
                rediectUrl("$o->document_url?action=edit_claim&claim_id=$o->claim_id",getSystemMsg(1,'Update data successfully'));
            }else{
                $_SESSION['status_alert'] = 'alert-error';
                $_SESSION['status_msg'] = "Update fail.";
                rediectUrl("$o->document_url?action=edit_claim&claim_id=$o->claim_id",getSystemMsg(0,'Update data fail'));
            }
            exit();
            break; 
        case "edit_claim":
            if($o->fetchClaimDetail(" AND claim_id = '$o->claim_id'","","",1)){
                $o->getInputForm();
            }else{
               rediectUrl("$o->document_url",getSystemMsg(0,'Fetch Data'));
            }
            exit();
            break; 
        case "delete_claim":
            if($o->deleteClaim()){
                $_SESSION['status_alert'] = 'alert-success';
                $_SESSION['status_msg'] = "Delete success.";
                rediectUrl("$o->document_url",getSystemMsg(1,'Delete data successfully'));
            }else{
                $_SESSION['status_alert'] = 'alert-error';
                $_SESSION['status_msg'] = "Delete fail.";
                rediectUrl("$o->document_url?action=edit_claim&claim_id=$o->claim_id",getSystemMsg(0,'Delete data fail'));
            }
            exit();
            break;
       case "saveline":
       case "updateline":    
//            $o->calculateLineAmount();

            if($o->clmd_id > 0 && $action == 'updateline'){
                $issuccess = $o->updateClaimLine();
            }else{
                $issuccess = $o->createClaimLine();
            }
            if($issuccess){
                echo json_encode(array('status'=>1));
            }else{
                echo json_encode(array('status'=>0));
            }
            exit();
            break;
       case "deleteline":
           if($o->deleteOrderLine()){
               echo json_encode(array('status'=>1));
           }else{
               echo json_encode(array('status'=>0));
           }
           exit();
           break;
       case "saveclmsline":
       case "updateclmsline":    

            if(($o->clmd_id > 0) && ($o->clms_id > 0) && ($action == 'updateclmsline')){
                $issuccess = $o->updateServJobLine();
            }else{
                $issuccess = $o->createServJobLine();
            }
            if($issuccess){
                echo json_encode(array('status'=>1,'clmd_id'=>$o->clmd_id));
            }else{
                echo json_encode(array('status'=>0));
            }
            exit();
            break;
       case "deleteclmsline":
           if($o->deleteServJobLine()){
               echo json_encode(array('status'=>1));
           }else{
               echo json_encode(array('status'=>0));
           }
           exit();
           break;
        case "getDataTable":
            $o->getDataTable();
            exit();
            break;
        case "getClmdAjaxDetail":
            $r = $o->getClmdAjaxDetail();

            echo json_encode(array('clmd_expenses_id'=>$r['clmd_expenses_id'],'clmd_expenses_desc'=>$r['clmd_expenses_desc'],
                                   'clmd_date'=>$r['clmd_date'],'clmd_currency_id'=>$r['clmd_currency_id'],
                                   'clmd_amt'=>$r['clmd_amt']));
            exit();
            break;
        case "refreshSevJobLine":
            $html = $o->getServJobLine();
            echo json_encode(array('html'=>$html));
            exit();
            break;
        default: 
            $o->getListing();
            exit();
            break;
    }


