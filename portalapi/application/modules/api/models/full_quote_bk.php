<?php 


// //Full Quote Request
// 	public function get_full_quote_data($lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $nominees, $proposal_details, $policy_sub_type_id, $sum_insured){ 
	
// 		//get proposal policy details
// 		$data = array();
// 		$data['customer_data'] = (array)$this->get_profile($emp_id);
// 		$data['member_data'] = (array)$this->get_all_member_data($proposal_policy_id);
// 		$data['nominee_data'] = $nominees;
// 		$data['proposal_data'] = $proposal_details;
		
// 		//echo "<pre>";print_r($data);exit;
		
// 		$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and lead_id='$lead_id' and proposal_policy_id='$proposal_policy_id' ")->row_array();
		
		
	
// 		if($query_quote > 0){
				
// 			//get Api URL with policy_sub_type_id
// 			$getApiUrl = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='".$policy_sub_type_id."' ")->row_array();
			
// 			$url = trim($getApiUrl['api_url']);
			
// 			if($url == ''){
// 				return array(
// 					"status" => "error",
// 					"msg" => "Something Went Wrong"
// 				);
// 				exit;
// 			}
			
// 			//Get Policy Details
// 			$policyDetails = $this->db->query("select * from proposal_policy where proposal_policy_id='".$proposal_policy_id."' ")->row_array();
			
// 			$coi_url = trim($policyDetails['coi_url']);
// 			$coi_uid_url = trim($policyDetails['coi_uid_url']);
		
// 			if($url == ''){
// 				return array(
// 					"status" => "error",
// 					"msg" => "Something Went Wrong"
// 				);
// 			}
			
// 			$occupation = '';
			
// 			//Data Variables
// 			if($data['proposal_data'][0]['mode_of_payment'] == 'Online'){
// 				$trans_date = $data['proposal_data'][0]['transaction_date'];
// 			}else if($data['proposal_data'][0]['mode_of_payment'] == 'NEFT'){
// 				$trans_date = $data['proposal_data'][0]['transaction_date'];
// 			}else{
// 				$trans_date = $data['proposal_data'][0]['transaction_date'];
// 			}
			
			
			
// 			//$Member_Customer_ID = $data['customer_data']['customer_id'];
// 			$Member_Customer_ID = 10000000000004;
// 			$uidNo = (!empty($data['customer_data']['adhar'])) ? $data['customer_data']['adhar'] : null;
			
// 			$MasterPolicyNumber = '61-20-00040-00-00';
// 			$GroupID = 'GRP001';
// 			//$PlanCode = '4211';
// 			//$plan_code = (!empty($data['proposal_data']['plan_code'])) ? $data['proposal_data']['plan_code'] : null;
// 			$plan_code = '4211';
// 			$Product_Code = '4211';
			
// 			$Member_Type_Code = null;
// 			$intermediaryCode = '2108233';
// 			$intermediaryBranchCode = '10MHMUM01';
// 			$SchemeCode = '4112000003';
			
// 			//get SumInsured Type
// 			$sumins_type = $this->db->query("select mi.suminsured_type from master_policy_si_type_mapping as m left join master_suminsured_type as mi ON m.suminsured_type_id = mi.suminsured_type_id where m.master_policy_id='$master_policy_id' ")->row();
			
// 			//echo "<pre>";print_r($sumins_type);exit;
// 			$SumInsured_Type = $sumins_type->suminsured_type;
// 			//echo $SumInsured_Type;exit;
			
			
			
// 			$leadID = $lead_id;
// 			$txnRefNumber = "pay_FrAaDQjzQFtQWG";
// 			$PaymentMode = "online";
// 			$bankName = 0;
// 			$branchName = null;
// 			$bankLocation = null;
// 			$chequeType = null;
// 			$ifscCode = null;
// 			$terminal_id = "EuxJCz8cZV9V63";
// 			//$trans_date = date("Y-m-d", strtotime($trans_date));
// 			$trans_date = '2020-10-20';
			
