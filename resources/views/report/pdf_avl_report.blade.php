<?php
  $no_avl = empty($VP)? "" : $VP->avl_no."/".date("F/Y", strtotime($VP->avl_date));
  $date_avl = empty($VP)? "" : date("d/F/Y", strtotime($VP->avl_date));
  $dir_name = "";
  $position = "";
  if (isset($VPB)){
    if ($VPB->company_head){
      $dir_name = $VPB->full_name;
      $position = $VPB->position;
    }
  }
  $address = "";
  $phone = "";  
  if (isset($VPG)){
    if ($VPG->primary_data){
      if($V->vendor_group=='foreign'){
        $str_adress = $VPG->address_1." ".$VPG->address_2." ".$VPG->address_3." ".$VPG->address_4." ".$VPG->address_5."<br/>";
        $str_adress = $str_adress." ".$city." ".$sub_district." ".$province." ".$VPG->postal_code."<br/>";
        $str_adress = $str_adress.$country;
      }else{
        $str_adress = $VPG->street." ".$VPG->house_number." ".$VPG->building_name." ".$VPG->kavling_floor_number." RT.".$VPG->rt." / RW.".$VPG->rw."<br/>";
        $str_adress = $str_adress." ".$VPG->village." ".$sub_district." ".$city." ".$province." ".$VPG->postal_code."<br/>";
        $str_adress = $str_adress.$country;
      }
      $phone = $VPG->phone_number;
    }
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  

<style>
        .tbl1 tr td{
            border:1px solid #000000;
            padding: 5px;
        } 
        .tbl1 thead th {
            border:1px solid #000000;
            background-color: #CCCCCC;
        } 
        body{
          font-family: Arial;
          font-size: 10px;
          font-style: normal;
        }
        .bold{
          font-weight:800;
        }
        .note{
          font-style: italic;
        }

        .tbl-detail tr td{
          padding: 2px 0 0 0;
        }

</style>

</head>
<body>
<h3>
<table width="100%" cellpadding="0" cellspacing="0" >    
     <tr><td align="center">APPROVED VENDOR LETTER<br/>(AVL)</td></tr>     
</table>
</h3>
  <div>
  <span><span class="bold">AVL No : </span>{{$no_avl}}</span><span style="float:right">
  <span class="bold">AVL Date : </span> {{$date_avl}}</span>
  </div>
  <br/>
  <table class="tbl-detail" width="100%" cellpadding="0" cellspacing="0" >    
    <tr><td colspan="3">The following is your company information:</td></tr>
    <tr><td width="200px" class="bold" valign="top">E-Procurement Vendor Number</td><td width="10px" valign="top">:</td><td>{{$V->vendor_code}}</td></tr>
    <tr><td width="200px" class="bold" valign="top">Company Name</td><td width="10px" valign="top">:</td><td>{{empty($VPG)? "" : $VPG->company_name}}</td></tr>
    <tr><td width="200px" class="bold" valign="top">Company Type</td><td width="10px" valign="top">:</td><td>{{$company_type}}</td></tr>
    <tr><td width="200px" class="bold" valign="top">Board of Director Name</td><td width="10px" valign="top">:</td><td>{{$dir_name}}</td></tr>
    <tr><td width="200px" class="bold" valign="top">Position</td><td width="10px" valign="top">:</td><td>{{$position}}</td></tr>
    <tr><td width="200px" class="bold" valign="top">Address</td><td valign="top" valign="top">:</td><td>{!!$str_adress!!}</td></tr>
    <tr><td width="200px" class="bold" valign="top">Phone Number</td><td width="10px" valign="top">:</td><td>{{$phone}}</td></tr>
    </table>
  <br/>
  <div>
    <p><b>Please to be advised that your company is a vendor's {{$VPO->org_code}} - {{$VPO->description}}. 
Your account has been included in the supplier database of PT Timas Suplindo. You are qualifed to sell in the following categories:</b></p>
        
  </div>
  <table class="tbl1" width="100%" cellpadding="0" cellspacing="0"  border="0">      
      <thead>
        <tr><th width="10%">No</th><th>Scope of Supply</th></tr>        
      </thead>
      <tbody>
      @if (isset($VPC))
        @foreach ($VPC as $key=>$val)
          <tr><td>{{$key+1}}</td><td>{{$val->classification}} {{$val->detail_competency}}</td></tr>
        @endforeach
      @endif
      </tbody>
  </table>
  <p>The AVL as a proof document that your company is an "ACCREDITED VENDOR". Please notice to comply with standard policy of PT Timas Suplindo.</p>
<br>&nbsp;<br>
<table width="100%"><tr><td width="60%">&nbsp;</td><td align="center">
    PT Timas Suplindo    
    <br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</br>
    ==========================================        
</td></tr></table>

<div class="note">
Notes: <br/>
<div><span style="padding-right:2%">1.</span>This AVL will not apply if: </div>
<div style="padding-left:3%"><span style="padding-right:2%">1.a</span>The company is subject to sanctions or legal problems according to the applicable laws and regulations.</div>
<div style="padding-left:6%">ketentuan dan perundang-undangan yang berlaku</div>
<div style="padding-left:3%"><b><span style="padding-right:2%">1.b</span>Administrative documents expire.</b></div>
<div><span style="padding-right:2%">2.</span>AVL can be used as one of the prerequisite for procurement process.
</div>
</body>

</html>