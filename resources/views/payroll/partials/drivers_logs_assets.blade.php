<script>
    window.driverLogsConfig = {!! json_encode($driverLogsConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
</script>
<script src="{{ asset('Datatables/2.0.8/js/dataTables.js') }}"></script>
<script src="{{ asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js') }}"></script>
<script src="{{ asset('js/payroll/drivers-attendance-logs.js') }}"></script>
