	<!--Part 5-->
	<div class="row mb-2">
		<div class="col-sm-3">
				
		</div>
		<div class="col-sm-6">
		<form class="g-2 needs-validation" id="CASHONHAND_FORM">
				<table class="table" width="100%">
                <thead>
				  <tr>
                    <th scope="col" style="text-align: center !important;" colspan="3">Cash On Hand</th>
                  </tr>
                  <tr>
                    <th scope="col" style="text-align: center !important;">Deno</th>
                    <th scope="col" style="text-align: center !important;">Quantity</th>
                    <th scope="col" style="text-align: center !important;">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1000</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="one_thousand_deno" id="one_thousand_deno" required step="1" onchange="one_thousand_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="one_thousand_deno_total_amt">0.00</span></td>
                  </tr>
                  <tr>
                    <th scope="row">500</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="five_hundred_deno" id="five_hundred_deno" required step="1" onchange="five_hundred_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="five_hundred_deno_total_amt">0.00</span></td>
                  </tr>
                  <tr>
                    <th scope="row">200</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="two_hundred_deno" id="two_hundred_deno" required step="1" onchange="two_hundred_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="two_hundred_deno_total_amt">0.00</span></td>
                  </tr>
                 <tr>
                    <th scope="row">100</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="one_hundred_deno" id="one_hundred_deno" required step="1" onchange="one_hundred_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="one_hundred_deno_total_amt">0.00</span></td>
                  </tr>
                 <tr>
                    <th scope="row">50</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="fifty_deno" id="fifty_deno" required step="1" onchange="fifty_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="fifty_deno_total_amt">0.00</span></td>
                  </tr>
				  <tr>
                    <th scope="row">20</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="twenty_deno" id="twenty_deno" required step="1" onchange="twenty_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="twenty_deno_total_amt">0.00</span></td>
                  </tr>
				  <tr>
                    <th scope="row">10</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="ten_deno" id="ten_deno" required step="1" onchange="ten_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="ten_deno_total_amt">0.00</span></td>
                  </tr>
				  <tr>
                    <th scope="row">5</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="five_deno" id="five_deno" required step="1" onchange="five_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="five_deno_total_amt">0.00</span></td>
                  </tr>
				  <tr>
                    <th scope="row">1</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="one_deno" id="one_deno" required step="1" onchange="one_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="one_deno_total_amt">0.00</span></td>
                  </tr>
				  <tr>
                    <th scope="row">.25</th>
                    <td style="text-align: center !important;">
						<input type="number" class="" placeholder=""  name="twenty_five_cent_deno" id="twenty_five_cent_deno" required step="1" onchange="twenty_five_cent_deno_total()" >
					</td>
                    <td style="text-align: right !important;"><span id="twenty_five_cent_deno_total_amt">0.00</span></td>
                  </tr>
				  <tr>
                    <th scope="col" style="text-align: center !important;"></th>
                    <th scope="col" style="text-align: right !important;">Total Cash on hand:</th>
                    <th scope="col" style="text-align: right !important;"><span id="total_cash_on_hand_amt">0.00</span></th>
                  </tr>
				  <tr>
                    <th scope="col" style="text-align: left !important;">Cash Drop:</th>
                    <th scope="col" style="text-align: center !important;">
					<input type="number" class="" placeholder=""  name="cash_drop" id="cash_drop" required step="1">
					<span class="valid-feedback" id="cash_dropError"></span>
					</th>
					<th scope="col" style="text-align: left !important;"></th>
				  </tr>
                </tbody>
              </table>
			 </form> 
				<div class="text-center">				
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="CASHONHAND"> Submit</button>
						  </div>
		</div>
		<div class="col-sm-3">
				
		</div>
		</div>
		
