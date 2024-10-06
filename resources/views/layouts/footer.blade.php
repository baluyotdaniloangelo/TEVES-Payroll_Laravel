  <!-- ======= Footer ======= 
  <footer id="footer" class="footer">
    <div class="copyright">
      Dychitan Electronics Corporation
    </div>
	

  </footer>--><!-- End Footer -->

	<!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Select "Logout" below if you are ready to end your current session.
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
                    <a class="btn btn-danger" href="/logout"><i class="bi bi-arrow-right-circle navbar_icon"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

	<!-- Success Modal-->
    <div class="modal fade" id="SuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-check2-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body success_modal_bg" id="modal-body">
				&nbsp;
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
	<!-- Invalid Modal-->
    <div class="modal fade" id="InvalidModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Invalid Input!
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="InvalidModalBtn" ><i class="bi bi-x-circle navbar_icon"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{asset('template/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- --><script src="{{asset('template/assets/vendor/tinymce/tinymce.min.js')}}"></script> 

  <!-- Template Main JS File -->
  <script src="{{asset('template/assets/js/main.js')}}"></script>
   <!-- Bootstrap core JavaScript-->
   <script src="{{asset('/jquery/jquery-3.6.0.min.js')}}"></script>