// 			//$SumInsured = $sum_insured;
// 			$SumInsured = 500000;
// 			$product_id = $data['proposal_data'][0]['plan_id'];
// 			$tax_amount = $policyDetails['tax_amount'];
// 			//$collectionAmount = ( $policyDetails['premium_amount'] + $tax_amount);
// 			$collectionAmount = 4260;
			
			
			
			
// 			$SPID = 0;
// 			$RefCode1 = 0;
// 			$RefCode2 = 0;
// 			$Policy_Tanure = 1;
// 			$AutoRenewal = 'Y';
			
// 			$totalMembers = count($data['member_data']);
			
// 			$member = [];
			
// 			$explode_name = array($data['customer_data']['first_name'],$data['customer_data']['middle_name'],$data['customer_data']['last_name']);
// 			$explode_name_nominee = array($data['nominee_data']['nominee_first_name'],$data['nominee_data']['nominee_last_name']);
		   
// 			for ($i = 0;$i < $totalMembers; $i++){
				
// 				//Checking relation based on self, spouse, son and daughter
// 				if($data['member_data'][$i]['relation_with_proposal'] == 1 ){
// 					$data['member_data'][$i]['relation_code'] = "R001";
// 				}else if($data['member_data'][$i]['relation_with_proposal'] == 2 ){
// 					$data['member_data'][$i]['relation_code'] = "R002";
// 				}else if($data['member_data'][$i]['relation_with_proposal'] == 3 ){
// 					$data['member_data'][$i]['relation_code'] = "R003";
// 				}else{
// 					$data['member_data'][$i]['relation_code'] = "R004";
// 				}
				
// 				//Nominee relation code
// 				if($data['nominee_data']['nominee_relation'] == 1 ){
// 					$data['nominee_data']['relation_code'] = "R001";
// 				}else if($data['nominee_data']['nominee_relation'] == 2 ){
// 					$data['nominee_data']['relation_code'] = "R002";
// 				}else if($data['nominee_data']['nominee_relation'] == 3 ){
// 					$data['nominee_data']['relation_code'] = "R003";
// 				}else{
// 					$data['nominee_data']['relation_code'] = "R004";
// 				}
			
				
// 				$abc = ["PEDCode" => null, "Remarks" => null];
			
// 				$member[] = ["MemberNo" => $i + 1, "Salutation" => $data['member_data'][$i]['policy_member_salutation'] , "First_Name" => $data['member_data'][$i]['policy_member_first_name'], "Middle_Name" => null, "Last_Name" => !empty($data['member_data'][$i]['policy_member_last_name']) ? $data['member_data'][$i]['policy_member_last_name'] : '.', "Gender" => ($data['member_data'][$i]['policy_member_gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['policy_member_dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $plan_code, "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

// 			}
			
// 			$data['proposal_data']['SourceSystemName_api'] = 'CreditorPortal';
		
			

// 			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $Member_Customer_ID, "salutation" => $data['customer_data']['salutation'],"firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1]) ? $explode_name[1] : '.',"dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F","educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo,"maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email_id'],"contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null,"annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null,"ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null,"homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null,"homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null,"mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null,"mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null,"ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null,"EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null],"PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $MasterPolicyNumber,"GroupID" => $GroupID, "Product_Code" => $Product_Code,"SumInsured_Type"=> null,"Policy_Tanure"=> "1","Member_Type_Code"=> $Member_Type_Code, "intermediaryCode" => $intermediaryCode,"AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null,"Customer_Signature_Date" => null,"businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1,"RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => $PaymentMode,"PolicyproductComponents" => ["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode] ], "MemObj" => ["Member" => $member],"ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null,"paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collectionAmount,2),"collectionRcvdDate" => $trans_date,"collectionMode" => $PaymentMode, "remarks" => null, "instrumentNumber" => $txnRefNumber,"instrumentDate" => $trans_date, "bankName" => $bankName, "branchName" => $branchName,"bankLocation" => $bankLocation, "micrNo" => null, "chequeType" => $chequeType, "ifscCode" => $ifscCode, "PaymentGatewayName" => $data['proposal_data']['SourceSystemName_api'], "TerminalID" => $terminal_id,"CardNo" => null]];
		 
