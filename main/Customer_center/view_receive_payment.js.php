<script>
    $(document).ready(function() {
        $('#reference_number_group').show();
        $('.icon-option').click(function() {
            $('.icon-option').removeClass('selected');
            $(this).addClass('selected');
            var value = $(this).data('value');
            if (value === 'cash') {
                $('#reference_number_group').show();
                $('#check_number_group').hide();
                $('#checkNo').removeAttr('name'); // Remove name attribute from check number input
                $('#RefNo').attr('name', 'RefNo'); // Set name attribute for reference number input
            } else {
                $('#reference_number_group').hide();
                $('#check_number_group').show();
                $('#RefNo').removeAttr('name'); // Remove name attribute from reference number input
                $('#checkNo').attr('name', 'RefNo'); // Set name attribute for check number input
            }
            $('#paymentType').val(value); // Set the paymentType value
        });
    });
</script>