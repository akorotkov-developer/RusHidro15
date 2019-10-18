<html>
<head>
    <script
        src="https://code.jquery.com/jquery-3.4.1.js"
        integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
        crossorigin="anonymous"></script>
</head>
<body>
<?
echo "<pre>";
var_dump($_SERVER['HTTP_USER_AGENT']);
echo "</pre>";
?>

<script>
    $( document ).ready(function() {
        data = {useragent: navigator.userAgent};
        $.ajax({
            type: "POST",
            url: "/ajax/sled.php",
            data: data,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $("#sled").html(data);
            }
        });
    });
</script>


<div id="sled"></div>
</body>
</html>

