<!DOCTYPE html>
<html>
<head>
    <title>Laravel 7 Datatables Tutorial - ItSolutionStuff.com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Vendor CSS Files -->
  <link href="{{('NiceAdmin-pro/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{('NiceAdmin-pro/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{('NiceAdmin-pro/assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{('NiceAdmin-pro/assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{('NiceAdmin-pro/assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="{{('NiceAdmin-pro/assets/css/style.css')}}" rel="stylesheet">
  <!-- Custom styles for this page -->
  <link href="{{('datatables/dataTables.bootstrap4.css')}}" rel="stylesheet">
  
  <link href="{{('style/custom_style.css')}}" rel="stylesheet">
	
  <!-- Vendor JS Files -->
  <script src="{{('NiceAdmin-pro/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- --><script src="{{('NiceAdmin-pro/assets/vendor/tinymce/tinymce.min.js')}}"></script> 

  <!-- Template Main JS File -->
  <script src="{{('NiceAdmin-pro/assets/js/main.js')}}"></script>
   <!-- Bootstrap core JavaScript-->
   <script src="{{asset('/jquery/jquery-3.6.0.min.js')}}"></script>
	
	   <!-- Page level plugins -->
   <script src="{{asset('datatables/jquery.dataTables.js')}}"></script>
   <script src="{{asset('datatables/dataTables.bootstrap4.js')}}"></script>
</head>
<body>
    
<div class="container">
    <h1>Laravel 7 Datatables Tutorial <br/> HDTuto.com</h1>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
               <th>#</th>
													<th>Company No.</th>
													<th>Business Entity</th>
													<th>Site Code</th>
													<th>Site Name</th>																						
													<th>Cut Off</th>
													<th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
   
</body>
   
<script type="text/javascript">
  $(function () {
    
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.index') }}",
        columns: [
            {data: 'company_no'},           
			
				{data: 'business_entity'},
				{data: 'site_code'},
				{data: 'site_name'},
				{data: 'site_cut_off'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    
  });
</script>
</html>