// 			//$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) ,"product_id" => "ABC", "type"=>"full_quote_request2"];
			
// 			echo "<pre>";print_r($fqrequest);exit;
			
// 			$request_arr = ["lead_id" => $leadID, "req" => json_encode($fqrequest) ,"product_id" => $product_id, "type"=>"full_quote_request", "proposal_policy_id"=> $proposal_policy_id];
		
// 			$this->db->insert("logs_docs",$request_arr);
// 			$insert_id = $this->db->insert_id();

// 			$curl = curl_init();

// 			curl_setopt_array($curl, array(
// 				CURLOPT_URL => $url,
// 				CURLOPT_RETURNTRANSFER => true,
// 				CURLOPT_ENCODING => "",
// 				CURLOPT_MAXREDIRS => 10,
// 				CURLOPT_TIMEOUT => 60,
// 				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 				CURLOPT_CUSTOMREQUEST => "POST",
// 				CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
// 				CURLOPT_HTTPHEADER => array(
// 					"Accept: */*",
// 					"Cache-Control: no-cache",
// 					"Connection: keep-alive",
// 					"Content-Length: " . strlen(json_encode($fqrequest)) ,
// 					"Content-Type: application/json",
// 					"Host: bizpre.adityabirlahealth.com"
// 				) ,
// 			));

// 			$response = curl_exec($curl);
			
// 			$request_arr = ["res" => json_encode($response)];
// 			$this->db->where("id",$insert_id);
// 			$this->db->update("logs_docs",$request_arr);

// 			$err = curl_error($curl);
// 			echo "<pre>";print_r($response);exit;
			
// 			curl_close($curl);

// 			if ($err){
// 				//echo "in";exit;
// 				return array(
// 					"status" => "error",
// 					"msg" => $err
// 				);
// 			}else{
// 				//echo "out";exit;
// 				// print_pre($response);exit;
// 				$new = simplexml_load_string($response);
// 				$con = json_encode($new);
// 				$newArr = json_decode($con, true);
// 				//print_pre($newArr);exit;
				
// 				$errorObj = $newArr['errorObj'];
// 				$return_data = [];
// 				// print_pre($errorObj);exit;
// 				if($errorObj['ErrorNumber']=='00'){
				  
// 					$return_data['status'] = 'Success';
// 					$return_data['msg'] = $errorObj['ErrorMessage'];

// 					$api_insert = array(
// 						"emp_id" => $emp_id,
// 						"client_id" => $newArr['policyDtls']['ClientID'],
// 						"certificate_number" => $newArr['policyDtls']['CertificateNumber'],
// 						"quotation_no" => $newArr['policyDtls']['QuotationNumber'],
// 						"proposal_no" => $newArr['policyDtls']['ProposalNumber'],
// 						"policy_no" => $newArr['policyDtls']['PolicyNumber'],
// 						"gross_premium" => $newArr['premium']['GrossPremium'],
// 						"status" => $errorObj['ErrorMessage'],
// 						"start_date" => $newArr['policyDtls']['startDate'],
// 						"end_date" => $newArr['policyDtls']['EndDate'],
// 						"created_date" => date('Y-m-d H:i:s'),
// 						"proposal_no_lead"=>$data['proposal_data']['proposal_no'],
// 						"CustomerID" => $data['customer_data']['cust_id'],
// 						"PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
// 						"MemberCustomerID" => $data['customer_data']['cust_id'],
// 						"letter_url" => $newArr['policyDtls']['LetterURL']
// 					);

