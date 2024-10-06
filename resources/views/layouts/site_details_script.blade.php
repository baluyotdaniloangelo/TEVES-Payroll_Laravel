
   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   
   <script src="{{asset('template/assets/vendor/chart.js/chart.min.js')}}"></script>
			<?php
			
				$total_gateway = $offline_data[0]->total_gateway;
			
				$online_gateway = $total_gateway - $offline_data[0]->offline_gateway;
				$offline_gateway = $total_gateway - ($online_gateway);
				
				$total_meter = $offline_data[0]->total_meter;
			
				$online_meter = $total_meter - $offline_data[0]->offline_meter;
				$offline_meter = $total_meter - ($online_meter);
				
			?>
   <script>
   
   //$(window).resize(function(){location.reload();});
   
                document.addEventListener("DOMContentLoaded", () => {
                  new Chart(document.querySelector('#gateway'), {
                    type: 'pie',
                    data: {
                      labels: [
                        'Online',
                        'Offline'
                      ],
                      datasets: [{
                        label: 'Gateway',
                        data: [<?=$online_gateway;?>, <?=$offline_gateway;?>],
                        backgroundColor: [
                          '#0A5C36',
                          '#A0153E'
                        ],
                        hoverOffset: 4
                      }] 
                    }
                  });
                });
				
				document.addEventListener("DOMContentLoaded", () => {
                  new Chart(document.querySelector('#meter'), {
                    type: 'pie',
                    data: {
                      labels: [
                        'Online',
                        'Offline'
                      ],
                      datasets: [{
                        label: 'Meter',
                        data: [<?=$online_meter;?>, <?=$offline_meter;?>],
                        backgroundColor: [
                          '#0A5C36',
                          '#A0153E'
                        ],
                        hoverOffset: 4
                      }]
                    }
                  });
                });
				
	/*Remember Site Details Tab*/   
	function remember_sitetab(tab) {
				
		$('.additional_page_options').empty('');
		
		var tab = tab;	
			if(tab == 'meter'){
				$('#EERoomfilter_gateway_div').hide();
					$('#EERoomfilter_meter_div').show();
					
					$('#site_option_per_tab').html('<div class="btn-group" role="group" aria-label="Basic outlined " id="meter_option">'+
						'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterModal" title="Add Meter" onclick="ResetFormAddMeter()"> Meter</button>'+
					'</div>');
								
			}else if(tab == 'gateway'){
				$('#EERoomfilter_gateway_div').show();
					$('#EERoomfilter_meter_div').hide();
					$('#site_option_per_tab').html('<div class="btn-group" role="group" aria-label="Basic outlined " id="gateway_option">'+
						'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateGatewayModal" title="Add Gaterway" onclick="ResetFormAddGateway()"> Gateway</button>'+
					'</div>');
					
			}else if(tab == 'building'){
				
			}else if(tab == 'meterlocation'){

					$('#EERoomfilter_gateway_div').hide();
					$('#EERoomfilter_meter_div').hide();
					
					$('#site_option_per_tab').html('<div class="btn-group" role="group" aria-label="Basic outlined " id="location_option">'+
						'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterLocationModal" title="Add EE Room or MC Room" onclick="ResetFormEEroom()"> EE/MC Room</button>'+
					'</div>');
						
			}else{
				/*default = status*/
					$('#site_option_per_tab').html('');
					$('#EERoomfilter_gateway_div').hide();
					$('#EERoomfilter_meter_div').hide();
			}
			  $.ajax({
				url: "/save_site_tab",
				type:"POST",
				data:{
				  tab:tab,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
	}	

	function downloadAsbuilt(){
		  	  
		var query = {
			 siteID:{{ $SiteData[0]->site_id }},
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_site_as_built_excel')}}?" + $.param(query)
		window.open(url);
	  
	}

    </script>
