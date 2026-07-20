<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: sans-serif;
    font-size:9px;
    color:#000;
}

h1{
    text-align:center;
    font-size:18px;
    margin-bottom:12px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,
td{
    border:0.6px solid #000;
    padding:3px 4px;
    vertical-align:middle;
}

td{
    white-space:nowrap;
}

thead th{
    background:#d9d9d9;
    text-align:center;
}

.text-center{
    text-align:center;
}

.text-right{
    text-align:right;
}

.category{
    background:#eeeeee;
    font-weight:bold;
}

.uraian{
    background:#f8f8f8;
}

.small{
    font-size:8px;
}

</style>
</head>
<body>

@include('build.pdf-detail')

</body>
</html>