// 					$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) , "type"=>"api_insert_retail"];
// 					$this->db->insert("logs_docs",$request_arr);
				  
// 					$this->db->insert('api_proposal_response', $api_insert);
				  
// 					//get coi member id data
				  
// 					$coi_uid_request = ["COINumber" => $newArr['policyDtls']['CertificateNumber']];
				  
// 					$curl_new = curl_init();

// 					curl_setopt_array($curl_new, array(
// 						CURLOPT_URL => $coi_uid_url,
// 						CURLOPT_RETURNTRANSFER => true,
// 						CURLOPT_ENCODING => "",
// 						CURLOPT_MAXREDIRS => 10,
// 						CURLOPT_TIMEOUT => 60,
// 						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 						CURLOPT_CUSTOMREQUEST => "POST",
// 						CURLOPT_POSTFIELDS => json_encode($coi_uid_request) ,
// 						CURLOPT_HTTPHEADER => array(
// 							"Accept: */*",
// 							"Cache-Control: no-cache",
// 							"Connection: keep-alive",
// 							"Content-Length: " . strlen(json_encode($coi_uid_request)) ,
// 							"Content-Type: application/json",
// 							"Host: bizpre.adityabirlahealth.com"
// 						) ,
// 					));

// 					$response_new = curl_exec($curl_new);
				  
// 					$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($coi_uid_request),"res" => json_encode($response_new) , "type"=>"coi_uid_genarate"];
// 					$this->db->insert("logs_docs",$request_arr);
				  
// 					$result_data = json_decode($response_new,true);
// 					$result_data = $result_data['clsReturn'];
				  
// 					if(!empty($result_data)){
// 						foreach ($result_data as $t => $v){
// 							$uid[$t] = $v['strMemberCode'];
// 						}

// 						$exclude_amt = $data['proposal_data']['net_premium'];
// 						$include_amt = $data['proposal_data']['premium'];
// 						$other_amt = (float) ($include_amt - $exclude_amt)/2;
				  
// 						$start_date = explode(" ",$newArr['policyDtls']['startDate']);
// 						$start_date = date("d/m/Y", strtotime($start_date[0]));
				  
