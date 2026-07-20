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
    width: 100%;
    border-collapse:collapse;
    table-layout:fixed;
}

th,td{
    border:1px solid #000;
    padding:4px;
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

@include('build.pdf-kurvas')

</body>
</html>