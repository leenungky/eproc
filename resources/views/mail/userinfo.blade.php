<html>
    <head>

    </head>
<body>
    <p>
        "Dear [vendor-company_name],

        We are pleased to advise that you are now a [registration_status] of vendor's [purchasing_org-description]. Herewith the login information :

        User ID : {{ $vendor->username }}
        Password : {{ $vendor->password }}

        Kindly [login_address_link] to change your own password and complete your Company Profile . If you have inquiries, please contact us at (+21) 7 632-3616 or email us at cpu.helpdesk@timas.co.id.


        Regards,
        E-Procurement [companycode-description]"		

        Dear Rekanan<br/><br/>
        
        Detail Informasi Rekanan :<br/><br/>
        
        Nama Rekanan : <b>{{ $vendor->vendor_name }}</b><br/>
        NPWP         : <b>{{ $vendor->npwp_tin_number }}</b><br/>
        
        permohonan anda untuk menjadi vendor sudah bisa diproses, silahkan lengkapi Profile Perusahaan Anda.<br/><br/>
        
        Detail login pengguna:<br/>
        Username : <b>{{ $vendor->username }}</b><br/>
        Password : <b>{{ $vendor->password }}</b><br/><br/>
        <br />
        Please akses login ke halaman {{ route('main') }}
        
        Best Regards,<br/><br/>
        
        Admin<br/>
    </p>
</body>
</html>