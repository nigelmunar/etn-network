<?php
    declare(strict_types = 1);
?>
            <footer class="main-footer">
                <strong>Copyright &copy; 2022 - <?php echo date("Y"); ?> ETN - NETWORK, All rights reserved.</strong>
            </footer>
        </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
        <script src="<?php echo $siteURL; ?>plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="<?php echo $siteURL; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script src="<?php echo $siteURL; ?>plugins/toastr/toastr.min.js"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo $siteURL; ?>dist/js/adminlte.min.js"></script>
        <script src="<?php echo $siteURL; ?>dist/js/cookies.min.js"></script>
        
        <!-- <script src="<?php echo $siteURL; ?>admin/plugins/magnific-popup/jquery.magnific-popup.min.js"></script> -->
        <!-- <script type="text/javascript" src="/admin/assets/js/general.min.js?v=0.1"></script> -->

        <script>
            $(function() {
                <?php
                    if(isset($_SESSION['Movie Added']) && $_SESSION['Movie Added'] === 'true')
                    {
                        echo 'toastr.success(\'Movie saved successfully.\'); ' . "\n";

                        unset($_SESSION['Movie Added']);
                    }

                    if(isset($_SESSION['Movie Deleted']) && $_SESSION['Movie Deleted'] === 'true')
                    {
                        echo 'toastr.success(\'Movie deleted successfully.\'); ' . "\n";

                        unset($_SESSION['Movie Deleted']);
                    }
                ?>
            });
        </script>
        
        <!-- Page specific script -->
        <?php
			for($i = 0; $i < count($scripts); $i++)
			{
				echo '<script src="' . $scripts[$i][0] . '" ';

				for($j = 1; $j < count($scripts[$i]); $j++)
				{
					echo ' ' . $scripts[$i][$j];
				}

				echo '></script>';
			}
			
            include ROOT_PATH . 'application/html-includes/admin-bar.php';
		?>   
        
    </body>
</html>