// 						$end_date = explode(" ",$newArr['policyDtls']['EndDate']);
// 						$end_date = date("d/m/Y", strtotime($end_date[0]));
// 						$end_date_new = date("d-m-Y", strtotime($end_date));
				 
				  
// 						$coi_request = [
// 							"ClientCode" => "AXIS_D2C", 
// 							"ProdCode" => "ABHI010", 
// 							"NoOfMembers" => $totalMembers, 
// 							"MPN" => $data['proposal_data']['master_policy_no'], 
// 							"MPHN" => "M/s Axis Bank Limited", 
// 							"MPStartDt" => "26/02/2020", 
// 							"MPEndDt" => "25/02/2021", 
// 							"PHMobNo" => $data['customer_data']['mob_no']." ".$data['customer_data']['email'], 
// 							"PHEmail" => $data['customer_data']['email'], 
// 							"PHContactDtls" => "", 
// 							"PrmPaymntMode" => "Yearly", 
// 							"InstNum" => $data['proposal_data']['TxRefNo'], 
// 							"InstDt" => $trans_date, 
// 							"BankName" => "Axis Bank Limited", 
// 							"PIO" => "Aditya Birla Health Insurance Company Limited,10th Floor,R-Tech Park,Nirlon Compound,Next To HUB Mall,Off Western Express Highway,Goregaon East,Mumbai-400063", 
// 							"PSO" => "Aditya Birla Health Insurance Company Limited, 7th floor, C building, Modi Business Centre, Kasarvadavali, Mumbai, Thane West 400 615", 
// 							"PHN" => $data['customer_data']['customer_name'], 
// 							"PN" => "", 
// 							"PHAdd" => "", 
// 							"PPN" => "Group Activ Health", 
// 							"ProdName" => $newArr['policyDtls']['ProductName'], 
// 							"PlanName" => "", 
// 							"PStartDt" => $start_date, 
// 							"PEndDt" => $end_date, 
// 							"IMDCode" => $data['proposal_data']['IMDCode'], 
// 							"IMDName" => "", 
// 							"IMDEmail" => "", 
// 							"IMDCDtls" => "", 
// 							"CN" => $newArr['policyDtls']['CertificateNumber'], 
// 							"PHNameAndAdd" => $data['customer_data']['customer_name']." ".$data['customer_data']['address']." ".$data['customer_data']['emp_city']." ".$data['customer_data']['emp_state']." ".$data['customer_data']['emp_pincode'], 
// 							"PHCommAdd" => "", 
// 							"UID" => $data['customer_data']['lead_id'], 
// 							"CvrType" => "", 
// 							"MID1" => $uid[0], 
// 							"IPName1" => $data['member_data'][0]['firstname'], 
// 							"IPDOB1" => $data['member_data'][0]['dob'], 
// 							"IPGender1" => ($data['member_data'][0]['gender'] == "Male") ? "M" : "F", 
// 							"IPNomName1" => $data['nominee_data']['nominee_fname'], 
// 							"IPNomRel1" => "Self", 
// 							"SumInsured" => $data['proposal_data']['sum_insured'], 
// 							"IPRel1" => ucfirst($data['nominee_data']['fr_name']), 
// 							"MID2" => isset($uid[1])?$uid[1]:"", 
// 							"IPName2" => isset($data['member_data'][1]['firstname'])?$data['member_data'][1]['firstname']:"", 
// 							"IPDOB2" => isset($data['member_data'][1]['dob']) ? $data['member_data'][1]['dob']:"", 
// 							"IPGender2" => isset($data['member_data'][1]['gender']) ? (($data['member_data'][1]['gender'] == "Male") ? "M" : "F") :"", 
// 							"IPNomName2" => "", 
// 							"IPNomRel2" => isset($data['member_data'][1]['fr_name']) ? $data['member_data'][1]['fr_name'] :"", 
// 							"MID3" => isset($uid[2])?$uid[2]:"", 
// 							"IPName3" => isset($data['member_data'][2]['firstname'])?$data['member_data'][2]['firstname']:"", 
// 							"IPDOB3" => isset($data['member_data'][2]['dob']) ? $data['member_data'][2]['dob']:"", 
// 							"IPGender3" => isset($data['member_data'][2]['gender']) ? (($data['member_data'][2]['gender'] == "Male") ? "M" : "F") :"", 
// 							"IPNomName3" => "", 
// 							"IPNomRel3" => isset($data['member_data'][2]['fr_name']) ? $data['member_data'][2]['fr_name'] :"", 
// 							"MID4" => isset($uid[3])?$uid[3]:"", 
// 							"IPName4" => isset($data['member_data'][3]['firstname'])?$data['member_data'][3]['firstname']:"", 
// 							"IPDOB4" => isset($data['member_data'][3]['dob']) ? $data['member_data'][3]['dob']:"", 
// 							"IPGender4" => isset($data['member_data'][3]['gender']) ? (($data['member_data'][3]['gender'] == "Male") ? "M" : "F") :"", 
// 							"IPNomName4" => "", 
// 							"IPNomRel4" => isset($data['member_data'][3]['fr_name']) ? $data['member_data'][3]['fr_name'] :"", 
// 							"InitCNAndStartDate" => "", 
// 							"PayoutBasis" => "", 
// 							"Options" => isset($abcd[3])?implode(",", $abcd[3]):"", 
// 							"ApplicabilityOpt1" => "Applicable", 
// 							"SumInsrdLmtOpt1" => "", 
// 							"PEDDtls1" => isset($abcd[0])?implode(",", $abcd[0]):"", 
// 							"PEDDtls2" => isset($abcd[1])?implode(",", $abcd[1]):"", 
// 							"NP" => $exclude_amt, 
// 							"CGST"=> "",
// 							"SGST"=> "",
// 							"IGST"=>($other_amt*2),
// 							"GP"=> $include_amt,
// 							"ClmAssistance" => "Static", 
// 							"UWNotesAndImpTrmsConditns" => "Static", 
// 							"COINo1" => $newArr['policyDtls']['CertificateNumber'], 
// 							"CvrgType1" => "", 
// 							"PrmAmt1" => "", 
// 							"PaymntDt1" => "", 
// 							"FY1" => isset($abcd[2])?implode(",", $abcd[2]):"", 
// 							"YrWiseProportionatePrmAmt1" => "", 
// 							"StampDutyAmt" => "", 
// 							"StampDuty" => "", 
// 							"PolIssDt" => $start_date, 
// 							"Place" => "Mumbai", 
// 							"SumInsured1" => $data['proposal_data']['sum_insured'], 
// 							"SumInsured2" => "", 
// 							"SumInsured3" => "", 
// 							"SumInsured4" => "" 
// 						]; 
				  
				  
// 						$curl = curl_init();

