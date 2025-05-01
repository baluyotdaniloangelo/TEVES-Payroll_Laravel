			<!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
					<div id="sidebar-menu" class="sidebar-menu">
						<nav class="greedys sidebar-horizantal">
							<ul class="list-inline-item list-unstyled links">
								<li class="menu-title"> 
									<span>Main</span>
								</li>
								
								<li class="menu-title"> 
									<span>Employees</span>
								</li>
								<li class="submenu">
									<a href="#" class="noti-dot"><i class="la la-user"></i> <span> Employees</span> <span class="menu-arrow"></span></a>
									<ul>
										<li><a href="{{ route('employee') }}">Employees List</a></li>
										<li><a href="{{ route('employee_attendance_logs') }}">Attendance Logs</a></li>
									</ul>
								</li>
								<li class="menu-title"> 
									<span>Maintenance</span>
								</li>
								<li class="submenu">
									<a href="#" class="noti-dot"><i class="la la-user"></i> <span> Maintenance</span> <span class="menu-arrow"></span></a>
									<ul>
										
										<li><a href="{{ route('holiday') }}">Holidays</a></li>
										<li><a href="{{ route('branch') }}">Branch</a></li>
									</ul>
								</li>
								<li class="menu-title"> 
									<span>HR</span>
								</li>
								
							</ul>
							<button class="viewmoremenu">More Menu</button>
							<ul class="hidden-links hidden">
								<li class="submenu">
									<a href="#"><i class="la la-money"></i> <span> Payroll </span> <span class="menu-arrow"></span></a>
									<ul>
										<li><a href="salary.html"> Employee Salary </a></li>
										<li><a href="salary-view.html"> Payslip </a></li>
										<li><a href="payroll-items.html"> Payroll Items </a></li>
									</ul>
								</li>
								
								<li class="submenu">
									<a href="#"><i class="la la-files-o"></i> <span> Accounting </span> <span class="menu-arrow"></span></a>
									<ul>
										<li><a href="categories.html">Categories</a></li>
										<li><a href="budgets.html">Budgets</a></li>
										<li><a href="budget-expenses.html">Budget Expenses</a></li>
										<li><a href="budget-revenues.html">Budget Revenues</a></li>
									</ul>
								</li>
								
								
								
								
							</ul>
						</nav>
						<ul class="sidebar-vertical">
							
							
							<!--<li class="menu-title"> 
								<span>Employees</span>
							</li>-->
							<li class="submenu">
								<a href="#" class="<?php if(@$active_link=='employee_attendance_logs' ||
								@$active_link=='employee_allowance_logs' ||
								@$active_link=='employee_deduction_logs'){ echo "noti-dot"; }?> "><i class="la la-user"></i> <span> Payroll Items</span> <span class="menu-arrow"></span></a>
								<ul>
									<li><a class="<?php if(@$active_link=='employee_attendance_logs'){ echo "active"; }?>" href="{{ route('employee_attendance_logs') }}">Attendance Logs</a></li>
									<li><a class="<?php if(@$active_link=='employee_deduction_logs'){ echo "active"; }?>" href="{{ route('employee_allowance_logs') }}">Allowance Logs</a></li>
									<li><a class="<?php if(@$active_link=='employee_deduction_logs'){ echo "active"; }?>" href="{{ route('employee_deduction_logs') }}">Deduction Logs</a></li>
									<li><a class="<?php if(@$active_link=='cut_off'){ echo "active"; }?>" href="{{ route('CutOff') }}" title="View Saved Payroll, on Every Cut Off, Review and Approved">Cut-Off</a></li>
									
								</ul>
							</li>
							<!--<li class="menu-title"> 
								<span>Maintenance</span>
							</li>-->
							<li class="submenu">
								<a href="#" class="<?php if(@$active_link=='employee'||@$active_link=='branch'||@$active_link=='holiday'||@$active_link=='deduction_type'){ echo "noti-dot"; }?> "><i class="la la-user"></i> <span> Maintenance</span> <span class="menu-arrow"></span></a>
								<ul>
								
									<li><a class="<?php if(@$active_link=='employee'){ echo "active"; }?>" href="{{ route('employee') }}">Employees List</a></li>
									<li><a class="<?php if(@$active_link=='branch'){ echo "active"; }?>" href="{{ route('branch') }}">Branch</a></li>
									<li><a class="<?php if(@$active_link=='user'){ echo "active"; }?>" href="{{ route('user') }}">User</a></li>
									<li><a class="<?php if(@$active_link=='holiday'){ echo "active"; }?>" href="{{ route('holiday') }}">Holidays</a></li>
									<li><a class="<?php if(@$active_link=='deduction_type'){ echo "active"; }?>" href="{{ route('deduction_type') }}">Deduction Type</a></li>
									
								</ul>
							</li>
							<!--<li class="menu-title"> 
								<span>HR</span>
							</li>-->
							
							<li class="submenu">
								<a href="#"><i class="la la-money"></i> <span> Payroll </span> <span class="menu-arrow"></span></a>
								<ul>
									<li><a href="{{ route('CreatePayroll') }}"> Create Payroll </a></li>
									<!--<li><a href="payroll-items.html"> Payroll Items </a></li>-->
								</ul>
							</li>
							<!--
							<li class="submenu">
								<a href="#"><i class="la la-files-o"></i> <span> Accounting </span> <span class="menu-arrow"></span></a>
								<ul>
									<li><a href="categories.html">Categories</a></li>
									<li><a href="budgets.html">Budgets</a></li>
									<li><a href="budget-expenses.html">Budget Expenses</a></li>
									<li><a href="budget-revenues.html">Budget Revenues</a></li>
								</ul>
							</li>
							-->
							
							<li class="submenu">
								<a href="#"><i class="la la-pie-chart"></i> <span> Reports </span> <span class="menu-arrow"></span></a>
								<ul>
									<li><a href="salary-view.html"> Payslip </a></li>
									<li><a href="payslip-reports.html"> Salary Report </a></li>
									<li><a href="attendance-reports.html"> Attendance Report </a></li>
								</ul>
							</li>
						
							
						</ul>
					</div>
                </div>
            </div>
			<!-- /Sidebar -->
