<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name', 'E-Procurement')}} {{!empty($title) ? ' -'.$title : ''}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page{
            margin-top: 100px; margin-bottom: 100px;
            margin-left: 10mm; margin-right: 10mm;
        }
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 10pt;
            font-style: normal;
        }

        .page-title{
            text-transform: uppercase;
            text-align: center;
            font-size: 12pt;
        }
        .no-space{
            border-spacing: 0px;
        }

        .tbl1 thead th {
            border: 1px solid #000000;
            background-color: #CCCCCC;
        }
        .tbl1 tr td {
            border: 1px solid #000000;
            padding: 5px;
        }
        .note {
            font-style: normal;
        }
        .tbl-detail tr td {
            padding: 2px 0 0 0;
        }
        .bold{
            font-weight: bold;
        }

        .page-break {page-break-after: always;}
        header {
            position: fixed; top: 0px; left: 0px; right: 0px; height: 150px;overflow: hidden;
            top: -60px;
        }
        footer {
            position: fixed; bottom: 0px; left: 0px; right: 0px; height: 50px;
            bottom: -60px;
        }
        /* main {margin-top: 150px;} */

        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 10pt;
                font-style: normal;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Header -->
    <header>
        @yield('header')
    </header>
    <!-- Footer -->
    <footer>
        @yield('footer')
    </footer>

    <!-- Body -->
    <main>
        @yield('content')
    </main>


</body>
</html>