// 						curl_setopt_array($curl, array(
// 							CURLOPT_URL => $coi_url,
// 							CURLOPT_RETURNTRANSFER => true,
// 							CURLOPT_ENCODING => "",
// 							CURLOPT_MAXREDIRS => 10,
// 							CURLOPT_TIMEOUT => 60,
// 							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 							CURLOPT_CUSTOMREQUEST => "POST",
// 							CURLOPT_POSTFIELDS => json_encode($coi_request) ,
// 							CURLOPT_HTTPHEADER => array(
// 								"Accept: */*",
// 								"Cache-Control: no-cache",
// 								"Connection: keep-alive",
// 								"Content-Length: " . strlen(json_encode($coi_request)) ,
// 								"Content-Type: application/json",
// 								"Host: bizpre.adityabirlahealth.com"
// 							) ,
// 						));

// 						$response = curl_exec($curl);
// 						//print_pre($response);exit;
// 						$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($coi_request),"res" => json_encode($response) , "type"=>"coi_genarate"];
// 						$this->db->insert("logs_docs",$request_arr);
					
// 						if ($err){
// 							return array(
// 								"status" => "error",
// 								"msg" => $err
// 							);
// 						}else{
					  
// 							$res_url = json_decode($response,true);
// 							$coi_url = $res_url['COIUrl'];

// 							$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "url" => $coi_url];
// 							$this->db->insert("coi_genarate_url",$request_arr);
							
// 							$click_url =$this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R05'")->row_array();
					
// 							$url_req = "https://api-alerts.kaleyra.com/v4/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($coi_url)."&title=xyz";
						
// 							$curl = curl_init();
					
// 							curl_setopt_array($curl, array(
// 								CURLOPT_URL => $url_req,
// 								CURLOPT_RETURNTRANSFER => true,
// 								CURLOPT_ENCODING => "",
// 								CURLOPT_MAXREDIRS => 10,
// 								CURLOPT_TIMEOUT => 30,
// 								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 								CURLOPT_CUSTOMREQUEST => "GET",
// 								//CURLOPT_POSTFIELDS => $parameters,
// 								CURLOPT_HTTPHEADER => array(
// 									"cache-control: no-cache",
// 									"content-type: application/json",
						   
// 								),
// 							));

// 							$result = curl_exec($curl);
						
// 							curl_close($curl);
						
// 							$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) , "type"=>"bitly_url_coi"];
// 							$this->db->insert("logs_docs",$request_arr);
							
// 							$data_url = json_decode($result,true);
							
// 							if(empty($data_url)){
// 								$data_url['txtly'] = $coi_url;
// 							}
							

// 							$senderID = 1;

