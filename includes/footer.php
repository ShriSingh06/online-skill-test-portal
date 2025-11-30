</div> </body>
</html>
<?php
// Close the database connection at the end of the script execution
if (isset($conn)) {
    close_db_connection($conn);
}
?>