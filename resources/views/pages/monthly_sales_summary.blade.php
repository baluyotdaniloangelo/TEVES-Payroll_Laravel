@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	

		
<section class="section">
      <div class="row">

        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"> {{ $title }} </h5>
				<div class="d-flex justify-content-end" id="user_option">
				<div class="btn-group" role="group" aria-label="Basic outlined example"style="margin-top: -50px; position: absolute;">
					    <select class="select_year form-select form-control" name="year" id='select_year'>
						
						<?php
						$current_year = date("Y");
						foreach(range(2021, $current_year) as $year){
							if($year==$current_year){
								echo "<option value='$year' selected>$year</option>";
							}else{
								echo "<option value='$year'>$year</option>";
							}
							
							//foreach(range(1, 12) as $month){
							//	echo date('Y.m.d', strtotime('Last day of ' . date('F', strtotime($year . '-' . $month . '-01')) . $year)) . PHP_EOL;
							//}
							
						}
												
						
						?>
						</select>
				  <button type="button" class="btn btn-success new_item bi bi-arrow-repeat" id="reloadMonthlyData" ></button>
				  </div>
				</div>

				 <div  style="max-height: 300px;">
					{!! $MonthlyChart->container() !!}
				</div>
					{!! $MonthlyChart->script() !!}
            </div>
          </div>
        </div>
                   
            </div>
          </div>