// 							$AlertV1 = $data['customer_data']['customer_name'];
// 							$AlertV2 = 'Group Activ Health';
// 							$AlertV3 = $start_date;
// 							$AlertV4 = $data['customer_data']['customer_name'];
// 							$AlertV5 = $newArr['policyDtls']['CertificateNumber'];
// 							$AlertV6 = $start_date;
// 							$AlertV7 = $end_date;
// 							$AlertV8 = date('d/m/Y', strtotime($end_date_new. ' + 1 day'));
// 							$AlertV9 = $data_url['txtly'];
// 							$AlertV10 = 'Annual';
// 							$AlertV11 = 'Health Insurance Policy';
// 							$AlertV12 = $data['proposal_data']['sum_insured'];
						
// 							// Added By Shardul For Validating ISNRI on 20-Aug-2020
// 							$dataArray['emp_id'] = !empty($data['customer_data']['emp_id']) ? $data['customer_data']['emp_id'] : "";
// 							$dataArray['isNri'] = !empty($data['customer_data']['ISNRI']) ? $data['customer_data']['ISNRI'] : "";
// 							$dataArray['product_id'] = !empty($data['customer_data']['product_id']) ? $data['customer_data']['product_id'] : "";;
// 							$alertMode = '';//helper_validate_is_nri($dataArray);
						
// 							$parameters =[
// 								"RTdetails" => [
// 								"PolicyID" => '',
// 								"AppNo" => 'DC100025765',
// 								"alertID" => 'A735',
// 								"channel_ID" => 'D2C Application',
// 								"Req_Id" => 1,
// 								"field1" => '',
// 								"field2" => '',
// 								"field3" => '',
// 								// "Alert_Mode" => 2,
// 								"Alert_Mode" => $alertMode,
// 								"Alertdata" => 
// 									[
// 										"mobileno" => substr(trim($data['customer_data']['mob_no']), -10),
// 										"emailId" => $data['customer_data']['email'],
// 										"AlertV1" => $AlertV1,
// 										"AlertV2" => $AlertV2,
// 										"AlertV3" => $AlertV3,
// 										"AlertV4" => $AlertV4,
// 										"AlertV5" => $AlertV5,
// 										"AlertV6" => $AlertV6,
// 										"AlertV7" => $AlertV7,
// 										"AlertV8" => $AlertV8,
// 										"AlertV9" => $AlertV9,
// 										"AlertV10" => $AlertV10,
// 										"AlertV11" => $AlertV11,
// 										"AlertV12" => $AlertV12,
// 										"AlertV13" => '',
										
// 									]
// 								]
// 							];
							
// 							$parameters = json_encode($parameters);
// 							$curl = curl_init();
						
// 							curl_setopt_array($curl, array(
// 							CURLOPT_URL => $click_url['click_pss_url'],
// 								CURLOPT_RETURNTRANSFER => true,
// 								CURLOPT_ENCODING => "",
// 								CURLOPT_MAXREDIRS => 10,
// 								CURLOPT_TIMEOUT => 30,
// 								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// 								CURLOPT_CUSTOMREQUEST => "POST",
// 								CURLOPT_POSTFIELDS => $parameters,
// 								CURLOPT_HTTPHEADER => array(
// 									"cache-control: no-cache",
// 									"content-type: application/json",
							   
// 								),
// 							));

// 							$response = curl_exec($curl);
						
// 							curl_close($curl);
						
// 							$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) , "type"=>"sms_logs_coi"];
// 							$this->db->insert("logs_docs",$request_arr);
					
						
// 						}
						
// 					}

// 				}else{

// 				  $return_data = array(
// 					'status'=>'error',
// 					"msg" => $errorObj['ErrorMessage']
// 				  );
// 				}
// 				return $return_data;
				 
// 			}
		
// 		}else{
			
// 			return $return_data = array(
// 				'status'=>'error',
// 				"msg" => "Quote error"
// 			  );
// 		}
// 	}