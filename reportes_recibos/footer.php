            </div> <!-- End container-fluid -->
        </div> <!-- End content -->
    </div> <!-- End wrapper -->

    <!-- Scripts -->
    <!-- DataTables JS -->
    <script src="<?php echo $pathToRoot; ?>js/vendor/jquery.dataTables.min.js"></script>
    <script src="<?php echo $pathToRoot; ?>js/vendor/dataTables.bootstrap5.min.js"></script>
    <script src="<?php echo $pathToRoot; ?>js/vendor/dataTables.responsive.min.js"></script>
    <!-- Select2 JS -->
    <script src="<?php echo $pathToRoot; ?>js/vendor/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
</body>
</html>