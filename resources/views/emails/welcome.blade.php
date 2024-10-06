<!DOCTYPE html>
<html>
<body>
    <!--<p style="background-color:green;color:white; text-align:center; padding:5px;">{{ $title }}</p>-->
	<p>Hi {{ $name }},</p>
	<p>{{ $body }}</p>
	
	<div align='left'>
			<table width='70%' style="color:black;">
			<tr>
				<td width="10%">Username:</td>
				<td>{{ $user_name }}</td>
			</tr>
			<tr>
				<td width="10%">Password:</td>
				<td>{{ $user_new_password }}</td>
			</tr>
			</table>
	</div>
	
	<p>Thank You!</p>
</body>
</html>