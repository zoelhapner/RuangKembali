<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:10px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,
td{
    border:1px solid #000;
    padding:4px;
    vertical-align:middle;
}

thead th{
    text-align:center;
    font-weight:normal;
}
tfoot th{
    text-align:center;
    font-weight:normal;
}
.text-center{
    text-align:center;
}

.text-end{
    text-align:right;
}

.group{
    background:#ddd;
    font-weight:bold;
}

</style>
</head>
<body>

@include('build.pdf-rekap')

</body>
</html>